<?php
/**
 * tests/run_tests.php — CLI test suite (no framework required).
 *
 * Usage:  php tests/run_tests.php
 * Exit code 0 = all passed, 1 = failures.
 *
 * Covers:
 *  - i18n dictionary integrity (direct inspection, no fallback masking)
 *  - shared rate limiter behaviour
 *  - mailer address sanitisation
 *  - full enquiry-handler workflow via tests/handler_probe.php
 *    (CSRF, honeypot, allowlists, dates, rate limit, mail provider)
 *
 * No real emails are sent: provider tests point RESEND_API_URL at a
 * local stub server started by this script.
 */

error_reporting(E_ALL);
define('PROJECT_ROOT', dirname(__DIR__));
// WAMP's configured session directory may be unavailable to the CLI user.
// Use the system temp directory consistently for the suite and probe workers.
ini_set('session.save_path', sys_get_temp_dir());

// The suite's unit tests exercise development-mode fallbacks. On CI there is
// no config.local.php and CLI has no HTTP_HOST, so ENVIRONMENT would resolve
// to 'production' (fail-closed rate limiter) and fail spuriously. Pin the
// suite process to development; handler probes set their own APP_ENV per spec.
putenv('APP_ENV=development');

require_once PROJECT_ROOT . '/config/config.php';
require_once PROJECT_ROOT . '/includes/i18n.php';
require_once PROJECT_ROOT . '/backend/rate_limiter.php';
require_once PROJECT_ROOT . '/backend/mailer.php';

$passed = 0;
$failed = 0;

function ok(bool $cond, string $label): void {
    global $passed, $failed;
    if ($cond) {
        $passed++;
        echo "  PASS  $label\n";
    } else {
        $failed++;
        echo "  FAIL  $label\n";
    }
}

// ── i18n dictionary (direct inspection — no t() fallback) ────
echo "== i18n dictionary ==\n";

$dict    = it_dictionary();
$en_keys = array_keys($dict['en'] ?? []);
$sw_keys = array_keys($dict['sw'] ?? []);

ok($en_keys === $sw_keys, 'en/sw key sets are identical'
    . ($en_keys === $sw_keys ? '' : ' — en-only: ' . implode(',', array_diff($en_keys, $sw_keys))
        . ' sw-only: ' . implode(',', array_diff($sw_keys, $en_keys))));

$empty = [];
foreach (['en', 'sw'] as $lang) {
    foreach (($dict[$lang] ?? []) as $k => $v) {
        if (!is_string($v) || trim($v) === '') { $empty[] = "$lang:$k"; }
    }
}
ok($empty === [], 'every dictionary entry is a non-empty string' . ($empty ? ' — empty: ' . implode(', ', $empty) : ''));

ok(t('hero_title', 'en') !== t('hero_title', 'sw'), 'en/sw hero_title differ (real translations)');
ok(t('nonexistent_key') === 'nonexistent_key', 'unknown key falls back to key name');
ok(t('hero_title', 'fr') === t('hero_title', 'en'), 'invalid language falls back to English');

// ── rate limiter ────────────────────────────────────────────
echo "== rate limiter ==\n";

if (DIRECTORY_SEPARATOR === '\\') {
    $driveProbe = rtrim(str_replace('\\', '/', sys_get_temp_dir()), '/') . '/rl_path_probe';
    $driveResolved = rl_resolve_path_symlinks($driveProbe);
    ok($driveResolved !== null && str_starts_with(strtolower($driveResolved), strtolower(str_replace('\\', '/', realpath(sys_get_temp_dir())) . '/')),
        'Windows drive-letter path resolves without duplicating its drive component');
}

$bucket = 'test:' . uniqid('', true);
ok(rate_limit_hit($bucket, 2, 2) === 0, 'first hit allowed');
ok(rate_limit_hit($bucket, 2, 2) === 0, 'second hit allowed');
$blocked = rate_limit_hit($bucket, 2, 2);
ok($blocked > 0 && $blocked <= 2, "third hit blocked with retry_after=$blocked");
ok(rate_limit_hit($bucket . ':other', 2, 2) === 0, 'separate bucket unaffected');

$bucket2 = 'test2:' . uniqid('', true);
rate_limit_hit($bucket2, 1, 1);
ok(rate_limit_hit($bucket2, 1, 1) > 0, 'limit enforced within window');
sleep(2);
ok(rate_limit_hit($bucket2, 1, 1) === 0, 'counter resets after window expires');

$rlDir = rl_storage_dir();
$rlDirNorm = str_replace('\\', '/', $rlDir);
ok($rlDir !== '' && is_dir($rlDir) && is_writable($rlDir),
    'rate-limit storage resolves to a writable directory');
// Guard files are only required when state lives inside the project root.
if (str_starts_with($rlDirNorm, str_replace('\\', '/', PROJECT_ROOT))) {
    ok(is_file($rlDir . DIRECTORY_SEPARATOR . '.htaccess'),
        'web guard .htaccess written for in-root storage dir');
} else {
    ok(!str_contains($rlDirNorm, '/storage/'),
        'preferred storage is outside the project web root');
}

// ── mailer helpers ──────────────────────────────────────────
echo "== mailer ==\n";

ok(mail_addr('Test "User"', 'a@b.com') === '"Test \\"User\\"" <a@b.com>', 'mail_addr quotes display name safely');
ok(mail_addr("Bad\r\nName", 'a@b.com') === '"Bad Name" <a@b.com>', 'mail_addr strips CRLF injection');
ok(mail_addr('', 'a@b.com') === '<a@b.com>', 'mail_addr handles empty name');

// ── handler integration probes ──────────────────────────────
echo "== handler probes ==\n";

/**
 * Stop the stub server and remove its log file.
 * proc_terminate() is unreliable for `php -S` on Windows, so verify the
 * process actually exited and fall back to taskkill there.
 */
function stop_stub($server, string $nullFile): void {
    static $stopped = [];
    if (is_resource($server)) {
        $resourceId = get_resource_id($server);
        if (isset($stopped[$resourceId])) {
            @unlink($nullFile);
            return;
        }
        $pid = null;
        $st  = proc_get_status($server);
        if (is_array($st) && isset($st['pid'])) { $pid = (int) $st['pid']; }
        if (PHP_OS_FAMILY === 'Windows') {
            // proc_open wraps commands in cmd.exe on Windows. proc_terminate
            // kills only that wrapper and orphans the php -S child BEFORE a
            // tree-kill can see it — so on Windows go straight to taskkill /T.
            if ($pid) {
                pclose(popen('taskkill /PID ' . $pid . ' /F /T >NUL 2>&1', 'r'));
            }
            usleep(200000);
            // taskkill terminates the server process tree. proc_close() can
            // block indefinitely on the Windows process handle after that.
        } else {
            proc_terminate($server);
            usleep(200000);
            @proc_close($server);
        }
        $stopped[$resourceId] = true;
    }
    @unlink($nullFile);
}

