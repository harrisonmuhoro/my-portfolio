<?php
/**
 * footer.php — Colophon
 */
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/i18n.php';
?>
<footer class="footer reveal" role="contentinfo">
    <div class="container footer__container">

        <div class="footer__top">
            <div class="footer__brand">
                <a href="<?= BASE_PATH ?>/" class="footer__logo"><?= OWNER_NAME ?></a>
                <p class="footer__tagline"><?= htmlspecialchars(t('footer_tagline')) ?></p>
            </div>

            <nav class="footer__nav" aria-label="Footer Navigation">
                <ul role="list">
                    <li><a href="<?= BASE_PATH ?>/#work"><?= htmlspecialchars(t('nav_work')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/about"><?= htmlspecialchars(t('nav_about')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/projects"><?= htmlspecialchars(t('nav_projects')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/services"><?= htmlspecialchars(t('nav_services')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/it-support"><?= htmlspecialchars(t('nav_it_support')) ?></a></li>
                    <li><a href="<?= BASE_PATH ?>/contact"><?= htmlspecialchars(t('nav_contact')) ?></a></li>
                </ul>
            </nav>
        </div>

        <div class="footer__bottom">
            <p class="footer__copy">&copy; <?= date('Y') ?> <?= OWNER_NAME ?></p>
            <p class="footer__location"><?= htmlspecialchars(t('footer_location')) ?></p>
        </div>

    </div>
</footer>

<script src="<?= BASE_PATH ?>/assets/js/navigation.js" defer></script>
<script src="<?= BASE_PATH ?>/assets/js/global.js" defer></script>
</body>
</html>
