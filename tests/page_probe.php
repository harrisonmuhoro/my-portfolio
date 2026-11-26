<?php
/**
 * tests/page_probe.php — Render a real page (or the full router) in a
 * subprocess and print the resulting HTML/XML to stdout.
 *
 * Usage:  php tests/page_probe.php <spec.json>
 * Spec:   {"page": "pages/about.php", "uri": "/about", "get": {...},
 *          "session": {...}, "host": "www.harrisonmuhoro.me", "env": {...},
 *          "router": true}
 *
 * Page mode includes the page file directly (a session is pre-started so
 * CSRF/i18n code has somewhere to store state). Router mode requires
 * index.php instead — no session is pre-started, so tests observe exactly
 * which routes the application decides need one. Router mode appends an
 * HTML comment reporting the final session_status() for assertions.
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

$root   = dirname(__DIR__);
$router = !empty($spec['router']);

foreach (($spec['env'] ?? []) as $k => $v) {
    putenv($k . '=' . $v);
}

$_SERVER['REQUEST_METHOD'] = 'GET';
$_SERVER['REMOTE_ADDR']     = $spec['ip'] ?? '203.0.113.10';
$_SERVER['HTTP_HOST']       = $spec['host'] ?? 'www.harrisonmuhoro.me';
$_SERVER['REQUEST_URI']     = $spec['uri'] ?? '/';
$_SERVER['SERVER_NAME']     = $_SERVER['HTTP_HOST'];
$_SERVER['HTTPS']           = !empty($spec['https']) ? 'on' : 'off';
$_GET  = $spec['get'] ?? [];
// Real requests populate $_GET from the URI query string — mirror that so
// router mode behaves like Apache/PHP-FPM rather than a synthetic GET.
$probeQuery = parse_url($_SERVER['REQUEST_URI'], PHP_URL_QUERY);
if ($probeQuery) {
    parse_str($probeQuery, $probeGet);
    $_GET = $probeGet + $_GET;
}
$_POST = [];
$_COOKIE = [];

if ($router) {
    // index.php defines PROJECT_ROOT itself and owns the session decision.
    require $root . '/index.php';
    $cp = session_status() === PHP_SESSION_ACTIVE ? session_get_cookie_params() : [];
    echo "\n<!--probe:session_status=" . session_status()
       . ";cookie=" . htmlspecialchars(json_encode($cp), ENT_QUOTES) . "-->\n";
    exit;
}

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', $root);
}
require_once PROJECT_ROOT . '/config/config.php';

$page = basename($spec['page'] ?? '');
$allowed = ['home-redesign.php', 'about.php', 'services.php', 'projects.php',
            'it-support.php', 'contact.php', '404.php', 'sitemap.xml.php',
            'robots.txt.php'];
if (!in_array($page, $allowed, true)) {
    fwrite(STDERR, "page not probeable: " . ($spec['page'] ?? '?') . "\n");
    exit(2);
}

session_id('page' . bin2hex(random_bytes(8)));
session_save_path(sys_get_temp_dir());
session_start();
$_SESSION = $spec['session'] ?? [];

$rel = $page === 'sitemap.xml.php' || $page === 'robots.txt.php'
    ? $page : 'pages/' . $page;
require PROJECT_ROOT . '/' . $rel;
