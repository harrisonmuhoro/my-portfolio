<?php

$_is_local = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']);
define('ENVIRONMENT', $_is_local ? 'development' : 'production');
define('DEBUG_MODE', ENVIRONMENT === 'development');

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', dirname(__DIR__) === dirname(dirname(__DIR__))
        ? __DIR__ . '/..'
        : realpath(__DIR__ . '/..'));
}

$_doc_root   = rtrim(str_replace(['\\', '//'], '/', $_SERVER['DOCUMENT_ROOT'] ?? ''), '/');
$_proj_root  = rtrim(str_replace(['\\', '//'], '/', realpath(__DIR__ . '/..')), '/');
$_base_path  = str_replace($_doc_root, '', $_proj_root);
define('BASE_PATH', rtrim($_base_path, '/'));   // e.g. '/harrison' or ''
define('BASE_URL',  (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://') . ($_SERVER['HTTP_HOST'] ?? 'localhost') . BASE_PATH);
unset($_doc_root, $_proj_root, $_base_path);

define('SITE_NAME', 'Harrison Muhoro');
define('SITE_TAGLINE', 'Building Systems That Work. Delivering Solutions That Scale.');
define('SITE_DESCRIPTION', 'Harrison Muhoro is a software engineer in Kenya who builds production-ready enterprise systems—backend architecture, performant web applications, and maintainable software for healthcare, operations, and business teams.');
define('SITE_URL', BASE_URL); 
define('SITE_VERSION', '1.0.0');
define('SITE_LANGUAGE', 'en-KE');
define('SITE_LOCALE', 'en_KE');

define('OWNER_NAME', 'Harrison Muhoro');
define('OWNER_FIRST_NAME', 'Harrison');
define('PRIMARY_TITLE', 'Software Engineer');
define('SECONDARY_TITLE', 'Enterprise Systems & Full-Stack Engineer');
define('LOCATION', 'Nyeri, Kenya');

define('EMAIL_ADDRESS', 'your@email.com');
define('PHONE_PRIMARY', '+254 700 000 000');
define('PHONE_SECONDARY', '+254 700 000 000');
define('WHATSAPP_NUMBER', '254700000000');
define('WHATSAPP_MESSAGE', 'Hello%20Harrison,%20I%20found%20your%20portfolio%20and%20I%20am%20interested%20in%20your%20web%20development%20services, system%20development%20services, and%20I%20would%20like%20to%20work%20with%20you .%20Please%20get%20back%20to%20me.');
define('WHATSAPP_LINK', 'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . WHATSAPP_MESSAGE);

define('SOCIAL_LINKS', [
    'github'   => 'https://github.com/harrisonmuhoro',
    'linkedin' => 'https://linkedin.com/in/zion-harrison',
    'facebook' => 'https://www.facebook.com/harrison.muhoro.1',
    'whatsapp' => WHATSAPP_LINK,
    'email'    => 'mailto:' . EMAIL_ADDRESS,
]);

// ─── CV / RESUME ──────────────────────────────────────────
define('CV_PATH', SITE_URL . '/assets/docs/harrison-muhoro-cv.pdf');

// ─── DATABASE ─────────────────────────────────────────────
define('DB_HOST', 'localhost');
define('DB_USER', 'your_db_user');
define('DB_NAME', 'your_db_name');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ─── SEO CONSTANTS ────────────────────────────────────────
define('SEO_KEYWORDS', 'Web Developer Kenya, PHP Developer Kenya, Full-Stack Developer Kenya, Website Developer Nyeri, Business System Developer Kenya, Harrison Muhoro, Web Development Nyeri, Corporate Web Developer Kenya');
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
    ini_set('expose_php', 0);  // Hide X-Powered-By header in production
}

// ─── TIMEZONE ─────────────────────────────────────────────
date_default_timezone_set('Africa/Nairobi');
