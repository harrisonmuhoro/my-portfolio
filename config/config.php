<?php
/**
 * Central Configuration File
 * Harrison Muhoro — Developer Portfolio
 * All constants defined here. No hardcoded values in pages.
 */

// ─── ENVIRONMENT ──────────────────────────────────────────
define('ENVIRONMENT', 'development'); // 'production' | 'development'
define('DEBUG_MODE', ENVIRONMENT === 'development');

// ─── BASE PATH (WAMP-compatible) ──────────────────────────
// Auto-detects whether running at root or in a subdirectory
// e.g. localhost/harrison → BASE_PATH = '/harrison'
// e.g. localhost/         → BASE_PATH = ''
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__) === dirname(dirname(__DIR__))
        ? __DIR__ . '/..'
        : realpath(__DIR__ . '/..'));
}

// Detect base path from script location relative to doc root
$_doc_root   = rtrim(str_replace('\\', '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$_proj_root  = rtrim(str_replace('\\', '/', realpath(__DIR__ . '/..')), '/');
$_base_path  = str_replace($_doc_root, '', $_proj_root);
define('BASE_PATH', rtrim($_base_path, '/'));   // e.g. '/harrison' or ''
define('BASE_URL',  'http://' . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);
unset($_doc_root, $_proj_root, $_base_path);

// ─── SITE IDENTITY ────────────────────────────────────────
define('SITE_NAME', 'Harrison Muhoro');
define('SITE_TAGLINE', 'Building Systems That Work. Delivering Solutions That Scale.');
define('SITE_DESCRIPTION', 'Harrison Muhoro is a Web Developer and Full-Stack Developer in Training based in Kenya, specializing in PHP, MySQL, and business-focused web systems for corporate, institutional, and government clients.');
define('SITE_URL', ENVIRONMENT === 'development' ? BASE_URL : 'https://harrisonmuhoro.dev');
define('SITE_VERSION', '1.0.0');
define('SITE_LANGUAGE', 'en-KE');
define('SITE_LOCALE', 'en_KE');

// ─── PERSONAL DETAILS ─────────────────────────────────────
define('OWNER_NAME', 'Harrison Muhoro');
define('OWNER_FIRST_NAME', 'Harrison');
define('PRIMARY_TITLE', 'Web Developer');
define('SECONDARY_TITLE', 'Full-Stack Developer in Training');
define('LOCATION', 'Nyeri, Kenya');

// ─── CONTACT INFORMATION ──────────────────────────────────
define('EMAIL_ADDRESS', 'zionh191@gmail.com');
define('PHONE_PRIMARY', '+254 726 300 091');
define('PHONE_SECONDARY', '+254 105 628 524');
define('WHATSAPP_NUMBER', '254726300091');
define('WHATSAPP_MESSAGE', 'Hello%20Harrison,%20I%20found%20your%20portfolio%20and%20I%20am%20interested%20in%20your%20web%20development%20services.%20Please%20get%20back%20to%20me.');
define('WHATSAPP_LINK', 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . WHATSAPP_MESSAGE);

// ─── SOCIAL LINKS ─────────────────────────────────────────
define('SOCIAL_LINKS', [
    'github'   => 'https://github.com/Ziha546',
    'linkedin' => 'https://linkedin.com/in/zion-harrison',
    'facebook' => 'https://www.facebook.com/harrison.muhoro.1',
    'whatsapp' => WHATSAPP_LINK,
    'email'    => 'mailto:' . EMAIL_ADDRESS,
]);

// ─── CV / RESUME ──────────────────────────────────────────
define('CV_PATH', SITE_URL . '/assets/docs/harrison-muhoro-cv.pdf');

// ─── DATABASE ─────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_NAME', 'portfolio_db');
define('DB_USER', 'portfolio_user');
define('DB_PASS', '');   // Set via environment variable in production
define('DB_CHARSET', 'utf8mb4');

// ─── SEO CONSTANTS ────────────────────────────────────────
define('SEO_KEYWORDS', 'Web Developer Kenya, PHP Developer Kenya, Full-Stack Developer Kenya, Website Developer Nyeri, Business System Developer Kenya, Harrison Muhoro, Web Development Nairobi, Corporate Web Developer Kenya');
define('SEO_AUTHOR', OWNER_NAME);
define('SEO_ROBOTS', 'index, follow');
define('OG_IMAGE', SITE_URL . '/assets/images/og-image.jpg');
define('OG_TYPE', 'website');
define('TWITTER_CARD', 'summary_large_image');

// ─── SECURITY ─────────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
define('FORM_RATE_LIMIT', 3);        // Max submissions per hour
define('ALLOWED_FILE_TYPES', []);    // No file uploads by default

// ─── ERROR HANDLING ───────────────────────────────────────
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
}

// ─── TIMEZONE ─────────────────────────────────────────────
date_default_timezone_set('Africa/Nairobi');
