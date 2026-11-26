<?php
/**
 * config/config.php
 * Safe to commit — all secrets come from environment variables.
 * Local overrides go in config/config.local.php (gitignored).
 */

// ─── PROJECT ROOT ─────────────────────────────────────────────────────────────
if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', realpath(__DIR__ . '/..'));
}

// ─── LOCAL OVERRIDES (never committed) ────────────────────────────────────────
// Loaded BEFORE the defaults below so config.local.php values win.
// Create config/config.local.php on WAMP for local-only values.
$_local = __DIR__ . '/config.local.php';
if (file_exists($_local)) {
    require_once $_local;
}
unset($_local);

// ─── ENVIRONMENT ──────────────────────────────────────────────────────────────
$_is_local = in_array($_SERVER['HTTP_HOST'] ?? '', ['localhost', '127.0.0.1']);
if (!defined('ENVIRONMENT')) {
    define('ENVIRONMENT', getenv('APP_ENV') ?: ($_is_local ? 'development' : 'production'));
}
if (!defined('DEBUG_MODE')) {
    define('DEBUG_MODE', ENVIRONMENT === 'development');
}

// ─── BASE PATH (Wasmer-safe) ───────────────────────────────────────────────────
// On Wasmer: DOCUMENT_ROOT is unreliable. Use env var instead, fallback to ''.
// On WAMP:   Set BASE_PATH=/harrison in config.local.php or .env.
if (!defined('BASE_PATH')) {
    $envBase = getenv('BASE_PATH');
    if ($envBase !== false) {
        // Explicit env var — most reliable
        define('BASE_PATH', rtrim($envBase, '/'));
    } elseif (!empty($_SERVER['DOCUMENT_ROOT'])) {
        // WAMP/Apache fallback: compute from doc root
        $_doc_root  = rtrim(str_replace(['\\', '//'], '/', $_SERVER['DOCUMENT_ROOT']), '/');
        $_proj_root = rtrim(str_replace(['\\', '//'], '/', realpath(__DIR__ . '/..')), '/');
        $_computed  = str_replace($_doc_root, '', $_proj_root);
        define('BASE_PATH', rtrim($_computed, '/'));
        unset($_doc_root, $_proj_root, $_computed);
    } else {
        define('BASE_PATH', '');
    }
}

