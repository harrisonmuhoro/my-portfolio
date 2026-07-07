<?php
$page_css = ['home'];
require_once __DIR__ . '/../includes/header.php';

$case_studies = [
    [
        'id' => 'work',
        'label' => 'Featured Case Study',
        'title' => 'Afya HMS',
        'category' => 'Hospital Management System',
        'visual_label' => 'Hospital operations dashboard',
        'problem' => 'Healthcare teams need one reliable place to manage patient records, billing, queues, and department handoffs without duplicating data across paper files and spreadsheets.',
        'solution' => 'Afya HMS centralizes patient workflows into a role-aware system for front desk, clinical, billing, and administrative teams.',
        'architecture' => 'The product is structured around a Laravel API, React interface, MySQL data model, authentication boundaries, and modular service layers that keep clinical and finance workflows separate but connected.',
        'impact' => 'The system is designed to reduce repeat data entry, speed up patient movement through departments, and give administrators clearer operational visibility.',
        'decision' => 'I prioritized reliability and auditability over visual novelty because healthcare users need predictable workflows, clear permissions, and traceable records.',
        'challenges' => 'The hardest problems were modeling patient journeys across multiple departments, keeping billing data consistent, and designing screens that staff can use quickly under pressure.',
        'stack' => ['PHP', 'Laravel', 'React', 'MySQL'],
        'github' => SOCIAL_LINKS['github'],
        'demo' => BASE_PATH . '/projects#afya-hms',
        'icon' => 'dashboard',
        'reverse' => false,
    ],
    [
        'id' => 'kenai',
        'label' => 'Featured Case Study',
        'title' => 'KenAI',
        'category' => 'NLP Analytics Platform',
        'visual_label' => 'Language analytics dashboard',
        'problem' => 'Teams working with regional text data need a practical way to analyze Swahili sentiment and extract entities without forcing local data into generic tooling.',
        'solution' => 'KenAI packages NLP workflows into a dashboard that turns unstructured text into searchable, structured insights for researchers and data-driven organizations.',
        'architecture' => 'The platform separates the interface, API, queueing layer, cache, and Python NLP services so language models can evolve without destabilizing the product shell.',
        'impact' => 'The product direction supports faster review of large text sets, clearer reporting, and a more localized analytics workflow for African language data.',
        'decision' => 'I treated the ML service as a replaceable capability rather than the entire product, which keeps the system maintainable as models and datasets improve.',
        'challenges' => 'The main challenges were latency, language-specific preprocessing, and presenting model output in a way non-technical users can trust and act on.',
        'stack' => ['TypeScript', 'Node.js', 'Python', 'Redis'],
        'github' => SOCIAL_LINKS['github'],
        'demo' => BASE_PATH . '/projects#kenai',
        'icon' => 'analytics',
        'reverse' => true,
    ],
];

$stack_categories = [
    ['name' => 'Backend', 'items' => 'PHP, Laravel, Node.js, REST APIs', 'icon' => 'server'],
    ['name' => 'Frontend', 'items' => 'React, TypeScript, HTML5, CSS3', 'icon' => 'code'],
    ['name' => 'Mobile', 'items' => 'Responsive web apps, PWA-ready interfaces', 'icon' => 'mobile'],
    ['name' => 'Database', 'items' => 'MySQL, PostgreSQL, MongoDB', 'icon' => 'database'],
    ['name' => 'Cloud', 'items' => 'Azure, Docker, CI/CD, Nginx', 'icon' => 'cloud'],
    ['name' => 'Tools', 'items' => 'Git, GitHub, VS Code, Postman', 'icon' => 'tool'],
];

$currently_building = [
    ['title' => 'Afya HMS — v2', 'status' => 'In active development', 'desc' => 'Extending the hospital management system with clearer billing reconciliation and role-based audit trails.', 'stack' => ['Laravel', 'React', 'MySQL']],
    ['title' => 'KenAI — Swahili NLP', 'status' => 'Prototype', 'desc' => 'Improving language-specific preprocessing and making model output easier for non-technical users to trust.', 'stack' => ['Python', 'Node.js', 'Redis']],
];

$certifications = [
    ['title' => 'Responsive Web Design', 'issuer' => 'freeCodeCamp', 'year' => '2023'],
    ['title' => 'Back End Development and APIs', 'issuer' => 'freeCodeCamp', 'year' => '2024'],
    ['title' => 'Microsoft Azure Fundamentals (AZ-900)', 'issuer' => 'Microsoft', 'year' => 'In progress'],
];