/**
 * Run one simulated POST through the real handler in a subprocess.
 * Returns ['json' => array|null, 'http' => int, 'raw' => string].
 */
function probe(array $spec): array {
    $file = tempnam(sys_get_temp_dir(), 'probe_') . '.json';
    file_put_contents($file, json_encode($spec));
    $process = proc_open([PHP_BINARY, '-d', 'xdebug.mode=off', PROJECT_ROOT . '/tests/handler_probe.php', $file],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    if (is_resource($process)) {
        fclose($pipes[0]);
        $out = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        if ($stderr !== '') { error_log(trim($stderr)); }
    } else {
        $out = '';
    }
    @unlink($file);

    $http = 200;
    if (preg_match('/__HTTP__:(\d+)/', $out, $m)) {
        $http = (int) $m[1];
        $out  = substr($out, 0, strpos($out, '__HTTP__:'));
    }
    $json = json_decode(trim($out), true);
    return ['json' => is_array($json) ? $json : null, 'http' => $http, 'raw' => $out];
}

/** Remove the PHP session file created by a persistent-session probe. */
function cleanup_probe_session(string $id): void {
    $savePath = sys_get_temp_dir();
    $savePath = $savePath !== '' ? $savePath : sys_get_temp_dir();
    @unlink(rtrim($savePath, '/\\') . DIRECTORY_SEPARATOR . 'sess_' . $id);
}

/**
 * Render a real page through tests/page_probe.php and return its HTML/XML.
 */
function page_probe(array $spec): string {
    $file = tempnam(sys_get_temp_dir(), 'page_') . '.json';
    file_put_contents($file, json_encode($spec));
    $process = proc_open([PHP_BINARY, '-d', 'xdebug.mode=off', PROJECT_ROOT . '/tests/page_probe.php', $file],
        [0 => ['pipe', 'r'], 1 => ['pipe', 'w'], 2 => ['pipe', 'w']], $pipes);
    $out = '';
    if (is_resource($process)) {
        fclose($pipes[0]);
        $out = (string) stream_get_contents($pipes[1]);
        $stderr = (string) stream_get_contents($pipes[2]);
        fclose($pipes[1]);
        fclose($pipes[2]);
        proc_close($process);
        if ($stderr !== '') { error_log(trim($stderr)); }
    }
    @unlink($file);
    return $out;
}

$basePost = [
    'name'        => 'Test User',
    'phone'       => '+254726300091',
    'category'    => 'network',
    'method'      => 'remote',
    'description' => 'This is a valid description for testing purposes.',
    'language'    => 'en',
];
$csrfSession = ['csrf_token' => 'test-token-123'];

// TEST-NET-2 range. The third octet is randomised per run so rate-limit
// buckets from previous runs (1h retention) can never collide with this
// run's probes; each probe still gets a distinct IP within the run.
$nextIp = function () {
    static $n = 0;
    return '198.51.' . random_int(1, 250) . '.' . (++$n);
};

$mkSpec = function (array $post, array $session = [], array $env = []) use ($basePost, $csrfSession, $nextIp) {
    return [
        'post'    => array_merge($basePost, $post),
        'session' => array_merge($csrfSession, $session),
        'ip'      => $nextIp(),
        'env'     => $env,
    ];
};

// Env pointing the mailer at a guaranteed-dead endpoint — deterministic
// provider failure without any external network call.
$deadMailEnv = [
    'RESEND_API_KEY' => 'test-key',
    'RESEND_API_URL' => 'http://127.0.0.1:1/emails',
];

// CSRF rejection
$r = probe($mkSpec(['csrf_token' => 'wrong-token']));
ok($r['http'] === 403 && ($r['json']['success'] ?? true) === false, 'invalid CSRF rejected with 403');

// Valid CSRF passes the CSRF gate (provider then fails on dead endpoint —
// still proves the gate let the request through to the mail stage)
$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $deadMailEnv));
ok($r['json'] !== null && ($r['json']['success'] ?? true) === false, 'valid CSRF reaches mail stage (provider failure)');

// Honeypot silently succeeds without touching validation or rate limit
$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'website' => 'http://spam.example']));
ok(($r['json']['success'] ?? false) === true, 'honeypot submission silently succeeds');

// Allowlist enforcement
$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'category' => 'hacked'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && str_contains($r['json']['message'] ?? '', 'service category'), 'invalid category rejected');

$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'method' => 'teleport'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && str_contains($r['json']['message'] ?? '', 'support method'), 'invalid method rejected');

// On-site requires location
$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'method' => 'onsite', 'location' => ''], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && str_contains($r['json']['message'] ?? '', 'location'), 'missing on-site location rejected');

// Language value is allowlisted — bogus code falls back, not trusted
$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'language' => 'xx'], [], $deadMailEnv));
ok($r['json'] !== null, 'bogus language code handled safely (JSON response)');

// Date validation
$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'preferred_date' => '2020-01-01'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && str_contains($r['json']['message'] ?? '', 'date'), 'past date rejected with error');

