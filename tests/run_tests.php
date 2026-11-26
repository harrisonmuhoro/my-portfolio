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
    if (is_resource($server)) {
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
        } else {
            proc_terminate($server);
            usleep(200000);
        }
        @proc_close($server);
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

// Symlink that resolves INTO the public root must be rejected — the
// post-creation realpath check is what catches it.
$lnkWeb = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_lnkw_' . bin2hex(random_bytes(6));
$lnkTarget = $lnkWeb . DIRECTORY_SEPARATOR . 'real_store';
$lnkLink = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'rl_lnk_' . bin2hex(random_bytes(6));
mkdir($lnkWeb);
mkdir($lnkTarget);
if (@symlink($lnkTarget, $lnkLink)) {
    $lnkSpec = $mkSpec(['csrf_token' => 'test-token-123'], [], $deadEnv + [
        'APP_ENV' => 'production',
        'RATE_LIMIT_DIR' => $lnkLink,
    ]);
    $lnkSpec['host'] = 'portfolio.example';
    $lnkSpec['document_root'] = $lnkWeb;
    $r = probe($lnkSpec);
    ok($r['http'] === 429, 'production rejects a symlink resolving inside the public root');
    @unlink($lnkLink);
} else {
    echo "  SKIP  symlink test (symlink() unavailable on this platform)\n";
}
foreach ((array) glob($lnkTarget . DIRECTORY_SEPARATOR . '*.json') as $file) { @unlink($file); }
@rmdir($lnkTarget);
@rmdir($lnkWeb);

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

// Explicit cleanup before exit (shutdown function is the backup).
stop_stub($server, $nullFile);

echo "\n$passed passed, $failed failed\n";
exit($failed > 0 ? 1 : 0);
