<?php

$page_title       = '404 — Page Not Found | ' . SITE_NAME;
$page_description = 'The page you are looking for does not exist.';
require_once __DIR__ . '/../includes/header.php';
// navigation.php is included inside header.php
?>

<main id="main-content" class="not-found-page">
    <div class="container not-found-page__inner">
        <span class="not-found-page__code">404</span>
        <h1 class="not-found-page__title">Page not found</h1>
        <p class="not-found-page__desc">
            The page you're looking for doesn't exist or has been moved.
        </p>
        <div class="not-found-page__actions">
            <a href="<?= BASE_PATH ?>/" class="btn btn--primary btn--lg">Return Home</a>
            <a href="<?= BASE_PATH ?>/contact" class="btn btn--outline btn--lg">Contact Me</a>
        </div>
    </div>
</main>

<style>
.not-found-page {
    min-height: calc(100vh - 70px);
    display: flex;
    align-items: center;
    justify-content: center;
    text-align: center;
    padding-top: 70px;
}
.not-found-page__inner { max-width: 560px; }
.not-found-page__code {
    display: block;
    font-family: var(--font-heading);
    font-size: clamp(5rem, 18vw, 8rem);
    font-weight: 700;
    color: var(--clr-text-base);
    opacity: 0.08;
    line-height: 1;
    letter-spacing: -0.05em;
    margin-bottom: var(--space-4);
}
.not-found-page__title {
    font-size: var(--text-3xl);
    margin-bottom: var(--space-4);
}
.not-found-page__desc {
    color: var(--clr-text-muted);
    margin-bottom: var(--space-8);
    font-size: var(--text-lg);
}
.not-found-page__actions {
    display: flex;
    gap: var(--space-4);
    justify-content: center;
    flex-wrap: wrap;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
