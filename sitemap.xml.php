<?php
/**
 * sitemap.xml.php — Dynamic XML Sitemap
 * Generates an SEO-compliant sitemap with verified routes,
 * accurate lastmod timestamps, and Google Image extension support.
 */

if (!defined('PROJECT_ROOT')) {
    define('PROJECT_ROOT', __DIR__);
}

require_once PROJECT_ROOT . '/config/config.php';

// Ensure clean base URL without trailing slash
$baseUrl = rtrim(SITE_URL, '/');

// Helper to determine last modified date from file or fallback to current date
if (!function_exists('getPageLastMod')) {
    function getPageLastMod(string $filePath): string {
        if (file_exists($filePath)) {
            $mtime = filemtime($filePath);
            if ($mtime !== false) {
                return date('Y-m-d', $mtime);
            }
        }
        return date('Y-m-d');
    }
}

// Portfolio sitemap entries
$pages = [
    [
        'loc'        => $baseUrl . '/',
        'file'       => PROJECT_ROOT . '/pages/home-redesign.php',
        'changefreq' => 'weekly',
        'priority'   => '1.0',
        'images'     => [
            [
                'loc'     => $baseUrl . '/assets/images/og-image.jpg',
                'title'   => SITE_NAME . ' — ' . PRIMARY_TITLE,
                'caption' => SITE_DESCRIPTION,
            ],
        ],
    ],
    [
        'loc'        => $baseUrl . '/about',
        'file'       => PROJECT_ROOT . '/pages/about.php',
        'changefreq' => 'monthly',
        'priority'   => '0.8',
        'images'     => [
            [
                'loc'     => $baseUrl . '/assets/images/harrison2.jpg',
                'title'   => OWNER_NAME . ' — ' . PRIMARY_TITLE . ', Kenya',
                'caption' => OWNER_NAME . ' — Software Engineer biography photo',
            ],
        ],
    ],
    [
        'loc'        => $baseUrl . '/projects',
        'file'       => PROJECT_ROOT . '/pages/projects.php',
        'changefreq' => 'weekly',
        'priority'   => '0.9',
    ],
    [
        'loc'        => $baseUrl . '/services',
        'file'       => PROJECT_ROOT . '/pages/services.php',
        'changefreq' => 'monthly',
        'priority'   => '0.8',
    ],
    [
        'loc'        => $baseUrl . '/contact',
        'file'       => PROJECT_ROOT . '/pages/contact.php',
        'changefreq' => 'monthly',
        'priority'   => '0.7',
    ],
];

// Send headers only when not running from CLI
if (!headers_sent()) {
    header('Content-Type: application/xml; charset=UTF-8');
    header('Cache-Control: public, max-age=86400, stale-while-revalidate=3600');
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9"
        xmlns:image="http://www.google.com/schemas/sitemap-image/1.1">
<?php foreach ($pages as $page): ?>
    <url>
        <loc><?= htmlspecialchars($page['loc'], ENT_XML1, 'UTF-8') ?></loc>
        <lastmod><?= getPageLastMod($page['file']) ?></lastmod>
        <changefreq><?= htmlspecialchars($page['changefreq'], ENT_XML1, 'UTF-8') ?></changefreq>
        <priority><?= htmlspecialchars($page['priority'], ENT_XML1, 'UTF-8') ?></priority>
<?php if (!empty($page['images'])): ?>
<?php foreach ($page['images'] as $image): ?>
        <image:image>
            <image:loc><?= htmlspecialchars($image['loc'], ENT_XML1, 'UTF-8') ?></image:loc>
<?php if (!empty($image['title'])): ?>
            <image:title><?= htmlspecialchars($image['title'], ENT_XML1, 'UTF-8') ?></image:title>
<?php endif; ?>
<?php if (!empty($image['caption'])): ?>
            <image:caption><?= htmlspecialchars($image['caption'], ENT_XML1, 'UTF-8') ?></image:caption>
<?php endif; ?>
        </image:image>
<?php endforeach; ?>
<?php endif; ?>
    </url>
<?php endforeach; ?>
</urlset>