function portfolio_icon(string $name, int $size = 24): string
{
    $icons = [
        'server' => '<rect x="3" y="4" width="18" height="6" rx="2"/><rect x="3" y="14" width="18" height="6" rx="2"/><line x1="7" y1="7" x2="7.01" y2="7"/><line x1="7" y1="17" x2="7.01" y2="17"/>',
        'code' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>',
        'mobile' => '<rect x="7" y="2" width="10" height="20" rx="2"/><line x1="11" y1="18" x2="13" y2="18"/>',
        'database' => '<ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/>',
        'cloud' => '<path d="M17.5 19H9a7 7 0 1 1 6.71-9h1.79a4.5 4.5 0 1 1 0 9Z"/>',
        'tool' => '<path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/>',
        'dashboard' => '<rect x="3" y="3" width="18" height="18" rx="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/>',
        'analytics' => '<polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/>',
        'email' => '<path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/>',
        'arrow' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>',
        'download' => '<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>',
    ];

    $path = $icons[$name] ?? $icons['code'];
    return '<svg width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">' . $path . '</svg>';
}
?>

<main id="main-content">
    <section class="hero-section" id="hero">
        <div class="container hero-container">
            <div class="hero__copy">
                <p class="hero__eyebrow reveal">Product-minded software engineer in Kenya</p>
                <h1 class="hero__headline reveal reveal-delay-1">Engineering robust systems for scale.</h1>
                <p class="hero__sentence reveal reveal-delay-2">I build production-ready web systems for organizations that need reliable software, clear workflows, and maintainable architecture.</p>
                <p class="hero__subheadline reveal reveal-delay-3">My work focuses on backend architecture, performant interfaces, and enterprise tools for healthcare, operations, and African business contexts.</p>
                <div class="hero__actions reveal reveal-delay-4">
                    <a href="#contact" class="btn btn--primary btn--lg">Start a conversation</a>
                    <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline btn--lg">View GitHub</a>
                </div>
            </div>

            <figure class="hero__photo reveal reveal-delay-2">
                <img src="<?= BASE_PATH ?>/assets/images/harrison2.jpg" alt="<?= OWNER_NAME ?>, software engineer in Kenya" width="520" height="640" loading="eager">
            </figure>
        </div>
    </section>

    <?php foreach ($case_studies as $index => $study): ?>
    <section class="case-study section<?= $index % 2 === 1 ? ' case-study--surface' : '' ?>" id="<?= htmlspecialchars($study['id']) ?>">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label"><?= htmlspecialchars($study['label']) ?></span>
                <h2><?= htmlspecialchars($study['title']) ?></h2>
                <p><?= htmlspecialchars($study['category']) ?></p>
            </header>

            <div class="case-study__grid<?= $study['reverse'] ? ' case-study__grid--reverse' : '' ?>">
                <div class="case-study__visual reveal">
                    <div class="case-study__screenshot" role="img" aria-label="<?= htmlspecialchars($study['visual_label']) ?>">
                        <div class="case-study__window">
                            <div class="case-study__window-bar"><span></span><span></span><span></span></div>
                            <div class="case-study__window-body">
                                <?= portfolio_icon($study['icon'], 52) ?>
                                <strong><?= htmlspecialchars($study['visual_label']) ?></strong>
                                <p><?= htmlspecialchars($study['category']) ?></p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="case-study__content reveal reveal-delay-1">
                    <?php foreach ([
                        'Problem' => $study['problem'],
                        'Solution' => $study['solution'],
                        'Architecture' => $study['architecture'],
                        'Impact' => $study['impact'],
                        'Decision making' => $study['decision'],
                        'Challenges solved' => $study['challenges'],
                    ] as $heading => $copy): ?>
                    <div class="case-study__block">
                        <h3><?= htmlspecialchars($heading) ?></h3>
                        <p><?= htmlspecialchars($copy) ?></p>
                    </div>
                    <?php endforeach; ?>

                    <div class="case-study__stack" aria-label="<?= htmlspecialchars($study['title']) ?> tech stack">
                        <?php foreach ($study['stack'] as $tech): ?>
                        <span class="badge"><?= htmlspecialchars($tech) ?></span>
                        <?php endforeach; ?>
                    </div>

                    <div class="case-study__actions">
                        <a href="<?= htmlspecialchars($study['demo']) ?>" class="btn btn--primary">Live Demo</a>
                        <a href="<?= htmlspecialchars($study['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">GitHub</a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <?php endforeach; ?>

    <section class="stack-section section" id="stack">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Capabilities</span>
                <h2>Technology Stack</h2>
            </header>

            <div class="stack-grid">
                <?php foreach ($stack_categories as $index => $category): ?>
                <div class="stack-card reveal reveal-delay-<?= min($index % 3, 2) ?>">
                    <div class="stack-card__icon"><?= portfolio_icon($category['icon']) ?></div>
                    <h3><?= htmlspecialchars($category['name']) ?></h3>
                    <p><?= htmlspecialchars($category['items']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="building-section building-section--surface section" id="building">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Currently Building</span>
                <h2>What I'm working on now</h2>
            </header>

            <div class="building-grid">
                <?php foreach ($currently_building as $index => $item): ?>
                <div class="building-card reveal reveal-delay-<?= min($index, 2) ?>">
                    <span class="building-card__status"><?= htmlspecialchars($item['status']) ?></span>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['desc']) ?></p>
                    <div class="building-card__stack" aria-label="<?= htmlspecialchars($item['title']) ?> tech stack">
                        <?php foreach ($item['stack'] as $tech): ?>
                        <span class="badge"><?= htmlspecialchars($tech) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- NOTE: Update certifications with real credentials. Placeholder content. -->
    <section class="cert-section section" id="certifications">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Certifications</span>
                <h2>Credentials &amp; learning</h2>
            </header>

            <div class="cert-grid">
                <?php foreach ($certifications as $index => $cert): ?>
                <div class="cert-card reveal reveal-delay-<?= min($index % 3, 2) ?>">
                    <h3 class="cert-card__title"><?= htmlspecialchars($cert['title']) ?></h3>
                    <p class="cert-card__issuer"><?= htmlspecialchars($cert['issuer']) ?></p>
                    <span class="cert-card__year"><?= htmlspecialchars($cert['year']) ?></span>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <section class="about-section section" id="about">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Identity</span>
                <h2>About</h2>
            </header>
            <div class="about-content reveal">
                <p class="text-large">I am a software engineer based in Kenya who builds production-ready enterprise systems for teams that need dependable software. I care about clean architecture, readable interfaces, and codebases that can survive real operational pressure. My work sits at the intersection of product design, backend reliability, and practical business outcomes. I am especially interested in systems that improve healthcare, operations, and access to digital infrastructure across Africa.</p>
            </div>
        </div>
    </section>

    <section class="contact-section section" id="contact">
        <div class="container">
            <div class="contact-grid">
                <div class="contact-header reveal">
                    <span class="section-heading__label">Contact</span>
                    <h2>Ready to discuss a role or system?</h2>
                    <p>Send the context, team need, or product problem. I will reply with a clear next step.</p>
                    <a href="<?= SOCIAL_LINKS['email'] ?>" class="btn btn--primary btn--lg contact-primary">Email Harrison</a>
                </div>

                <div class="contact-links reveal reveal-delay-1">
                    <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                        <span class="contact-link__icon"><?= portfolio_icon('code', 20) ?></span>
                        <span class="contact-link__text">GitHub</span>
                        <?= portfolio_icon('arrow', 16) ?>
                    </a>
                    <a href="<?= SOCIAL_LINKS['linkedin'] ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                        <span class="contact-link__icon"><?= portfolio_icon('server', 20) ?></span>
                        <span class="contact-link__text">LinkedIn</span>
                        <?= portfolio_icon('arrow', 16) ?>
                    </a>
                    <a href="<?= SOCIAL_LINKS['whatsapp'] ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                        <span class="contact-link__icon"><?= portfolio_icon('mobile', 20) ?></span>
                        <span class="contact-link__text">WhatsApp</span>
                        <?= portfolio_icon('arrow', 16) ?>
                    </a>
                    <a href="<?= SOCIAL_LINKS['email'] ?>" class="contact-link">
                        <span class="contact-link__icon"><?= portfolio_icon('email', 20) ?></span>
                        <span class="contact-link__text">Email</span>
                        <?= portfolio_icon('arrow', 16) ?>
                    </a>
                    <a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="contact-link">
                        <span class="contact-link__icon"><?= portfolio_icon('download', 20) ?></span>
                        <span class="contact-link__text">Download CV</span>
                        <?= portfolio_icon('arrow', 16) ?>
                    </a>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
