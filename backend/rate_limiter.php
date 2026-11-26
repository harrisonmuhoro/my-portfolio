<?php
/**
 * backend/rate_limiter.php — Shared server-side fixed-window rate limiter.
 *
 * Stores counters in per-bucket files under the system temp dir, guarded by
 * flock() so concurrent requests update atomically. Keyed by a hash of the
 * caller identity (IP + form bucket), so clearing cookies or opening a new
 * browser session does not reset the limit.
 *
 * STORAGE & FAILURE POLICY
 * ------------------------
 * - State lives in RATE_LIMIT_DIR when configured. Otherwise the first
 *   writable of: a sibling directory next to DOCUMENT_ROOT (outside the
 *   web root on Apache-style hosts), PROJECT_ROOT/storage/rate_limits,
 *   then the system temp dir. Point RATE_LIMIT_DIR at a persistent
 *   directory outside the public web root in production — the in-project
 *   fallback relies on Apache access controls, and .htaccess/index.php
 *   guards are defense-in-depth only, NOT a universal protection
 *   mechanism (they do nothing on nginx or misconfigured Apache).
 * - Designed for a single application instance with a shared local
 *   filesystem (WAMP, typical shared hosting, single-container PaaS such as
 *   Wasmer Edge). If the app ever runs across multiple instances/containers
 *   without shared storage, replace this with a shared atomic store
 *   (Redis INCR+EXPIRE or a database) — file locks are not cross-host.
 * - Client identity uses REMOTE_ADDR only. X-Forwarded-For / Client-IP are
 *   deliberately ignored because they are client-spoofable unless a trusted
 *   proxy is configured (none is, today).
 * - Failure policy is configurable via RATE_LIMIT_FAIL_MODE:
 *     'open'   (default) — storage failures allow the request and are logged.
 *     'closed' — storage failures deny the request (HTTP 429) and are logged.
 *   Default is 'open' so a contact form stays reachable during storage
 *   incidents; CSRF, honeypot and input validation still apply either way.
 *   Corrupted state files are treated as an expired window.
 * - Retention: bucket files are deleted opportunistically (~2% of requests)
 *   once they are older than twice the rate-limit window.
 *
 * Not web-accessible: this file is only ever included by backend handlers.
 */

if (!defined('PROJECT_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Resolve a writable state directory, or '' when none is available.
 *
 * When RATE_LIMIT_DIR is explicitly configured it is authoritative — no
 * silent fallback, so a misconfigured path surfaces via the fail policy
 * instead of scattering state across locations. Otherwise prefer the
 * persistent in-project storage dir, then the system temp dir.
 */
function rl_storage_dir(): string {
    $candidates = [];
    if (defined('RATE_LIMIT_DIR') && trim((string) RATE_LIMIT_DIR) !== '') {
        $candidates[] = rtrim((string) RATE_LIMIT_DIR, '/\\');
    } else {
        // Preferred fallback: a directory beside DOCUMENT_ROOT — outside the
        // served tree on Apache-style hosts. Skipped when DOCUMENT_ROOT is
        // unset/unreliable (e.g. some PaaS runtimes).
        $docRoot = isset($_SERVER['DOCUMENT_ROOT']) ? trim((string) $_SERVER['DOCUMENT_ROOT']) : '';
        if ($docRoot !== '' && !in_array($docRoot, ['/', '\\'], true)) {
            $candidates[] = dirname(rtrim($docRoot, '/\\')) . DIRECTORY_SEPARATOR . 'hm_portfolio_rl';
        }
        $candidates[] = PROJECT_ROOT . DIRECTORY_SEPARATOR . 'storage'
                      . DIRECTORY_SEPARATOR . 'rate_limits';
        $candidates[] = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'hm_portfolio_rl';
    }

    foreach ($candidates as $dir) {
        if (!is_dir($dir) && !@mkdir($dir, 0700, true) && !is_dir($dir)) {
            error_log('[RateLimiter] cannot create storage directory: ' . $dir);
            continue;
        }
        if (!is_writable($dir)) {
            error_log('[RateLimiter] storage directory not writable: ' . $dir);
            continue;
        }
        rl_guard_web_dir($dir);
        return $dir;
    }

    error_log('[RateLimiter] no writable storage directory available');
    return '';
}

/** Drop Apache/PHP guard files when state lives inside the web root. */
function rl_guard_web_dir(string $dir): void {
    $root = str_replace(['\\', '//'], '/', PROJECT_ROOT);
    $path = str_replace(['\\', '//'], '/', $dir);
    if (!str_starts_with($path, $root)) {
        return;
    }
    $htaccess = $dir . DIRECTORY_SEPARATOR . '.htaccess';
    // Apache 2.4 syntax only — the legacy "Deny from all" line triggers a
    // 500 on hosts without mod_access_compat.
    $want = "Require all denied\n";
    if (!is_file($htaccess) || trim((string) @file_get_contents($htaccess)) !== trim($want)) {
        @file_put_contents($htaccess, $want);
    }
    $index = $dir . DIRECTORY_SEPARATOR . 'index.php';
    if (!is_file($index)) {
        @file_put_contents($index, "<?php http_response_code(403); exit;\n");
    }
}

/**
 * Apply the configured failure policy when storage is unusable.
 * Returns 0 (allow) under 'open', or a retry-after under 'closed'.
 */
function rl_storage_failure(int $window): int {
    $mode = defined('RATE_LIMIT_FAIL_MODE')
        ? strtolower(trim((string) RATE_LIMIT_FAIL_MODE)) : 'open';
    if ($mode === 'closed') {
        error_log('[RateLimiter] fail-closed policy: denying request');
        return max(1, $window);
    }
    return 0;
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
    $dir = rl_storage_dir();
    if ($dir === '') {
        return rl_storage_failure($window);
    }
    $file = $dir . DIRECTORY_SEPARATOR . hash('sha256', $bucket) . '.json';

    $fp = @fopen($file, 'c+');
    if ($fp === false) {
        // Storage unavailable — log, then apply the fail policy.
        error_log('[RateLimiter] cannot open state file: ' . $file);
        return rl_storage_failure($window);
    }

    if (!flock($fp, LOCK_EX)) {
        // Consistent with the fopen failure path: log, then fail policy.
        error_log('[RateLimiter] failed to lock state file: ' . $file);
        fclose($fp);
        return rl_storage_failure($window);
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
