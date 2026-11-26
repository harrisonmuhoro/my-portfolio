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

// ── mailer helpers ──────────────────────────────────────────
echo "== mailer ==\n";

ok(mail_addr('Test "User"', 'a@b.com') === '"Test \\"User\\"" <a@b.com>', 'mail_addr quotes display name safely');
ok(mail_addr("Bad\r\nName", 'a@b.com') === '"Bad Name" <a@b.com>', 'mail_addr strips CRLF injection');
ok(mail_addr('', 'a@b.com') === '<a@b.com>', 'mail_addr handles empty name');

// ── handler integration probes ──────────────────────────────
echo "== handler probes ==\n";

/**
 * Run one simulated POST through the real handler in a subprocess.
 * Returns ['json' => array|null, 'http' => int, 'raw' => string].
 */
function probe(array $spec): array {
    $file = tempnam(sys_get_temp_dir(), 'probe_') . '.json';
    file_put_contents($file, json_encode($spec));
    $cmd = 'php ' . escapeshellarg(PROJECT_ROOT . '/tests/handler_probe.php') . ' ' . escapeshellarg($file);
    $out = (string) shell_exec($cmd);
    @unlink($file);

    $http = 200;
    if (preg_match('/__HTTP__:(\d+)/', $out, $m)) {
        $http = (int) $m[1];
        $out  = substr($out, 0, strpos($out, '__HTTP__:'));
    }
    $json = json_decode(trim($out), true);
    return ['json' => is_array($json) ? $json : null, 'http' => $http, 'raw' => $out];
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

// Unused TEST-NET-3 range; each probe gets a distinct IP so rate-limit
// state never leaks between tests or between runs.
$nextIp = function () {
    static $n = 0;
    return '198.51.100.' . (++$n);
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

$stubPort = 18231;
$nullFile = tempnam(sys_get_temp_dir(), 'stub_null_');
$server = proc_open(
    'php -S 127.0.0.1:' . $stubPort . ' ' . escapeshellarg(PROJECT_ROOT . '/tests/resend_stub.php'),
    [0 => ['pipe', 'r'], 1 => ['file', $nullFile, 'a'], 2 => ['file', $nullFile, 'a']],
    $pipes
);
usleep(500000); // let the stub boot

$providerEnv = [
    'RESEND_API_KEY' => 'test-key',
    'RESEND_API_URL' => 'http://127.0.0.1:' . $stubPort . '/emails',
];

// Success path: stub returns 200
$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $providerEnv));
ok(($r['json']['success'] ?? false) === true, 'provider 200 → success:true');

// Failure path: unreachable endpoint → generic user-facing error
$r = probe($mkSpec(['csrf_token' => 'test-token-123'], [], $deadMailEnv));
ok(($r['json']['success'] ?? true) === false, 'provider unreachable → success:false (generic error)');

// ── Rate limit enforcement through the handler ──────────────
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

if (is_resource($server)) { proc_terminate($server); }
@unlink($nullFile);

echo "\n$passed passed, $failed failed\n";
exit($failed > 0 ? 1 : 0);