$r = probe($mkSpec(['csrf_token' => 'test-token-123', 'preferred_date' => 'not-a-date'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && str_contains($r['json']['message'] ?? '', 'date'), 'malformed date rejected with error');

// ── Mail provider paths (stubbed — no real email) ───────────
echo "== mail provider ==\n";

// Allocate a free port dynamically — never a hardcoded one.
$tmpSock = stream_socket_server('tcp://127.0.0.1:0', $errno, $errstr);
if ($tmpSock === false) {
    echo "FAIL cannot allocate a local port: $errstr\n";
    exit(1);
}
$stubPort = (int) substr(strrchr(stream_socket_get_name($tmpSock, false), ':'), 1);
fclose($tmpSock);

$nullFile = tempnam(sys_get_temp_dir(), 'stub_null_');
$server = proc_open(
    [PHP_BINARY, '-d', 'xdebug.mode=off', '-S', '127.0.0.1:' . $stubPort, PROJECT_ROOT . '/tests/resend_stub.php'],
    [0 => ['pipe', 'r'], 1 => ['file', $nullFile, 'a'], 2 => ['file', $nullFile, 'a']],
    $pipes
);

// Poll the stub until it actually answers (bounded at ~5s) — no fixed sleep.
$stubReady = false;
for ($i = 0; $i < 50 && !$stubReady; $i++) {
    $stubReady = @file_get_contents('http://127.0.0.1:' . $stubPort . '/emails') !== false;
    if (!$stubReady) { usleep(100000); }
}
if (!$stubReady) {
    stop_stub($server, $nullFile);
    echo "FAIL Resend stub did not become ready on 127.0.0.1:$stubPort\n";
    exit(1);
}
// Guarantee cleanup even if the script exits early.
register_shutdown_function(function () use ($server, $nullFile) {
    stop_stub($server, $nullFile);
});

$providerEnv = [
    'RESEND_API_KEY' => 'test-key',
    'RESEND_API_URL' => 'http://127.0.0.1:' . $stubPort . '/emails',
];

// Success path: stub returns 200
$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $providerEnv));
ok(($r['json']['success'] ?? false) === true, 'provider 200 → success:true');

// Failure path: unreachable endpoint → generic user-facing error + 503
$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && $r['http'] === 503, 'provider unreachable → success:false + HTTP 503');

// ── Contact handler probes ──────────────────────────────────
echo "== contact handler probes ==\n";

$contactPost = [
    'name'         => 'Test User',
    'email'        => 'test@example.com',
    'phone'        => '+254726300091',
    'project_type' => 'website',
    'subject'      => 'Test enquiry subject',
    'message'      => 'This is a valid message for the contact form handler tests.',
];
$mkContact = function (array $post, array $session = [], array $env = [], string $method = 'POST') use ($contactPost, $csrfSession, $nextIp) {
    return [
        'handler' => 'contact_handler.php',
        'method'  => $method,
        'post'    => array_merge($contactPost, $post),
        'session' => array_merge($csrfSession, $session),
        'ip'      => $nextIp(),
        'env'     => $env,
    ];
};

// Non-POST rejected
$r = probe($mkContact([], [], [], 'GET'));
ok($r['http'] === 405 && ($r['json']['success'] ?? true) === false, 'contact GET → 405 Method Not Allowed');

// CSRF rejection
$r = probe($mkContact(['csrf_token' => 'bad-token']));
ok($r['http'] === 403 && ($r['json']['success'] ?? true) === false, 'contact invalid CSRF → 403');

// Honeypot silently succeeds
$r = probe($mkContact(['csrf_token' => 'test-token-123', 'website' => 'http://spam.example']));
ok(($r['json']['success'] ?? false) === true, 'contact honeypot silently succeeds');

