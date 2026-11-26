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

// Handler name is restricted to a basename inside backend/ — no traversal.
$handler = basename($spec['handler'] ?? 'it_support_handler.php');

$_SERVER['REQUEST_METHOD'] = strtoupper($spec['method'] ?? 'POST');
$_SERVER['REMOTE_ADDR']     = $spec['ip'] ?? '203.0.113.10';
$_SERVER['HTTP_HOST']       = 'localhost';
$_SERVER['REQUEST_URI']     = '/backend/' . $handler;
$_SERVER['SERVER_NAME']     = 'localhost';
$_POST  = $spec['post']    ?? [];
$_GET   = $spec['get']     ?? [];
$_COOKIE = [];

// Fresh session per probe; seed it with the spec's session values.
session_id('probe-' . bin2hex(random_bytes(8)));
session_start();
$_SESSION = $spec['session'] ?? [];

// Report the final HTTP status after json_response() exits.
register_shutdown_function(function () {
    echo "\n__HTTP__:" . http_response_code();
});

require PROJECT_ROOT . '/backend/' . $handler;
