<?php
/**
 * tests/page_probe.php — Render a real page (or sitemap) in a subprocess
 * and print the resulting HTML/XML to stdout.
 *
 * Usage:  php tests/page_probe.php <spec.json>
 * Spec:   {"page": "pages/about.php", "uri": "/about", "get": {...},
 *          "session": {...}, "host": "www.harrisonmuhoro.me", "env": {...}}
 *
 * Mirrors handler_probe.php: env vars are applied before config.php loads,
 * a session is started (seeded from spec), and the page file is included
 * exactly as index.php's router would include it.
 */

$specFile = $argv[1] ?? null;
if (!$specFile || !is_file($specFile)) {
    fwrite(STDERR, "usage: php page_probe.php <spec.json>\n");
    exit(2);
}
$spec = json_decode(file_get_contents($specFile), true);
if (!is_array($spec)) {
    fwrite(STDERR, "invalid spec json\n");
    exit(2);
}

define('PROJECT_ROOT', dirname(__DIR__));

foreach (($spec['env'] ?? []) as $k => $v) {
    putenv($k . '=' . $v);
}

$page = basename($spec['page'] ?? '');
$allowed = ['home-redesign.php', 'about.php', 'services.php', 'projects.php',
            'it-support.php', 'contact.php', '404.php', 'sitemap.xml.php'];
if (!in_array($page, $allowed, true)) {
    fwrite(STDERR, "page not probeable: " . ($spec['page'] ?? '?') . "\n");
    exit(2);
}

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR']     = $spec['ip'] ?? '203.0.113.10';
$_SERVER['HTTP_HOST']       = $spec['host'] ?? 'www.harrisonmuhoro.me';
$_SERVER['REQUEST_URI']     = $spec['uri'] ?? '/';
$_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
$_GET  = $spec['get'] ?? [];
$_POST = [];
$_COOKIE = [];

session_id('page' . bin2hex(random_bytes(8)));
session_save_path(sys_get_temp_dir());
session_start();
$_SESSION = $spec['session'] ?? [];

$rel = $page === 'sitemap.xml.php' ? 'sitemap.xml.php' : 'pages/' . $page;
require PROJECT_ROOT . '/' . $rel;