// Required-field validation
$r = probe($mkContact(['csrf_token' => 'test-token-123', 'email' => 'not-an-email', 'project_type' => 'hacked', 'subject' => 'x', 'message' => 'short'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && $r['http'] === 422, 'contact invalid fields rejected with 422');

// Valid submission reaches provider — success path
$r = probe($mkContact(['csrf_token' => 'test-token-123'], [], $providerEnv));
ok(($r['json']['success'] ?? false) === true && $r['http'] === 200, 'contact valid submission + provider 200 → success');

// Provider failure → 503
$r = probe($mkContact(['csrf_token' => 'test-token-123'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false && $r['http'] === 503, 'contact provider failure → success:false + 503');

// Contact rate limit: FORM_RATE_LIMIT submissions then 429
$contactRlIp = $nextIp();
$codes = [];
for ($i = 0; $i < FORM_RATE_LIMIT + 1; $i++) {
    $r = probe([
        'handler' => 'contact_handler.php',
        'post'    => array_merge($contactPost, ['csrf_token' => 'test-token-123']),
        'session' => $csrfSession,
        'ip'      => $contactRlIp,
        'env'     => $providerEnv,
    ]);
    $codes[] = $r['http'];
}
ok(in_array(429, $codes, true) && end($codes) === 429, 'contact rate limit → 429 after ' . FORM_RATE_LIMIT . ' (got: ' . implode(',', $codes) . ')');

// ── Rate limit enforcement through the IT support handler ───
echo "== handler rate limit ==\n";

$rlIp = $nextIp(); // fresh IP per run — never reuse a saturated bucket
$codes = [];
for ($i = 0; $i < FORM_RATE_LIMIT + 1; $i++) {
    $r = probe([
        'post'    => array_merge($basePost, ['csrf_token' => 'test-token-123']),
        'session' => $csrfSession,
        'ip'      => $rlIp,
        'env'     => $providerEnv,
    ]);
    $codes[] = $r['http'];
}
ok(in_array(429, $codes, true), 'rate limit returns 429 after ' . FORM_RATE_LIMIT . ' submissions (got: ' . implode(',', $codes) . ')');
ok(end($codes) === 429, 'the (limit+1)th request is the blocked one');

// ── CSRF rotation: repeat submission through one persisted PHP session ──
echo "== CSRF token rotation ==\n";

// A stable session ID models a browser session cookie across independent
// handler processes. The returned token must be in PHP's persisted session.
$itSessionId = 'itrotate' . bin2hex(random_bytes(8));
$itFirst = $mkSpec(['csrf_token' => 'test-token-123'], [], $providerEnv);
$itFirst['session_id'] = $itSessionId;
$r1 = probe($itFirst);
$newToken = $r1['json']['csrf_token'] ?? null;
ok(($r1['json']['success'] ?? false) === true
    && is_string($newToken) && $newToken !== '' && $newToken !== 'test-token-123',
    'IT support success returns a rotated csrf_token');
if (is_string($newToken) && $newToken !== '') {
    $itSecond = $mkSpec(['csrf_token' => $newToken], [], $providerEnv);
    $itSecond['session_id'] = $itSessionId;
    $r2 = probe($itSecond);
    $tokenB = $r2['json']['csrf_token'] ?? null;
    ok(($r2['json']['success'] ?? false) === true && $r2['http'] === 200,
        'IT support rotated token succeeds in the same persisted PHP session');
    ok(is_string($tokenB) && $tokenB !== '' && $tokenB !== $newToken,
        'second IT support success rotates the token again');

    // Token A must be dead now that token B replaced it in the session.
    $itStale = $mkSpec(['csrf_token' => $newToken], [], $providerEnv);
    $itStale['session_id'] = $itSessionId;
    $r3 = probe($itStale);
    ok($r3['http'] === 403, 'superseded IT support token rejected after second rotation');

    // The original token stays rejected too.
    $itOld = $mkSpec(['csrf_token' => 'test-token-123'], [], $providerEnv);
    $itOld['session_id'] = $itSessionId;
    $r4 = probe($itOld);
    ok($r4['http'] === 403, 'original IT support token still rejected');

    // Token B is the live one — next submission succeeds.
    if (is_string($tokenB) && $tokenB !== '') {
        $itThird = $mkSpec(['csrf_token' => $tokenB], [], $providerEnv);
        $itThird['session_id'] = $itSessionId;
        $r5 = probe($itThird);
        ok(($r5['json']['success'] ?? false) === true && $r5['http'] === 200,
            'newest IT support token accepted in the same session');
    }
}

$contactSessionId = 'contactrotate' . bin2hex(random_bytes(8));
$contactFirst = $mkContact(['csrf_token' => 'test-token-123'], [], $providerEnv);
$contactFirst['session_id'] = $contactSessionId;
$c1 = probe($contactFirst);
$cNewToken = $c1['json']['csrf_token'] ?? null;
ok(($c1['json']['success'] ?? false) === true
    && is_string($cNewToken) && $cNewToken !== '' && $cNewToken !== 'test-token-123',
    'contact success returns a rotated csrf_token');
if (is_string($cNewToken) && $cNewToken !== '') {
    $contactSecond = $mkContact(['csrf_token' => $cNewToken], [], $providerEnv);
    $contactSecond['session_id'] = $contactSessionId;
    $c2 = probe($contactSecond);
    $cTokenB = $c2['json']['csrf_token'] ?? null;
    ok(($c2['json']['success'] ?? false) === true && $c2['http'] === 200,
        'contact rotated token succeeds in the same persisted PHP session');
    ok(is_string($cTokenB) && $cTokenB !== '' && $cTokenB !== $cNewToken,
        'second contact success rotates the token again');

    // Token A must be dead now that token B replaced it in the session.
    $contactStale = $mkContact(['csrf_token' => $cNewToken], [], $providerEnv);
    $contactStale['session_id'] = $contactSessionId;
    $c3 = probe($contactStale);
    ok($c3['http'] === 403, 'superseded contact token rejected after second rotation');

    // The original token stays rejected too.
    $contactOld = $mkContact(['csrf_token' => 'test-token-123'], [], $providerEnv);
    $contactOld['session_id'] = $contactSessionId;
    $c4 = probe($contactOld);
    ok($c4['http'] === 403, 'original contact token still rejected');

    // Token B is the live one — next submission succeeds.
    if (is_string($cTokenB) && $cTokenB !== '') {
        $contactThird = $mkContact(['csrf_token' => $cTokenB], [], $providerEnv);
        $contactThird['session_id'] = $contactSessionId;
        $c5 = probe($contactThird);
        ok(($c5['json']['success'] ?? false) === true && $c5['http'] === 200,
            'newest contact token accepted in the same session');
    }
}
cleanup_probe_session($itSessionId);
cleanup_probe_session($contactSessionId);
// ── Rate limiter failure policy ──────────────────────────────
echo "== rate limiter failure policy ==\n";

// Point RATE_LIMIT_DIR at an existing FILE so no storage candidate works.
$badDirFile = (string) tempnam(sys_get_temp_dir(), 'rl_bad_');
$deadEnv = [
    'RESEND_API_KEY' => 'test-key',
    'RESEND_API_URL' => 'http://127.0.0.1:1/emails',
];

$missingProdDir = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv);
$missingProdDir['host'] = 'portfolio.example';
$missingProdDir['env']['APP_ENV'] = 'production';
$r = probe($missingProdDir);
ok($r['http'] === 429, 'production with missing RATE_LIMIT_DIR defaults closed');

$invalidProdDir = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $badDirFile,
]);
$invalidProdDir['host'] = 'portfolio.example';
$r = probe($invalidProdDir);
ok($r['http'] === 429, 'production with invalid RATE_LIMIT_DIR denies requests');

$inRootProdDirPath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_public_' . bin2hex(random_bytes(6));
mkdir($inRootProdDirPath);
$inRootProdDir = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $inRootProdDirPath . DIRECTORY_SEPARATOR . 'rate_limits',
]);
$inRootProdDir['host'] = 'portfolio.example';
$inRootProdDir['document_root'] = $inRootProdDirPath;
$r = probe($inRootProdDir);
ok($r['http'] === 429, 'production storage resolving under DOCUMENT_ROOT is rejected');
foreach (glob($inRootProdDirPath . DIRECTORY_SEPARATOR . 'rate_limits' . DIRECTORY_SEPARATOR . '*') ?: [] as $file) { @unlink($file); }
@rmdir($inRootProdDirPath . DIRECTORY_SEPARATOR . 'rate_limits');
@rmdir($inRootProdDirPath);

// Public root cannot be established → fail closed even with a valid dir.
$noRootDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_noroot_' . bin2hex(random_bytes(6));
$noRootSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $noRootDir,
]);
$noRootSpec['host'] = 'portfolio.example';
// No document_root and no PUBLIC_ROOT → public root unresolvable.
$r = probe($noRootSpec);
ok($r['http'] === 429, 'production fails closed when public root cannot be established');

// DOCUMENT_ROOT set but unresolvable → same fail-closed outcome.
$badRootSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $noRootDir,
]);
$badRootSpec['host'] = 'portfolio.example';
$badRootSpec['document_root'] = $noRootDir . '_does_not_exist';
$r = probe($badRootSpec);
ok($r['http'] === 429, 'production fails closed on unresolvable DOCUMENT_ROOT');