// ─── BASE URL ─────────────────────────────────────────────────────────────────
define('BASE_URL',
    (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https://' : 'http://')
    . ($_SERVER['HTTP_HOST'] ?? 'localhost')
    . BASE_PATH
);

// ─── SITE INFO ────────────────────────────────────────────────────────────────
define('SITE_NAME',        'Harrison Muhoro');
define('SITE_TAGLINE',     'Building Systems That Work. Delivering Solutions That Scale.');
define('SITE_DESCRIPTION', 'Harrison Muhoro is a software developer and technology consultant in Kenya offering full-stack development, IT support & networking, and enterprise software development. Explore projects, technical skills, certifications and IT support services.');
// Use the production domain directly so deployment environment variables
// cannot leak the former Wasmer hostname into canonicals or the sitemap.
$canonicalOrigin = $_is_local ? rtrim(BASE_URL, '/') : 'https://www.harrisonmuhoro.me';
define('SITE_URL',         $canonicalOrigin);
define('SITE_VERSION',     '1.0.0');
define('SITE_LANGUAGE',    'en-KE');
define('SITE_LOCALE',      'en_KE');

// ─── OWNER INFO ───────────────────────────────────────────────────────────────
define('OWNER_NAME',       'Harrison Muhoro');
define('OWNER_FIRST_NAME', 'Harrison');
define('PRIMARY_TITLE',    'Software Developer & Technology Consultant');
define('SECONDARY_TITLE',  'Full-Stack Development · IT Support & Networking · Enterprise Software Development');
define('LOCATION',         'Nyeri, Kenya');

// ─── CONTACT ──────────────────────────────────────────────────────────────────
define('EMAIL_ADDRESS',    getenv('CONTACT_EMAIL')   ?: 'zionmuhoro@gmail.com');
define('PHONE_PRIMARY',    '+254 726 300 091');
define('PHONE_SECONDARY',  '+254 105 628 524');
define('WHATSAPP_NUMBER',  '254726300091');
define('WHATSAPP_MESSAGE', 'Hello%20Harrison,%20I%20found%20your%20portfolio%20and%20I%20am%20interested%20in%20your%20web%20development%20services, system%20development%20services, and%20I%20would%20like%20to%20work%20with%20you .%20Please%20get%20back%20to%20me.');
define('WHATSAPP_LINK',    'https://wa.me/' . WHATSAPP_NUMBER . '?text=' . WHATSAPP_MESSAGE);

define('SOCIAL_LINKS', [
    'github'   => 'https://github.com/harrisonmuhoro',
    'linkedin' => 'https://www.linkedin.com/in/harrison-muhoro/',
    'facebook' => 'https://www.facebook.com/harrison.muhoro.1',
    'whatsapp' => WHATSAPP_LINK,
    'email'    => 'mailto:' . EMAIL_ADDRESS,
]);

// ─── PERSON ENTITY (schema.org) ──────────────────────────────────────────────
// One stable identity referenced by every JSON-LD node via @id. sameAs is
// limited to owner-verified public profiles — contact channels (email,
// WhatsApp) and unverified accounts are deliberately excluded.
define('PERSON_ID',      SITE_URL . '/#person');
define('PERSON_IMAGE',   SITE_URL . '/assets/images/harrison2.webp');
define('PERSON_SAME_AS', [SOCIAL_LINKS['github'], SOCIAL_LINKS['linkedin']]);

// ─── CV ───────────────────────────────────────────────────────────────────────
define('CV_PATH', SITE_URL . '/assets/docs/Harrison_Muhoro_Resume.pdf#toolbar=0&navpanes=0');

// ─── DATABASE ─────────────────────────────────────────────────────────────────
if (!defined('DB_HOST'))    define('DB_HOST',    getenv('DB_HOST')    ?: 'localhost');
if (!defined('DB_NAME'))    define('DB_NAME',    getenv('DB_NAME')    ?: 'portfolio_db');
if (!defined('DB_USER'))    define('DB_USER',    getenv('DB_USER')    ?: 'portfolio_user');
if (!defined('DB_PASS'))    define('DB_PASS',    getenv('DB_PASS')    ?: '');
define('DB_CHARSET', 'utf8mb4');

// ─── MAILER (Resend) ──────────────────────────────────────────────────────────
if (!defined('RESEND_API_KEY')) define('RESEND_API_KEY', getenv('RESEND_API_KEY') ?: '');
if (!defined('RESEND_API_URL')) define('RESEND_API_URL', getenv('RESEND_API_URL') ?: 'https://api.resend.com/emails');
if (!defined('MAIL_FROM'))      define('MAIL_FROM',      getenv('MAIL_FROM')      ?: 'no-reply@harrisonmuhoro.me');
if (!defined('MAIL_TO'))        define('MAIL_TO',        getenv('MAIL_TO')        ?: getenv('CONTACT_EMAIL') ?: 'zionmuhoro@gmail.com');

// ─── SEO ──────────────────────────────────────────────────────────────────────
define('SEO_KEYWORDS',   'Web Developer Kenya, PHP Developer Kenya, Full-Stack Developer Kenya, Website Developer Nyeri, Business System Developer Kenya, Harrison Muhoro, Software Developer Kenya, Technology Consultant Kenya, IT Consultant Nyeri');
define('SEO_AUTHOR',     OWNER_NAME);
define('SEO_ROBOTS',     'index, follow');
define('OG_IMAGE',       SITE_URL . '/assets/images/og-image.webp');
define('OG_TYPE',        'website');
define('TWITTER_CARD',   'summary_large_image');

// ─── IT SUPPORT & NETWORKING ─────────────────────────────────────────────────
// Server-side allowlists — the client never dictates these values.
define('IT_SUPPORT_CATEGORIES', ['network', 'troubleshooting', 'software', 'business', 'other']);
define('IT_SUPPORT_METHODS',    ['onsite', 'remote']);
define('IT_SUPPORT_LANGS',      ['en', 'sw']);

// ─── SECURITY ─────────────────────────────────────────────────────────────────
define('CSRF_TOKEN_NAME', 'csrf_token');
if (!defined('APP_SECRET')) {
    define('APP_SECRET', getenv('APP_SECRET') ?: 'fallback-change-in-production');
}
define('FORM_RATE_LIMIT', 3);
define('ALLOWED_FILE_TYPES', []);

// ─── RATE LIMITER STORAGE ────────────────────────────────────────────────────
// RATE_LIMIT_DIR:      required in production; persistent, writable, absolute
//                      path outside the public web root. Development may use
//                      safe local fallbacks.
// RATE_LIMIT_FAIL_MODE: production defaults to 'closed'; development defaults
//                      to 'open'. Explicit values may override either default.
if (!defined('RATE_LIMIT_DIR'))       define('RATE_LIMIT_DIR',       getenv('RATE_LIMIT_DIR') ?: '');
if (!defined('RATE_LIMIT_FAIL_MODE')) define('RATE_LIMIT_FAIL_MODE', getenv('RATE_LIMIT_FAIL_MODE') ?: (ENVIRONMENT === 'production' ? 'closed' : 'open'));
// PUBLIC_ROOT: optional explicit path to the served document root. Used by
// the rate limiter to verify RATE_LIMIT_DIR sits outside the public tree.
// When unset, DOCUMENT_ROOT is used; if neither resolves in production,
// rate-limit storage validation fails closed.
if (!defined('PUBLIC_ROOT'))          define('PUBLIC_ROOT',          getenv('PUBLIC_ROOT') ?: '');

// ─── ERROR HANDLING ───────────────────────────────────────────────────────────
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
    ini_set('expose_php', '0');
}

// ─── TIMEZONE ─────────────────────────────────────────────────────────────────
date_default_timezone_set('Africa/Nairobi');

// ─── SESSION ──────────────────────────────────────────────────────────────────
// Sessions are started lazily — only by routes/handlers that need them
// (CSRF tokens, language preference) — so public pages emit no PHPSESSID
// cookie and stay edge-cacheable. Always go through this helper so the
// hardened cookie parameters are applied consistently.
if (!function_exists('app_session_start')) {
    function app_session_start(): void {
        if (session_status() !== PHP_SESSION_NONE) {
            return;
        }
        $is_secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
        session_set_cookie_params([
            'lifetime' => 0,              // Session cookie (expires on browser close)
            'path'     => '/',
            // No 'domain' — host-only cookie. HTTP_HOST is request-controlled
            // (and may carry a port locally); the session belongs to the
            // exact host that issued it. Cross-subdomain sessions are not
            // needed anywhere in this app.
            'secure'   => $is_secure,      // Only send over HTTPS in production
            'httponly'  => true,           // Prevent JavaScript access
            'samesite'  => 'Lax',          // CSRF protection
        ]);
        session_start();
    }
}

unset($_is_local);
