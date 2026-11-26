<?php
/**
 * backend/rate_limiter.php — Shared server-side fixed-window rate limiter.
 *
 * Stores counters in per-bucket files under the system temp dir, guarded by
 * flock() so concurrent requests update atomically. Keyed by a hash of the
 * caller identity (IP + form bucket), so clearing cookies or opening a new
 * browser session does not reset the limit.
 *
 * DEPLOYMENT ASSUMPTIONS & FAILURE POLICY
 * ---------------------------------------
 * - Designed for a single application instance with a shared local
 *   filesystem (WAMP, typical shared hosting, single-container PaaS such as
 *   Wasmer Edge). If the app ever runs across multiple instances/containers
 *   without shared storage, replace this with a shared atomic store
 *   (Redis INCR+EXPIRE or a database) — file locks are not cross-host.
 * - Client identity uses REMOTE_ADDR only. X-Forwarded-For / Client-IP are
 *   deliberately ignored because they are client-spoofable unless a trusted
 *   proxy is configured (none is, today).
 * - Failure policy: FAIL OPEN. If the state directory cannot be created or
 *   a file cannot be locked, the request is allowed and the failure is
 *   logged. Rationale: a contact form should remain reachable during
 *   storage incidents; the CSRF token, honeypot and input validation still
 *   apply. Corrupted state files are treated as an expired window.
 * - Retention: bucket files are deleted opportunistically (~2% of requests)
 *   once they are older than twice the rate-limit window.
 *
 * Not web-accessible: this file is only ever included by backend handlers.
 */

if (!defined('PROJECT_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

/** Directory holding limiter state files (outside the web root). */
function rl_storage_dir(): string {
    $dir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hm_portfolio_rl';
    if (!is_dir($dir)) {
        @mkdir($dir, 0700, true);
    }
    // If the directory exists but is not writable, surface it for logging.
    if (is_dir($dir) && !is_writable($dir)) {
        error_log('[RateLimiter] storage directory not writable: ' . $dir);
    }
    return $dir;
}

/** Remove state files older than $ttl seconds (opportunistic cleanup). */
function rl_cleanup(string $dir, int $ttl): void {
    foreach ((array) glob($dir . DIRECTORY_SEPARATOR . '*.json') as $file) {
        if (filemtime($file) !== false && filemtime($file) < time() - $ttl) {
            @unlink($file);
        }
    }
}

/**
 * Record one hit against $bucket and report whether the caller is limited.
 *
 * @param string $bucket  Logical bucket, e.g. 'it_support:' . client_ip_hash()
 * @param int    $max     Maximum allowed hits per window
 * @param int    $window  Window length in seconds
 * @return int   0 when allowed; otherwise seconds until the window resets
 */
function rate_limit_hit(string $bucket, int $max, int $window): int {
    $dir  = rl_storage_dir();
    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $bucket) . '.json';

    $fp = @fopen($file, 'c+');
    if ($fp === false) {
        // Storage unavailable — fail open but leave a trace.
        error_log('[RateLimiter] cannot open state file: ' . $file);
        return 0;
    }

    if (!flock($fp, LOCK_EX)) {
        fclose($fp);
        return 0;
    }

    $raw  = stream_get_contents($fp);
    $data = $raw ? json_decode($raw, true) : null;
    $now  = time();

    if (!is_array($data) || !isset($data['start'], $data['count'])
        || ($now - (int) $data['start']) > $window) {
        $data = ['start' => $now, 'count' => 0];
    }

    if ((int) $data['count'] >= $max) {
        $retry_after = max(1, (int) $data['start'] + $window - $now);
        flock($fp, LOCK_UN);
        fclose($fp);
        return $retry_after;
    }

    $data['count']++;
    ftruncate($fp, 0);
    rewind($fp);
    fwrite($fp, json_encode($data));
    fflush($fp);
    flock($fp, LOCK_UN);
    fclose($fp);

    // Occasional garbage collection of expired buckets (~2% of requests).
    if (random_int(1, 50) === 1) {
        rl_cleanup($dir, $window * 2);
    }

    return 0;
}

/**
 * Pseudonymous client identity for rate limiting.
 * Uses REMOTE_ADDR only — X-Forwarded-For is client-spoofable and is ignored.
 */
function rl_client_key(string $form): string {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
    return $form . ':' . hash('sha256', $ip . '|' . ($_SERVER['SERVER_NAME'] ?? ''));
}