// Positive case: real document root + storage outside it → allowed through.
$pubRoot = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_webroot_' . bin2hex(random_bytes(6));
$outsideDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_outside_' . bin2hex(random_bytes(6));
mkdir($pubRoot);
$okProdSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $outsideDir,
]);
$okProdSpec['host'] = 'portfolio.example';
$okProdSpec['document_root'] = $pubRoot;
$r = probe($okProdSpec);
ok($r['http'] === 503, 'production accepts storage verified outside the public root');
foreach ((array) glob($outsideDir . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($outsideDir);
@rmdir($pubRoot);

// PUBLIC_ROOT env override establishes the root when DOCUMENT_ROOT is absent.
$pubOverride = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_pubovr_' . bin2hex(random_bytes(6));
$outsideOvr = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_outovr_' . bin2hex(random_bytes(6));
mkdir($pubOverride);
$ovrSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'PUBLIC_ROOT' => $pubOverride,
    'RATE_LIMIT_DIR' => $outsideOvr,
]);
$ovrSpec['host'] = 'portfolio.example';
$r = probe($ovrSpec);
ok($r['http'] === 503, 'PUBLIC_ROOT override verifies storage outside the public root');
$insideOvr = $pubOverride . DIRECTORY_SEPARATOR . 'rl_inside';
$inOvrSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'PUBLIC_ROOT' => $pubOverride,
    'RATE_LIMIT_DIR' => $insideOvr,
]);
$inOvrSpec['host'] = 'portfolio.example';
$r = probe($inOvrSpec);
ok($r['http'] === 429, 'PUBLIC_ROOT override rejects storage inside the public root');
foreach ((array) glob($outsideOvr . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($outsideOvr);
foreach ((array) glob($insideOvr . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($insideOvr);
@rmdir($pubOverride);
@rmdir($noRootDir);

// Pre-existing directory outside the public root is accepted as-is.
$preWeb = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_prew_' . bin2hex(random_bytes(6));
$preDir = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_pred_' . bin2hex(random_bytes(6));
mkdir($preWeb);
mkdir($preDir);
$preSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production',
    'RATE_LIMIT_DIR' => $preDir,
]);
$preSpec['host'] = 'portfolio.example';
$preSpec['document_root'] = $preWeb;
$r = probe($preSpec);
ok($r['http'] === 503, 'production accepts a pre-existing directory outside the public root');
foreach ((array) glob($preDir . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($preDir);
@rmdir($preWeb);

// Symlink fixtures cover dangling and existing targets. A symlink-specific
// skip is reported only when this runtime cannot create a link at all.
$symWeb = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_symw_' . bin2hex(random_bytes(6));
$symOutside = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_symo_' . bin2hex(random_bytes(6));
$symBase = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_sym_' . bin2hex(random_bytes(6));
mkdir($symWeb);
mkdir($symOutside);
$symProbe = $symBase . '_probe';
if (@symlink($symOutside, $symProbe)) {
    @unlink($symProbe);
    $cases = [
        ['dangling link into public root', $symBase . '_in', $symWeb . DIRECTORY_SEPARATOR . 'not_created', true, false],
        ['dangling link outside public root', $symBase . '_out', $symOutside . DIRECTORY_SEPARATOR . 'not_created', false, false],
        ['relative dangling link outside public root', $symBase . '_relative_dangling', basename($symOutside) . DIRECTORY_SEPARATOR . 'relative_missing', false, true],
        ['link to existing external directory', $symBase . '_external', $symOutside, false, false],
        ['relative link to existing external directory', $symBase . '_relative_external', basename($symOutside), false, true],
        ['link to existing directory inside public root', $symBase . '_inside', $symWeb, true, false],
    ];
    foreach ($cases as [$label, $link, $target, $reject, $relative]) {
        $expectedTarget = $relative ? dirname($link) . DIRECTORY_SEPARATOR . $target : $target;
        if (!@symlink($target, $link)) {
            echo "  SKIP  $label (symlink creation unavailable)\n";
            continue;
        }
        $spec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
            'APP_ENV' => 'production', 'RATE_LIMIT_DIR' => $link . DIRECTORY_SEPARATOR . 'rate_limits',
        ]);
        $spec['host'] = 'portfolio.example';
        $spec['document_root'] = $symWeb;
        $r = probe($spec);
        ok($r['http'] === ($reject ? 429 : 503), 'production ' . ($reject ? 'rejects' : 'accepts') . ' ' . $label);
        if ($reject && str_contains($label, 'dangling link into')) {
            ok(!file_exists($expectedTarget), 'dangling in-root target is not created before rejection');
        } elseif ($reject && str_contains($label, 'inside public root')) {
            ok(!file_exists($expectedTarget . DIRECTORY_SEPARATOR . 'rate_limits'), 'existing in-root symlink target remains untouched');
        } elseif (str_contains($label, 'dangling link outside')) {
            ok(is_dir($expectedTarget . DIRECTORY_SEPARATOR . 'rate_limits'), 'verified dangling external target is created at its resolved location');
        }
        $createdStore = $expectedTarget . DIRECTORY_SEPARATOR . 'rate_limits';
        foreach ((array) glob($createdStore . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
        @rmdir($createdStore);
        if (str_contains($label, 'dangling link outside')) { @rmdir($expectedTarget); }
        @unlink($link);
    }
    $loopA = $symBase . '_loop_a';
    $loopB = $symBase . '_loop_b';
    if (@symlink($loopB, $loopA) && @symlink($loopA, $loopB)) {
        $loopSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
            'APP_ENV' => 'production', 'RATE_LIMIT_DIR' => $loopA . DIRECTORY_SEPARATOR . 'rate_limits',
        ]);
        $loopSpec['host'] = 'portfolio.example';
        $loopSpec['document_root'] = $symWeb;
        $r = probe($loopSpec);
        ok($r['http'] === 429 && !file_exists($loopA . DIRECTORY_SEPARATOR . 'rate_limits'), 'production fails closed on symlink loop before creating storage');
        @unlink($loopA);
        @unlink($loopB);
    } else {
        echo "  SKIP  symlink loop case (symlink creation unavailable)\n";
        @unlink($loopA);
        @unlink($loopB);
    }
} else {
    echo "  SKIP  symlink regression cases (symlink() unavailable on this platform)\n";
}
$ordinaryOutside = $symOutside . DIRECTORY_SEPARATOR . 'new' . DIRECTORY_SEPARATOR . 'rate_limits';
$ordinarySpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'APP_ENV' => 'production', 'RATE_LIMIT_DIR' => $ordinaryOutside,
]);
$ordinarySpec['host'] = 'portfolio.example';
$ordinarySpec['document_root'] = $symWeb;
$r = probe($ordinarySpec);
ok($r['http'] === 503, 'production accepts nonexistent ordinary storage outside the public root');
ok(is_dir($ordinaryOutside), 'ordinary external storage directory is actually created');
foreach ((array) glob($ordinaryOutside . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($ordinaryOutside);
@rmdir(dirname($ordinaryOutside));
@rmdir($symOutside);
@rmdir($symWeb);

$localFallback = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv);
$r = probe($localFallback);
ok($r['http'] === 503, 'development fallback operates and reaches mail stage');

$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'RATE_LIMIT_DIR'       => $badDirFile,
    'RATE_LIMIT_FAIL_MODE' => 'closed',
]));
ok($r['http'] === 429, 'fail-closed policy denies request when storage unusable');

