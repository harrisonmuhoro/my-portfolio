<?php
/**
 * tests/handler_probe.php — Simulates one POST request through
 * backend/it_support_handler.php without a web server.
 *
 * Usage:  php tests/handler_probe.php <spec.json>
 * Spec:   {"post": {...}, "session": {...}, "env": {...}, "ip": "...",
 *          "handler": "it_support_handler.php", "method": "POST"}
 *
 * Output: the handler's JSON response followed by a line
 *         "__HTTP__:<code>" appended by a shutdown function.
 * Never sends real email — tests point RESEND_API_URL at a stub.
 */

$specFile = $argv[1] ?? null;
if (!$specFile || !is_file($specFile)) {
    fwrite(STDERR, "usage: php handler_probe.php <spec.json>\n");
    exit(2);
}
$spec = json_decode(file_get_contents($specFile), true);
if (!is_array($spec)) {
    fwrite(STDERR, "invalid spec json\n");
    exit(2);
}

define('PROJECT_ROOT', dirname(__DIR__));

// Environment overrides must be set before config.php reads them.
foreach (($spec['env'] ?? []) as $k => $v) {
    putenv($k . '=' . $v);
}
// Simulate deployment environment even when this checkout has an untracked
// config/config.local.php that forces development mode.
if (getenv('APP_ENV') !== false && !defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('APP_ENV'));
}

// Handler name is restricted to a basename inside backend/ — no traversal.
$handler = basename($spec['handler'] ?? 'it_support_handler.php');

$_SERVER['REQUEST_METHOD'] = strtoupper($spec['method'] ?? 'POST');
$_SERVER['REMOTE_ADDR']     = $spec['ip'] ?? '203.0.113.10';
$_SERVER['HTTP_HOST']       = $spec['host'] ?? 'localhost';
$_SERVER['REQUEST_URI']     = '/backend/' . $handler;
$_SERVER['SERVER_NAME']     = 'localhost';
if (isset($spec['document_root'])) {
    $_SERVER['DOCUMENT_ROOT'] = $spec['document_root'];
}
$_POST  = $spec['post']    ?? [];
$_GET   = $spec['get']     ?? [];
$_COOKIE = [];

// A supplied ID lets sequential subprocess probes share PHP's persisted
// session storage, just as a browser reuses its session cookie.
$sessionId = $spec['session_id'] ?? ('probe' . bin2hex(random_bytes(8)));
if (!preg_match('/^[A-Za-z0-9,-]{1,128}$/', $sessionId)) {
    fwrite(STDERR, "invalid session id\n");
    exit(2);
}
session_id($sessionId);
session_save_path(sys_get_temp_dir());
session_start();
if (empty($spec['session_id']) || empty($_SESSION)) {
    $_SESSION = $spec['session'] ?? [];
}

// Report the final HTTP status after json_response() exits.
register_shutdown_function(function () {
    echo "\n__HTTP__:" . http_response_code();
});

require PROJECT_ROOT . '/backend/' . $handler;
