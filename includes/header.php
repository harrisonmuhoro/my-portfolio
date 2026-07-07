<?php
/**
 * Header Include
 * Renders <head> section with full SEO, OG, JSON-LD structured data
 */
require_once __DIR__ . '/../config/config.php';

// Page-specific overrides (set before including header)
$page_title       = $page_title       ?? SITE_NAME . ' — ' . PRIMARY_TITLE;
$page_description = $page_description ?? SITE_DESCRIPTION;

// Clean REQUEST_URI — strip query strings from canonical
$clean_uri        = strtok($_SERVER['REQUEST_URI'], '?');
$page_canonical   = $page_canonical   ?? rtrim(SITE_URL, '/') . $clean_uri;
$page_og_image    = $page_og_image    ?? OG_IMAGE;

// Ensure BASE_PATH never has a trailing slash
$base = rtrim(BASE_PATH, '/');
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars(SITE_LANGUAGE) ?>" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <!-- ── Primary SEO ── -->
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="keywords"    content="<?= htmlspecialchars(SEO_KEYWORDS) ?>">
    <meta name="author"      content="<?= htmlspecialchars(SEO_AUTHOR) ?>">
    <meta name="robots"      content="<?= htmlspecialchars(SEO_ROBOTS) ?>">
    <link rel="canonical"    href="<?= htmlspecialchars($page_canonical) ?>">

    <!-- ── Open Graph ── -->
    <meta property="og:type"        content="<?= htmlspecialchars(OG_TYPE) ?>">
    <meta property="og:url"         content="<?= htmlspecialchars($page_canonical) ?>">
    <meta property="og:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta property="og:image"       content="<?= htmlspecialchars($page_og_image) ?>">
    <meta property="og:locale"      content="<?= htmlspecialchars(SITE_LOCALE) ?>">
    <meta property="og:site_name"   content="<?= htmlspecialchars(SITE_NAME) ?>">

    <!-- ── Twitter Card ── -->
    <meta name="twitter:card"        content="<?= htmlspecialchars(TWITTER_CARD) ?>">
    <meta name="twitter:title"       content="<?= htmlspecialchars($page_title) ?>">
    <meta name="twitter:description" content="<?= htmlspecialchars($page_description) ?>">
    <meta name="twitter:image"       content="<?= htmlspecialchars($page_og_image) ?>">

    <!-- ── Favicon ── -->
    <link rel="icon" type="image/svg+xml"  href="<?= $base ?>/assets/icons/favicon.svg">
    <link rel="apple-touch-icon"           href="<?= $base ?>/assets/icons/apple-touch-icon.png">
    <meta name="theme-color" content="#0A1628">

    <!-- ── Fonts ── -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=JetBrains+Mono&family=Space+Grotesk:wght@500;700&display=swap" rel="stylesheet">

    <!-- ── Theme Persistence ── -->
    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme');
            const prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
            if (savedTheme === 'light' || (!savedTheme && prefersLight)) {
                document.documentElement.setAttribute('data-theme', 'light');
            }
        })();
    </script>

    <!-- ── CSS Delivery ── -->
    <link rel="stylesheet" href="<?= $base ?>/assets/css/global.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/navigation.css">
    <link rel="stylesheet" href="<?= $base ?>/assets/css/footer.css">

    <?php if (!empty($page_css) && is_array($page_css)): ?>
        <?php foreach ($page_css as $css_file): ?>
            <link rel="stylesheet" href="<?= $base ?>/assets/css/<?= htmlspecialchars($css_file) ?>.css">
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- ── JSON-LD Structured Data ── -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "Person",
        "name": <?= json_encode(OWNER_NAME) ?>,
        "jobTitle": <?= json_encode(PRIMARY_TITLE) ?>,
        "description": <?= json_encode(SITE_DESCRIPTION) ?>,
        "url": <?= json_encode(SITE_URL) ?>,
        "email": <?= json_encode(EMAIL_ADDRESS) ?>,
        "telephone": <?= json_encode(PHONE_PRIMARY) ?>,
        "address": {
            "@type": "PostalAddress",
            "addressLocality": "Nyeri",
            "addressCountry": "KE"
        },
        "sameAs": <?= json_encode(array_values(SOCIAL_LINKS)) ?>,
        "knowsAbout": ["HTML5", "CSS3", "JavaScript", "PHP", "MySQL", "MongoDB", "Web Development", "Full-Stack Development"]
    }
    </script>
</head>
<body>
<a href="#main-content" class="skip-link">Skip to main content</a>
<?php require_once __DIR__ . '/navigation.php'; ?>