$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
    'RATE_LIMIT_DIR'       => $badDirFile,
    'RATE_LIMIT_FAIL_MODE' => 'open',
]));
ok($r['http'] === 503, 'fail-open policy (default) still allows request when storage unusable');

@unlink($badDirFile);

// ── SEO: rendered-page metadata, structured data, hreflang ───
echo "== SEO ==\n";

$seoPages = [
    '/'         => 'home-redesign.php',
    '/about'    => 'about.php',
    '/services' => 'services.php',
    '/projects' => 'projects.php',
    '/contact'  => 'contact.php',
];
$seoHtml = [];
foreach ($seoPages as $uri => $page) {
    $seoHtml[$uri] = page_probe(['page' => $page, 'uri' => $uri, 'host' => 'www.harrisonmuhoro.me']);
}
$seoHtml['/it-support?lang=en'] = page_probe(['page' => 'it-support.php', 'uri' => '/it-support', 'get' => ['lang' => 'en'], 'host' => 'www.harrisonmuhoro.me']);
$seoHtml['/it-support?lang=sw'] = page_probe(['page' => 'it-support.php', 'uri' => '/it-support', 'get' => ['lang' => 'sw'], 'host' => 'www.harrisonmuhoro.me']);

$seoTitle = function (string $html): ?string {
    return preg_match('~<title>(.*?)</title>~s', $html, $m) ? trim(html_entity_decode($m[1])) : null;
};
$seoDesc = function (string $html): ?string {
    return preg_match('~<meta name="description" content="([^"]*)"~', $html, $m) ? html_entity_decode($m[1]) : null;
};
$seoCanon = function (string $html): ?string {
    return preg_match('~<link rel="canonical"\s+href="([^"]+)"~', $html, $m) ? html_entity_decode($m[1]) : null;
};
$seoLd = function (string $html): array {
    preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $html, $m);
    $out = [];
    foreach (($m[1] ?? []) as $block) {
        $decoded = json_decode(trim($block), true);
        if (is_array($decoded)) { $out[] = $decoded; }
    }
    return $out;
};
$seoFindType = function (array $nodes, string $type): array {
    return array_values(array_filter($nodes, fn($n) => ($n['@type'] ?? '') === $type));
};

$titles = array_map($seoTitle, $seoHtml);
$descs  = array_map($seoDesc, $seoHtml);
ok(count(array_unique(array_filter($titles))) === count($seoHtml) && !in_array(null, $titles, true),
    'every public page has a unique non-empty <title>');
ok(count(array_unique(array_filter($descs))) === count($seoHtml) && !in_array(null, $descs, true),
    'every public page has a unique non-empty meta description');

// Canonicals: plain routes point at the clean production URL.
$canonOk = true;
foreach ($seoPages as $uri => $page) {
    $expected = 'https://www.harrisonmuhoro.me' . $uri;
    if (($seoCanon($seoHtml[$uri]) ?? '') !== $expected) { $canonOk = false; }
}
ok($canonOk, 'non-i18n pages canonicalize to https://www.harrisonmuhoro.me/<path>');

// Language canonicals + reciprocal hreflang.
ok(($seoCanon($seoHtml['/it-support?lang=en']) ?? '') === 'https://www.harrisonmuhoro.me/it-support?lang=en',
    'English IT Support canonical is ?lang=en');
ok(($seoCanon($seoHtml['/it-support?lang=sw']) ?? '') === 'https://www.harrisonmuhoro.me/it-support?lang=sw',
    'Kiswahili IT Support canonical is ?lang=sw');
foreach (['en', 'sw', 'x-default'] as $hl) {
    ok(str_contains($seoHtml['/it-support?lang=sw'], 'rel="alternate" hreflang="' . $hl . '"'),
        "Kiswahili page emits hreflang=\"$hl\"");
}
ok($seoTitle($seoHtml['/it-support?lang=en']) !== $seoTitle($seoHtml['/it-support?lang=sw']),
    'EN and SW IT Support pages render different titles');

// Language isolation: an explicit ?lang= must override any stale session
// preference; a missing ?lang= falls back to the session; invalid codes
// are ignored entirely.
$itProbe = function (?string $langGet, ?string $sessionLang): string {
    $spec = ['page' => 'it-support.php', 'uri' => '/it-support', 'host' => 'www.harrisonmuhoro.me'];
    if ($langGet !== null)    { $spec['get']     = ['lang' => $langGet]; }
    if ($sessionLang !== null){ $spec['session'] = ['it_support_lang' => $sessionLang]; }
    return page_probe($spec);
};
$itEnFromSw   = $itProbe('en', 'sw');   // session=sw, request ?lang=en
$itSwFromEn   = $itProbe('sw', 'en');   // session=en, request ?lang=sw
$itEnFresh    = $itProbe('en', null);   // no session, ?lang=en
$itSwFresh    = $itProbe('sw', null);   // no session, ?lang=sw
$itSwSticky   = $itProbe(null, 'sw');   // session=sw, no ?lang → persists
$itInvalidSw  = $itProbe('fr', 'sw');   // invalid code ignored → session sw
$itInvalidEn  = $itProbe('fr', 'en');   // invalid code ignored → session en
$seoHtmlLang = function (string $html): ?string {
    return preg_match('~<html lang="([^"]+)"~', $html, $m) ? $m[1] : null;
};
ok($seoHtmlLang($itEnFromSw) === 'en-KE'
    && $seoTitle($itEnFromSw) === $seoTitle($seoHtml['/it-support?lang=en'])
    && ($seoCanon($itEnFromSw) ?? '') === 'https://www.harrisonmuhoro.me/it-support?lang=en',
    '?lang=en overrides a stale Kiswahili session (lang attr, title, canonical)');
ok($seoHtmlLang($itSwFromEn) === 'sw-KE'
    && $seoTitle($itSwFromEn) === $seoTitle($seoHtml['/it-support?lang=sw'])
    && ($seoCanon($itSwFromEn) ?? '') === 'https://www.harrisonmuhoro.me/it-support?lang=sw',
    '?lang=sw overrides a stale English session (lang attr, title, canonical)');
