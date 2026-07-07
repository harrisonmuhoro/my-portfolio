<?php
/**
 * pages/services.php — Redesigned
 */
$page_css = ['services'];
require_once __DIR__ . '/../includes/header.php';

$services = [
    [
        'label'    => 'Custom Web Development',
        'desc'     => 'Full-stack web applications built from requirements to deployment. I write clean, maintainable code using modern frameworks — not page builders.',
        'outcomes' => ['Business-critical systems', 'Custom admin dashboards', 'Multi-role user portals', 'API integrations'],
        'icon'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    ],
    [
        'label'    => 'Enterprise System Architecture',
        'desc'     => 'Design and build systems that serve organizations at scale. From loan management for county governments to student portals for institutions.',
        'outcomes' => ['Scalable backend design', 'Role-based access control', 'Audit trails & reporting', 'Data integrity guarantees'],
        'icon'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 2L2 7l10 5 10-5-10-5z"/><path d="M2 17l10 5 10-5"/><path d="M2 12l10 5 10-5"/></svg>',
    ],
    [
        'label'    => 'Backend API Development',
        'desc'     => 'RESTful APIs built with Laravel and PHP, ready for mobile frontends, third-party services, or SPA clients.',
        'outcomes' => ['Token-based auth (JWT/OAuth)', 'Rate limiting & validation', 'Proper error handling', 'API documentation'],
        'icon'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
    ],
    [
        'label'    => 'Maintenance & Technical Support',
        'desc'     => 'Ongoing support for existing systems: bug fixes, security patches, performance improvements, and feature additions.',
        'outcomes' => ['Security audits', 'Performance profiling', 'Dependency updates', 'LAN configuration support'],
        'icon'     => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    ],
];
?>

<main id="main-content">
    <section class="services-page section" id="services">
        <div class="container">

            <header class="section-heading reveal">
                <span class="section-heading__label">Services</span>
                <h1>What I build</h1>
                <p class="services-page__sub">I work with businesses, institutions, and government bodies to engineer systems that solve real operational problems.</p>
            </header>

            <div class="services-page__grid">
                <?php foreach ($services as $i => $service): ?>
                <div class="service-item reveal reveal-delay-<?= min($i, 3) ?>">
                    <div class="service-item__icon"><?= $service['icon'] ?></div>
                    <h2 class="service-item__title"><?= htmlspecialchars($service['label']) ?></h2>
                    <p class="service-item__desc"><?= htmlspecialchars($service['desc']) ?></p>
                    <ul class="service-item__outcomes" aria-label="Outcomes for <?= htmlspecialchars($service['label']) ?>">
                        <?php foreach ($service['outcomes'] as $outcome): ?>
                        <li><?= htmlspecialchars($outcome) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- CTA -->
            <div class="services-page__cta reveal">
                <div class="services-page__cta-inner">
                    <h2>Have a project in mind?</h2>
                    <p>Let's talk through what you're building and how I can help.</p>
                    <a href="<?= BASE_PATH ?>/contact" class="btn btn--primary btn--lg">Start a Conversation</a>
                </div>
            </div>

        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
