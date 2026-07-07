<?php
/**
 * pages/about.php — Redesigned: concise bio + icon-based stack + no timelines
 */
$page_css = ['about'];
require_once __DIR__ . '/../includes/header.php';
// navigation.php is included at the bottom of header.php

$stack_categories = [
    [
        'name'  => 'Backend',
        'items' => ['PHP', 'Laravel', 'Node.js', 'REST APIs'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
    ],
    [
        'name'  => 'Frontend',
        'items' => ['React', 'TypeScript', 'HTML5', 'CSS3'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
    ],
    [
        'name'  => 'Database',
        'items' => ['MySQL', 'PostgreSQL', 'MongoDB'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
    ],
    [
        'name'  => 'Cloud & Ops',
        'items' => ['Microsoft Azure', 'Docker', 'CI/CD', 'Nginx'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/></svg>',
    ],
    [
        'name'  => 'Networking',
        'items' => ['LAN Configuration', 'Infrastructure Setup'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="8" rx="2"/><rect x="2" y="14" width="20" height="8" rx="2"/><line x1="6" y1="6" x2="6.01" y2="6"/><line x1="6" y1="18" x2="6.01" y2="18"/></svg>',
    ],
    [
        'name'  => 'Tools',
        'items' => ['Git & GitHub', 'VS Code', 'Postman', 'WAMP'],
        'icon'  => '<svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
    ],
];
?>

<main id="main-content">

    <!-- ── ABOUT ── -->
    <section class="about-page section" id="about">
        <div class="container">

            <div class="about-page__grid">
                <div class="about-page__bio reveal">
                    <span class="section-heading__label">Identity</span>
                    <h1 class="about-page__name"><?= OWNER_NAME ?></h1>
                    <p class="about-page__title"><?= PRIMARY_TITLE ?>, Kenya</p>

                    <p class="about-page__para">
                        I am a software engineer specializing in building production-ready enterprise systems for businesses, institutions, and government bodies across Kenya. My engineering philosophy is rooted in solving real problems — not writing code for the sake of it.
                    </p>
                    <p class="about-page__para">
                        I architect backends with PHP and Laravel, build reactive frontends with React and TypeScript, and deploy on Azure and cloud infrastructure. I have delivered systems for county governments, tech startups, and private enterprises — each one measured by the business outcomes it produces.
                    </p>
                    <p class="about-page__para">
                        I care deeply about system design, code maintainability, and the user experience of the people who rely on what I build every day.
                    </p>

                    <div class="about-page__cta">
                        <a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">Download CV</a>
                        <a href="<?= BASE_PATH ?>/#contact" class="btn btn--outline">Get in Touch</a>
                    </div>
                </div>

                <div class="about-page__photo reveal reveal-delay-1">
                    <div class="about-page__photo-frame">
                        <img
                            src="<?= BASE_PATH ?>/assets/images/harrison2.jpg"
                            alt="<?= OWNER_NAME ?> — Software Engineer, Kenya"
                            class="about-page__img"
                            width="480"
                            height="480"
                            loading="eager"
                        >
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ── STACK ── -->
    <section class="stack-page section" id="stack" style="background-color: var(--clr-bg-surface);">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Capabilities</span>
                <h2>Technology Stack</h2>
            </header>

            <div class="stack-page__grid">
                <?php foreach ($stack_categories as $i => $cat): ?>
                <div class="stack-page__card reveal reveal-delay-<?= min($i, 4) ?>">
                    <div class="stack-page__card-header">
                        <span class="stack-page__card-icon"><?= $cat['icon'] ?></span>
                        <h3><?= htmlspecialchars($cat['name']) ?></h3>
                    </div>
                    <ul class="stack-page__items" aria-label="<?= htmlspecialchars($cat['name']) ?> technologies">
                        <?php foreach ($cat['items'] as $item): ?>
                        <li><?= htmlspecialchars($item) ?></li>
                        <?php endforeach; ?>
                    </ul>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- ── CURRENTLY BUILDING ── -->
    <section class="building-section section" id="building">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">What's Next</span>
                <h2>Currently Building</h2>
            </header>

            <div class="building-grid">
                <div class="building-card reveal">
                    <div class="building-card__status">
                        <span class="building-card__dot"></span>
                        In Progress
                    </div>
                    <h3>Afya HMS</h3>
                    <p>A hospital management system centralizing electronic health records, automated billing, and patient triage for healthcare facilities in Kenya.</p>
                    <div class="building-card__stack">
                        <span class="badge">Laravel</span>
                        <span class="badge">React</span>
                        <span class="badge">MySQL</span>
                    </div>
                </div>

                <div class="building-card reveal reveal-delay-1">
                    <div class="building-card__status">
                        <span class="building-card__dot"></span>
                        In Progress
                    </div>
                    <h3>KenAI Platform</h3>
                    <p>An NLP analytics dashboard for Swahili text sentiment analysis and entity extraction, built for researchers and data-driven organizations.</p>
                    <div class="building-card__stack">
                        <span class="badge">TypeScript</span>
                        <span class="badge">Python</span>
                        <span class="badge">Node.js</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
