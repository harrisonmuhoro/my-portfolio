<?php

$page_css = ['services'];
require_once __DIR__ . '/../includes/header.php';

$services = [
    [
        'label'    => 'Custom web development',
        'desc'     => 'Full-stack applications from requirements to deployment. I write maintainable code in modern frameworks — not page builders.',
        'outcomes' => ['Business-critical systems', 'Admin desks', 'Multi-role portals', 'API integrations'],
    ],
    [
        'label'    => 'System architecture',
        'desc'     => 'Systems organisations can actually run: from loan desks for county governments to student portals for institutions.',
        'outcomes' => ['Scalable backends', 'Role-based access', 'Audit trails and reporting', 'Data that stays consistent'],
    ],
    [
        'label'    => 'Backend APIs',
        'desc'     => 'REST APIs in Laravel and PHP, ready for a mobile app, a third-party service, or a browser client.',
        'outcomes' => ['Token auth', 'Rate limiting and validation', 'Honest error handling', 'Documentation'],
    ],
    [
        'label'    => 'Maintenance and support',
        'desc'     => 'Keep an existing system alive: fixes, security patches, performance work, and the next feature.',
        'outcomes' => ['Security reviews', 'Performance profiling', 'Dependency updates', 'LAN and ops support'],
    ],
];
?>

<main id="main-content">
    <section class="services-page section" id="services">
        <div class="container">

            <header class="section-heading">
                <span class="section-heading__label">Services</span>
                <h1>What I take on</h1>
                <p class="services-page__sub">Businesses, institutions, and government desks that need software for a real operational problem — not a brochure site.</p>
            </header>

            <ol class="services-list">
                <?php foreach ($services as $i => $service): ?>
                <li class="service-row">
                    <span class="service-row__num"><?= str_pad((string) ($i + 1), 2, '0', STR_PAD_LEFT) ?></span>
                    <div>
                        <h2 class="service-row__title"><?= htmlspecialchars($service['label']) ?></h2>
                        <p class="service-row__desc"><?= htmlspecialchars($service['desc']) ?></p>
                        <p class="service-row__outcomes"><?= htmlspecialchars(implode(' · ', $service['outcomes'])) ?></p>
                    </div>
                </li>
                <?php endforeach; ?>
            </ol>

            <div class="services-page__cta">
                <h2>If you already know the problem, write.</h2>
                <p>We can talk through what you need built and whether I am the right person for it.</p>
                <a href="<?= BASE_PATH ?>/contact" class="btn btn--primary">Contact</a>
            </div>

        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
