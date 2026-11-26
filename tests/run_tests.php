<?php
/**
 * tests/run_tests.php — CLI test suite (no framework required).
 *
 * Usage:  php tests/run_tests.php
 * Exit code 0 = all passed, 1 = failures.
 *
 * Covers: i18n dictionary integrity & language validation,
 * shared rate limiter behaviour, and mailer address sanitisation.
 * Network delivery is verified separately via HTTP (see README note).
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

echo "== i18n ==\n";

// Every key must exist in both languages and be non-empty.
$ref = new ReflectionFunction('t');
// Build the union of keys by probing known prefixes is brittle; instead
// verify representative keys plus parity via a static extraction.
$dict_src = file_get_contents(PROJECT_ROOT . '/includes/i18n.php');
preg_match_all("/'([a-z_0-9]+)'\s*=>/", $dict_src, $m);
$all_keys = array_diff(array_unique($m[1]), ['en', 'sw']);
$missing  = [];
foreach ($all_keys as $k) {
    foreach (['en', 'sw'] as $l) {
        if (t($k, $l) === $k) { $missing[] = "$k ($l)"; }
    }
}
ok($missing === [], 'all dictionary keys present in en+sw' . ($missing ? ' — missing: ' . implode(', ', $missing) : ''));

ok(t('hero_title', 'en') !== t('hero_title', 'sw'), 'en/sw translations differ (real translations)');
ok(t('nonexistent_key') === 'nonexistent_key', 'unknown key falls back to key name');
ok(t('hero_title', 'fr') === t('hero_title', 'en'), 'invalid language falls back to English');

echo "== rate limiter ==\n";

$bucket = 'test:' . uniqid('', true);
// Use a 2s window, limit 2.
ok(rate_limit_hit($bucket, 2, 2) === 0, 'first hit allowed');
ok(rate_limit_hit($bucket, 2, 2) === 0, 'second hit allowed');
$blocked = rate_limit_hit($bucket, 2, 2);
ok($blocked > 0 && $blocked <= 2, "third hit blocked with retry_after=$blocked");

// Different bucket unaffected.
ok(rate_limit_hit($bucket . ':other', 2, 2) === 0, 'separate bucket unaffected');

// Window expiry resets the counter.
$bucket2 = 'test2:' . uniqid('', true);
rate_limit_hit($bucket2, 1, 1);
ok(rate_limit_hit($bucket2, 1, 1) > 0, 'limit enforced within window');
sleep(2);
ok(rate_limit_hit($bucket2, 1, 1) === 0, 'counter resets after window expires');

echo "== mailer ==\n";

ok(mail_addr('Test "User"', 'a@b.com') === '"Test \\"User\\"" <a@b.com>', 'mail_addr quotes display name safely');
ok(mail_addr("Bad\r\nName", 'a@b.com') === '"Bad Name" <a@b.com>', 'mail_addr strips CRLF injection');
ok(mail_addr('', 'a@b.com') === '<a@b.com>', 'mail_addr handles empty name');

echo "\n$passed passed, $failed failed\n";
exit($failed > 0 ? 1 : 0);
