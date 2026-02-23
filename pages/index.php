<?php
/**
 * pages/index.php — Main Portfolio Homepage
 * Harrison Muhoro — Web Developer & Full-Stack Developer in Training
 */

require_once __DIR__ . '/../config/config.php';

// ── Start session BEFORE any HTML output ──────────────────
// Must be here so contact.php CSRF token works without warnings
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Page-specific SEO
$page_title       = OWNER_NAME . ' — Web Developer Kenya | PHP & Full-Stack Developer in Training';
$page_description = 'Harrison Muhoro is a Web Developer based in Nairobi, Kenya, specializing in PHP, MySQL, and business-focused web systems. Delivering websites and management systems for businesses, institutions, and government bodies.';
$page_canonical   = SITE_URL . '/';

require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content">

    <?php require_once __DIR__ . '/../sections/hero.php'; ?>

    <?php require_once __DIR__ . '/../sections/about.php'; ?>

    <?php require_once __DIR__ . '/../sections/skills.php'; ?>

    <?php require_once __DIR__ . '/../sections/projects.php'; ?>

    <?php require_once __DIR__ . '/../sections/services.php'; ?>

    <?php require_once __DIR__ . '/../sections/experience.php'; ?>

    <?php require_once __DIR__ . '/../sections/testimonials.php'; ?>

    <?php require_once __DIR__ . '/../sections/contact.php'; ?>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
