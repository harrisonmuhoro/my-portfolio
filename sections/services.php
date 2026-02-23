<?php
/**
 * sections/services.php
 */
require_once __DIR__ . '/../config/config.php';

$services = [
    [
        'icon'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'title'    => 'Website Development',
        'desc'     => 'Custom websites built from scratch — corporate sites, business landing pages, organizational platforms, and more. Clean code, fast loading, and fully responsive.',
        'features' => ['Responsive design (mobile-first)', 'SEO-optimized structure', 'Custom UI/UX design', 'Performance optimization', 'Ongoing maintenance'],
    ],
    [
        'icon'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
        'title'    => 'Business & Organizational Systems',
        'desc'     => 'Custom management systems for businesses and institutions — loan systems, portals, trackers, and workflow automation tools built to your exact requirements.',
        'features' => ['Government-grade systems', 'Student/client portals', 'Loan & finance tracking', 'Inventory management', 'Role-based access control'],
    ],
    [
        'icon'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'title'    => 'Backend Integration',
        'desc'     => 'Solid PHP and database backends that power your web applications. From form handling to full CRUD systems — secure, sanitized, and production-ready.',
        'features' => ['PHP backend (no frameworks)', 'MySQL database design', 'MongoDB integration', 'Secure form handling', 'Input validation & sanitization'],
    ],
    [
        'icon'     => '<svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'title'    => 'System Maintenance & Support',
        'desc'     => 'Keeping your existing systems healthy, updated, and running. Bug fixes, feature additions, performance improvements, and security patches.',
        'features' => ['Bug fixes & debugging', 'Feature enhancements', 'Security patching', 'Code refactoring', 'Technical documentation'],
    ],
];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/services.css">

<section class="services section" id="services" aria-labelledby="services-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">What I Offer</p>
            <h2 class="section-header__title" id="services-title">
                Services Built for <em>Business</em>
            </h2>
            <p class="section-header__subtitle">
                Practical web development services tailored for Kenyan businesses, government bodies, and institutions that need technology that actually works.
            </p>
        </header>

        <div class="services__grid">
            <?php foreach ($services as $i => $service): ?>
            <div class="service-card reveal reveal-delay-<?= min($i + 1, 5) ?>">
                <div class="service-card__icon"><?= $service['icon'] ?></div>
                <h3 class="service-card__title"><?= htmlspecialchars($service['title']) ?></h3>
                <p class="service-card__desc"><?= htmlspecialchars($service['desc']) ?></p>
                <div class="service-card__features">
                    <?php foreach ($service['features'] as $feature): ?>
                    <div class="service-card__feature">
                        <span class="service-card__feature-dot" aria-hidden="true"></span>
                        <?= htmlspecialchars($feature) ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- CTA Banner -->
        <div class="services__cta reveal">
            <h3 class="services__cta-title">Ready to build something <em style="font-style:italic; color:var(--clr-accent);">that works</em>?</h3>
            <p class="services__cta-subtitle">Reach out and let's discuss your project. Whether you need a business website, a management system, or backend integration — let's talk.</p>
            <div class="services__cta-actions">
                <a href="<?= SOCIAL_LINKS['whatsapp'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary btn--lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                    WhatsApp Me
                </a>
                <a href="/#contact" class="btn btn--outline btn--lg">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Send a Message
                </a>
            </div>
        </div>

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/services.js" defer></script>