ok($seoHtmlLang($itEnFresh) === 'en-KE' && $seoHtmlLang($itSwFresh) === 'sw-KE',
    'fresh sessions render the requested language');
ok($seoHtmlLang($itSwSticky) === 'sw-KE'
    && $seoTitle($itSwSticky) === $seoTitle($seoHtml['/it-support?lang=sw']),
    'session language persists when no ?lang is given');
ok($seoHtmlLang($itInvalidSw) === 'sw-KE' && $seoHtmlLang($itInvalidEn) === 'en-KE',
    'invalid ?lang codes are ignored (session fallback preserved)');

// OG / Twitter parity: social metadata must mirror the page's own
// title, description and canonical on every route.
$ogTwitterOk = true;
foreach ($seoHtml as $h) {
    $t = $seoTitle($h); $d = $seoDesc($h); $c = $seoCanon($h);
    foreach ([
        '~<meta property="og:title"\s+content="([^"]*)"~'       => $t,
        '~<meta property="og:description"\s+content="([^"]*)"~' => $d,
        '~<meta property="og:url"\s+content="([^"]*)"~'         => $c,
        '~<meta name="twitter:title"\s+content="([^"]*)"~'      => $t,
        '~<meta name="twitter:description"\s+content="([^"]*)"~'=> $d,
        '~<meta name="twitter:url"\s+content="([^"]*)"~'        => $c,
    ] as $pattern => $expected) {
        if (!preg_match($pattern, $h, $m) || html_entity_decode($m[1]) !== $expected) {
            $ogTwitterOk = false;
        }
    }
}
ok($ogTwitterOk, 'OG and Twitter metadata agree with title/description/canonical on every route');

// Person entity: one canonical @id, approved sameAs only.
$homeLd = $seoLd($seoHtml['/']);
$people = [];
foreach ($homeLd as $node) { $people = array_merge($people, $seoFindType([$node], 'Person')); }
ok(count($people) === 1 && ($people[0]['@id'] ?? '') === 'https://www.harrisonmuhoro.me/#person',
    'homepage emits exactly one Person with the stable @id');
$sameAs = $people[0]['sameAs'] ?? [];
sort($sameAs);
ok($sameAs === ['https://github.com/harrisonmuhoro', 'https://linkedin.com/in/zion-harrison'],
    'Person sameAs contains only the approved identity profiles');
ok(!preg_grep('~mailto:|wa\.me~', $sameAs), 'Person sameAs excludes email and WhatsApp');

// About: ProfilePage referencing the canonical Person.
$aboutLd = $seoLd($seoHtml['/about']);
$profiles = [];
foreach ($aboutLd as $node) { $profiles = array_merge($profiles, $seoFindType([$node], 'ProfilePage')); }
ok(count($profiles) === 1
    && ($profiles[0]['mainEntity']['@id'] ?? '') === 'https://www.harrisonmuhoro.me/#person'
    && str_ends_with($profiles[0]['url'] ?? '', '/about'),
    'about page emits ProfilePage referencing the canonical Person @id');

// IT Support: Service provider references @id; still exactly one Person node.
$itLd = $seoLd($seoHtml['/it-support?lang=en']);
$itPeople = $itServices = [];
foreach ($itLd as $node) {
    $itPeople    = array_merge($itPeople,    $seoFindType([$node], 'Person'));
    $itServices  = array_merge($itServices,  $seoFindType([$node], 'Service'));
}
ok(count($itPeople) === 1, 'IT Support page has exactly one Person entity (no duplicates)');
ok(count($itServices) === 1
    && ($itServices[0]['provider']['@id'] ?? '') === 'https://www.harrisonmuhoro.me/#person',
    'IT Support Service.provider references the canonical Person @id');

// Person consistency across every route: identical @id, identical name,
// and never more than one Person node per page.
$personIds = $personNames = [];
foreach ($seoHtml as $uri => $h) {
    $found = 0;
    foreach ($seoLd($h) as $node) {
        if (($node['@type'] ?? '') === 'Person') {
            $found++;
            $personIds[$uri]   = $node['@id']   ?? null;
            $personNames[$uri] = $node['name'] ?? null;
        }
    }
    if ($found > 1) { $personIds[$uri] = 'DUPLICATE'; }
}
ok($personIds !== [] && count(array_unique($personIds)) === 1
    && reset($personIds) === 'https://www.harrisonmuhoro.me/#person',
    'Person @id is identical on every page that emits it');
ok(count(array_unique($personNames)) === 1 && reset($personNames) === 'Harrison Muhoro',
    'Person name is consistent on every page');

// Every emitted JSON-LD block must be valid JSON (XSS/encoding safety).
$badJson = 0;
foreach ($seoHtml as $h) {
    preg_match_all('~<script type="application/ld\+json">(.*?)</script>~s', $h, $m);
    foreach (($m[1] ?? []) as $block) {
        if (json_decode(trim($block), true) === null && trim($block) !== 'null') { $badJson++; }
    }
}
ok($badJson === 0, 'all JSON-LD blocks are valid JSON');

