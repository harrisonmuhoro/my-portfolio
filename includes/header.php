<?php
/**
 * Header Include
 * Renders <head> section with full SEO, OG, JSON-LD structured data
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/seo.php';

// Page-specific overrides (set before including header)
$page_title       = $page_title       ?? SITE_NAME . ' — ' . PRIMARY_TITLE;
$page_description = $page_description ?? SITE_DESCRIPTION;
$seo_keywords     = $seo_keywords     ?? SEO_KEYWORDS;

// Active UI language — honoured only on pages that opted into i18n
// (IT_I18N_ACTIVE), so html lang/og:locale always agree with content.
$page_lang  = $page_lang ?? (defined('IT_I18N_ACTIVE') && isset($lang) && in_array($lang, ['en', 'sw'], true) ? $lang : 'en');
$html_lang  = $page_lang === 'sw' ? 'sw-KE' : SITE_LANGUAGE;
$og_locale  = $page_lang === 'sw' ? 'sw_KE' : SITE_LOCALE;

// Clean REQUEST_URI — strip query strings from canonical
$clean_uri        = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
if (BASE_PATH !== '' && strpos($clean_uri, BASE_PATH) === 0) {
    $clean_uri = substr($clean_uri, strlen(BASE_PATH)) ?: '/';
}
$clean_uri        = $clean_uri === '/' ? '/' : '/' . trim($clean_uri, '/');

// Language-indexed pages (IT_I18N_ACTIVE) get per-language canonical URLs
// and reciprocal hreflang annotations so each locale is independently
// indexable. The active $lang is set by includes/i18n.php before header.
$hreflang_urls = [];
if (defined('IT_I18N_ACTIVE') && isset($lang) && in_array($lang, ['en', 'sw'], true)) {
    $hreflang_urls['en'] = SITE_URL . $clean_uri . '?lang=en';
    $hreflang_urls['sw'] = SITE_URL . $clean_uri . '?lang=sw';
    $hreflang_urls['x-default'] = $hreflang_urls['en'];
    $page_canonical = $hreflang_urls[$lang];
} else {
    $page_canonical = $page_canonical ?? SITE_URL . $clean_uri;
}
$page_og_image    = $page_og_image    ?? OG_IMAGE;

// Ensure BASE_PATH never has a trailing slash
$base = rtrim(BASE_PATH, '/');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($html_lang) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ── Primary SEO ── -->
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="keywords"    content="<?= htmlspecialchars($seo_keywords) ?>">
    <meta name="author"      content="<?= htmlspecialchars(SEO_AUTHOR) ?>">
    <meta name="robots"      content="<?= htmlspecialchars(SEO_ROBOTS) ?>">
    <link rel="canonical"    href="<?= htmlspecialchars($page_canonical) ?>">
    <?php foreach ($hreflang_urls as $hreflang => $hreflang_href): ?>
    <link rel="alternate" hreflang="<?= htmlspecialchars($hreflang) ?>" href="<?= htmlspecialchars($hreflang_href) ?>">
    <?php endforeach; ?>
    <link rel="sitemap"      type="application/xml" title="Sitemap" href="<?= $base ?>/sitemap.xml">

    <!-- ── Open Graph ── -->
    <meta property="og:type"        content="<?= htmlspecialchars(OG_TYPE) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($page_canonical) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($page_og_image) ?>">
    <meta property="og:locale"      content="<?= htmlspecialchars($og_locale) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME) ?>">

    <!-- ── Twitter Card ── -->
    <meta name="twitter:card"        content="<?= htmlspecialchars(TWITTER_CARD) ?>">
    <meta name="twitter:url"         content="<?= htmlspecialchars($page_canonical) ?>">
    <meta name="twitter:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($page_og_image) ?>">

    <!-- ── Favicon ── -->
    <link rel="icon" type="image/svg+xml"  href="<?= $base ?>/assets/icons/favicon.svg">
    <link rel="apple-touch-icon"           href="<?= $base ?>/assets/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#F4EFE6">

    <!-- ── Fonts ── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Fraunces:ital,opsz,wght@0,9..144,400;0,9..144,500;0,9..144,600;1,9..144,400&family=Source+Sans+3:wght@400;500;600&family=Source+Serif+4:ital,opsz,wght@0,8..60,400;0,8..60,500;0,8..60,600;1,8..60,400&display=swap" rel="stylesheet">

    <!-- ── Theme Persistence ── -->
    <script>
        (function() {
            document.documentElement.classList.add('js');
            const savedTheme = localStorage.getItem('theme');
            document.documentElement.setAttribute('data-theme', savedTheme === 'dark' ? 'dark' : 'light');
        })();
    </script>

    <!-- ── CSS Delivery ── -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/global.css?v=4">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/navigation.css?v=4">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/footer.css?v=4">

    <?php if (!empty($page_css) && is_array($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?= $base ?>/assets/css/<?= htmlspecialchars($css_file) ?>.css?v=4">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ── JSON-LD Structured Data (centralized Person entity) ── -->
    <?php seo_jsonld(seo_person_schema()); ?>
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<div class="scroll-progress" aria-hidden="true"><span class="scroll-progress__bar"></span></div>
<?php require_once __DIR__ . '/navigation.php'; ?>
