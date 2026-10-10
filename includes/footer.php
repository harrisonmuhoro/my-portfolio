<?php
/**
 * footer.php — Colophon
 */
require_once __DIR__ . '/../config/config.php';
?>
<footer class="footer" role="contentinfo">
    <div class="container footer__container">

        <div class="footer__top">
            <div class="footer__brand">
                <a href="<?= BASE_PATH ?>/" class="footer__logo"><?= OWNER_NAME ?></a>
                <p class="footer__tagline">Software engineer building dependable systems in Nyeri, Kenya.</p>
            </div>

            <nav class="footer__nav" aria-label="Footer Navigation">
                <ul role="list">
                    <li><a href="<?= BASE_PATH ?>/#work">Work</a></li>
                    <li><a href="<?= BASE_PATH ?>/about">About</a></li>
                    <li><a href="<?= BASE_PATH ?>/projects">Projects</a></li>
                    <li><a href="<?= BASE_PATH ?>/services">Services</a></li>
                    <li><a href="<?= BASE_PATH ?>/contact">Contact</a></li>
                </ul>
            </nav>
        </div>

        <div class="footer__bottom">
            <p class="footer__copy">&copy; <?= date('Y') ?> <?= OWNER_NAME ?></p>
            <p class="footer__location">Nyeri, Kenya</p>
        </div>

    </div>
</footer>

<script src="<?= BASE_PATH ?>/assets/js/navigation.js" defer></script>
<script src="<?= BASE_PATH ?>/assets/js/global.js" defer></script>
</body>
</html>
