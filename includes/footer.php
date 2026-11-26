<?php
/**
 * footer.php — Colophon
 *
 * Footer chrome is translated only on pages that opt in to i18n by
 * defining IT_I18N_ACTIVE — matching the navigation behaviour.
 */
require_once __DIR__ . '/../config/config.php';

$tr = function (string $key, string $en): string {
    return (defined('IT_I18N_ACTIVE') && function_exists('t')) ? t($key) : $en;
};
?>
<footer class="footer reveal" role="contentinfo">
    <div class="container footer__container">

        <div class="footer__top">
            <div class="footer__brand">
                <a href="<?= BASE_PATH ?>/" class="footer__logo"><?= OWNER_NAME ?></a>
                <p class="footer__tagline"><?= htmlspecialchars($tr('footer_tagline', 'Software developer and technology consultant building dependable systems in Nyeri, Kenya.')) ?></p>
            </div>

            <nav class="footer__nav" aria-label="Footer Navigation">
                <ul role="list">
                    <li><a href="<?= BASE_PATH ?>/#work"><?= htmlspecialchars($tr('nav_work', 'Work')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/about"><?= htmlspecialchars($tr('nav_about', 'About')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/projects"><?= htmlspecialchars($tr('nav_projects', 'Projects')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/services"><?= htmlspecialchars($tr('nav_services', 'Services')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/it-support"><?= htmlspecialchars($tr('nav_it_support', 'IT Support')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/contact"><?= htmlspecialchars($tr('nav_contact', 'Contact')) ?></a></li>
                </ul>
            </nav>
        </div>

        <div class="footer__bottom">
            <p class="footer__copy">&copy; <?= date('Y') ?> <?= OWNER_NAME ?></p>
            <p class="footer__location"><?= htmlspecialchars($tr('footer_location', 'Nyeri, Kenya')) ?></p>
        </div>

    </div>
</footer>

<script src="<?= BASE_PATH ?>/assets/js/navigation.js" defer></script>
<script src="<?= BASE_PATH ?>/assets/js/global.js" defer></script>
</body>
</html>
