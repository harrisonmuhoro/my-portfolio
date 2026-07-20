<?php

// Define the project root as a constant so all includes work
define('PROJECT_ROOT', __DIR__);

require_once PROJECT_ROOT . '/config/config.php';

// ── Send security headers (MUST be before any output) ──────
require_once PROJECT_ROOT . '/config/security_headers.php';

// ── Secure session configuration ───────────────────────────
if (session_status() === PHP_SESSION_NONE) {
    $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params([
        'lifetime' => 0,              // Session cookie (expires on browser close)
        'path'     => '/',
        'domain'   => $_SERVER['HTTP_HOST'] ?? '',
        'secure'   => $is_secure,      // Only send over HTTPS in production
        'httponly'  => true,           // Prevent JavaScript access
        'samesite'  => 'Lax',          // CSRF protection
    ]);
    session_start();
}

// ── Simple Router ──────────────────────────────────────────
$request_uri = $_SERVER['REQUEST_URI'];
// Remove query string
$path = parse_url($request_uri, PHP_URL_PATH);

// If BASE_PATH is defined and the request starts with it, strip it out.
if (BASE_PATH !== '' && strpos($path, BASE_PATH) === 0) {
    $path = substr($path, strlen(BASE_PATH));
}

// Default to '/' if empty after stripping base path
if (empty($path)) {
    $path = '/';
}

// Map paths to page files
$routes = [
    '/'          => 'pages/home-redesign.php',
    '/about'     => 'pages/about.php',
    '/services'  => 'pages/services.php',
    '/projects'  => 'pages/projects.php',
    '/contact'   => 'pages/contact.php',
];

// Determine the page file
if (array_key_exists($path, $routes)) {
    $page_file = PROJECT_ROOT . '/' . $routes[$path];
} else {
    // 404
    http_response_code(404);
    $page_file = PROJECT_ROOT . '/pages/404.php';
}

// Include the target page
if (file_exists($page_file)) {
    require_once $page_file;
} else {
    echo "Error: Page file not found.";
}
