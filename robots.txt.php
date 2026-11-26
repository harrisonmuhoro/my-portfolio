<?php
/**
 * robots.txt.php — Dynamic robots.txt
 * Tells search engine crawlers which areas to index and references the sitemap.
 */

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__);
}

require_once PROJECT_ROOT . '/config/config.php';

if (!headers_sent()) {
    header('Content-Type: text/plain; charset=UTF-8');
    header('Cache-Control: public, max-age=86400');
}

$baseUrl = rtrim(SITE_URL, '/');
?>
User-agent: *
Allow: /

# Disallow internal and vendor directories
Disallow: /backend/
Disallow: /config/
Disallow: /vendor/
Disallow: /includes/

# Private rate-limit counter storage (not access control — the directory
# also carries its own .htaccess deny; this just keeps crawlers out)
Disallow: /storage/

# Sitemap
Sitemap: <?= $baseUrl ?>/sitemap.xml