// Sitemap: parse with DOMDocument — well-formedness, namespaces, exact
// scheme/host per URL, required+strict lastmod, uniqueness, image assets.
$sitemap = page_probe(['page' => 'sitemap.xml.php', 'uri' => '/sitemap.xml', 'host' => 'www.harrisonmuhoro.me']);
libxml_use_internal_errors(true);
$sitemapDom = new DOMDocument();
$sitemapParsed = $sitemapDom->loadXML($sitemap);
libxml_clear_errors();
ok($sitemapParsed === true, 'sitemap is well-formed XML (DOMDocument)');
$sitemapEntries = [];
$sitemapImgs    = [];
$sitemapNsOk    = false;
if ($sitemapParsed) {
    $root = $sitemapDom->documentElement;
    $sitemapNsOk = $root !== null
        && $root->localName === 'urlset'
        && $root->namespaceURI === 'http://www.sitemaps.org/schemas/sitemap/0.9'
        && $root->lookupNamespaceURI('image') === 'http://www.google.com/schemas/sitemap-image/1.1';
    $xp = new DOMXPath($sitemapDom);
    $xp->registerNamespace('sm', 'http://www.sitemaps.org/schemas/sitemap/0.9');
    $xp->registerNamespace('image', 'http://www.google.com/schemas/sitemap-image/1.1');
    foreach ($xp->query('//sm:url') ?: [] as $urlNode) {
        $loc = $urlNode->getElementsByTagNameNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'loc')->item(0);
        $mod = $urlNode->getElementsByTagNameNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'lastmod')->item(0);
        if ($loc) {
            $sitemapEntries[] = [
                'loc'      => $loc->textContent,
                'lastmod'  => $mod ? trim($mod->textContent) : null,
                'locCount' => $urlNode->getElementsByTagNameNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'loc')->length,
                'modCount' => $mod ? $urlNode->getElementsByTagNameNS('http://www.sitemaps.org/schemas/sitemap/0.9', 'lastmod')->length : 0,
            ];
        }
        foreach ($xp->query('.//image:image/image:loc', $urlNode) ?: [] as $imgLoc) {
            $sitemapImgs[] = $imgLoc->textContent;
        }
    }
}
ok($sitemapNsOk, 'sitemap root is <urlset> with sitemap + image namespaces declared');
ok(count($sitemapEntries) > 0, 'sitemap parses to at least one <url> in the sitemap namespace');
$locs = array_column($sitemapEntries, 'loc');
ok(count($locs) === count(array_unique($locs)), 'sitemap contains no duplicate <loc> URLs');
$urlsOk = $sitemapEntries !== [];
foreach ($sitemapEntries as $e) {
    // Exactly one loc and one lastmod per <url>.
    if ($e['locCount'] !== 1 || $e['modCount'] !== 1) { $urlsOk = false; continue; }
    // lastmod must be a real calendar date in YYYY-MM-DD.
    if ($e['lastmod'] === null
        || !preg_match('/^(\d{4})-(\d{2})-(\d{2})$/', $e['lastmod'], $dm)
        || !checkdate((int) $dm[2], (int) $dm[3], (int) $dm[1])) { $urlsOk = false; continue; }
    // URL must parse cleanly with the exact canonical scheme + host.
    $parts = parse_url($e['loc']);
    if (!is_array($parts)
        || ($parts['scheme'] ?? '') !== 'https'
        || ($parts['host'] ?? '') !== 'www.harrisonmuhoro.me'
        || empty($parts['path'])) { $urlsOk = false; }
}
ok($urlsOk, 'every sitemap <url> has one loc + valid lastmod date and exact https://www.harrisonmuhoro.me origin');
ok(in_array('https://www.harrisonmuhoro.me/it-support?lang=en', $locs, true)
    && in_array('https://www.harrisonmuhoro.me/it-support?lang=sw', $locs, true),
    'sitemap lists both IT Support language URLs');
ok(!in_array('https://www.harrisonmuhoro.me/it-support', $locs, true),
    'sitemap has no bare /it-support entry');
$imgOk = $sitemapImgs !== [];
foreach ($sitemapImgs as $loc) {
    $path = parse_url(html_entity_decode($loc), PHP_URL_PATH);
    if ($path === null || !is_file(PROJECT_ROOT . $path)) { $imgOk = false; }
}
ok($imgOk, 'every sitemap <image:loc> maps to an existing file (' . count($sitemapImgs) . ' images)');

// ── Sessions: lazy start — public routes must not open a PHP session ──
echo "== sessions ==\n";

$routerProbe = function (string $uri): string {
    return page_probe(['router' => true, 'uri' => $uri, 'host' => 'www.harrisonmuhoro.me']);
};
$sessionState = function (string $html): ?int {
    return preg_match('/<!--probe:session_status=(\d+)/', $html, $m) ? (int) $m[1] : null;
};
foreach (['/', '/about', '/services', '/projects', '/sitemap.xml', '/robots.txt', '/nonexistent-404'] as $u) {
    ok($sessionState($routerProbe($u)) === PHP_SESSION_NONE,
        "no PHP session started for public route $u");
}
foreach (['/contact', '/it-support'] as $u) {
    ok($sessionState($routerProbe($u)) === PHP_SESSION_ACTIVE,
        "session started for stateful route $u");
}
// Session cookie attributes on a stateful route: host-only (no domain),
// HttpOnly, SameSite=Lax, path=/, browser-session lifetime. 'secure' is
// asserted as a boolean — true only when the request is HTTPS.
$cookieParams = function (string $html): ?array {
    return preg_match('/<!--probe:session_status=\d+;cookie=(.*?)-->/', $html, $m)
        ? (json_decode(html_entity_decode($m[1], ENT_QUOTES), true) ?: null) : null;
};
foreach (['/contact', '/it-support'] as $u) {
    $cp = $cookieParams($routerProbe($u));
    ok(is_array($cp)
        && ($cp['httponly'] ?? null) === true
        && strtolower((string) ($cp['samesite'] ?? '')) === 'lax'
        && ($cp['domain'] ?? 'x') === ''
        && ($cp['path'] ?? '') === '/'
        && (int) ($cp['lifetime'] ?? -1) === 0
        && array_key_exists('secure', $cp),
        "session cookie on $u is host-only, HttpOnly, SameSite=Lax, path=/, session-lifetime");
}
// Language persistence still works end-to-end through the router:
// ?lang=sw renders Kiswahili even though the session starts fresh.
$routerSw = $routerProbe('/it-support?lang=sw');
ok(($seoHtmlLang($routerSw) ?? '') === 'sw-KE'
    && str_contains($routerSw, '<title>Huduma za Msaada'),
    'router: ?lang=sw renders Kiswahili through index.php');

// ── robots.txt: single dynamic source, canonical sitemap URL ──
echo "== robots.txt ==\n";
$robots = page_probe(['page' => 'robots.txt.php', 'uri' => '/robots.txt', 'host' => 'www.harrisonmuhoro.me']);
ok(str_contains($robots, 'User-agent: *'), 'robots.txt allows all user agents');
ok(str_contains($robots, 'Sitemap: https://www.harrisonmuhoro.me/sitemap.xml'),
    'robots.txt references the canonical sitemap URL');
ok(str_contains($robots, 'Disallow: /backend/'), 'robots.txt disallows backend paths');
ok(str_contains($robots, 'Disallow: /storage/'), 'robots.txt disallows private rate-limit storage');
ok(!is_file(PROJECT_ROOT . '/robots.txt'), 'no static robots.txt shadowing the dynamic route');

// Explicit cleanup before exit (shutdown function is the backup).
stop_stub($server, $nullFile);

echo "\n$passed passed, $failed failed\n";
exit($failed > 0 ? 1 : 0);
