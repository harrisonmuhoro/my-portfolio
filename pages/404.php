<?php

$page_title       = '404 — Page Not Found | ' . SITE_NAME;
$page_description = 'The page you are looking for does not exist.';
require_once __DIR__ . '/../includes/header.php';
?>

<main id="main-content" class="not-found-page">
    <div class="container not-found-page__inner reveal-stagger">
        <p class="not-found-page__code">404</p>
        <h1 class="not-found-page__title reveal">This page is not here</h1>
        <p class="not-found-page__desc reveal">
            It may have moved, or the address is wrong. The work and contact pages still are.
        </p>
        <div class="not-found-page__actions reveal">
            <a href="<?= BASE_PATH ?>/" class="btn btn--primary">Home</a>
            <a href="<?= BASE_PATH ?>/contact" class="btn btn--outline">Contact</a>
        </div>
    </div>
</main>

<style>
.not-found-page {
    min-height: 70vh;
    display: flex;
    align-items: center;
    padding-block: var(--space-24);
}
.not-found-page__inner { max-width: 32rem; }
.not-found-page__code {
    font-family: var(--font-heading);
    font-size: var(--text-sm);
    color: var(--clr-accent);
    margin-bottom: var(--space-4);
}
.not-found-page__title {
    font-size: clamp(var(--text-3xl), 6vw, var(--text-5xl));
    margin-bottom: var(--space-4);
    font-weight: 500;
}
.not-found-page__desc {
    color: var(--clr-text-muted);
    margin-bottom: var(--space-8);
    font-size: var(--text-lg);
}
.not-found-page__actions {
    display: flex;
    gap: var(--space-4);
    flex-wrap: wrap;
}
</style>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
