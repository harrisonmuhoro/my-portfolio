<?php

$page_css = ['about'];
require_once __DIR__ . '/../includes/header.php';

$stack_categories = [
    ['name' => 'Backend',     'items' => ['PHP', 'Laravel', 'Node.js', 'REST APIs']],
    ['name' => 'Frontend',    'items' => ['React', 'TypeScript', 'HTML5', 'CSS3']],
    ['name' => 'Database',    'items' => ['MySQL', 'PostgreSQL', 'MongoDB']],
    ['name' => 'Cloud & ops', 'items' => ['Microsoft Azure', 'Docker', 'CI/CD', 'Nginx']],
    ['name' => 'Networking',  'items' => ['LAN configuration', 'Infrastructure setup']],
    ['name' => 'Tools',       'items' => ['Git & GitHub', 'VS Code', 'Postman', 'WAMP']],
];
?>

<main id="main-content">

    <section class="about-page section" id="about">
        <div class="container">

            <div class="about-page__grid">
                <div class="about-page__bio">
                    <span class="section-heading__label">About</span>
                    <h1 class="about-page__name"><?= OWNER_NAME ?></h1>
                    <p class="about-page__title"><?= PRIMARY_TITLE ?>, Nyeri</p>

                    <p class="about-page__para">
                        I'm Harrison — a software engineer from Nyeri, Kenya. I got into this because I kept seeing organisations struggle with problems software could actually solve: hospitals losing records between departments, hotels reconciling M-Pesa by hand at close of day, students navigating an attachment system that ran on paper and trust.
                    </p>
                    <p class="about-page__para">
                        I work primarily in PHP and Laravel on the backend and React and TypeScript on the frontend. Healthcare, hospitality, and education have all taught me the same lesson: the software has to hold up when real people hit it under pressure.
                    </p>
                    <p class="about-page__para">
                        I care about readable architecture and leaving a team better than I found it. The best system is the one someone else can still understand six months after it ships.
                    </p>

                    <div class="about-page__cta">
                        <a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">Download CV</a>
                        <a href="<?= BASE_PATH ?>/contact" class="btn btn--outline">Write to me</a>
                    </div>
                </div>

                <figure class="about-page__photo">
                    <img
                        src="<?= BASE_PATH ?>/assets/images/harrison2.jpg"
                        alt="<?= OWNER_NAME ?> — software engineer, Kenya"
                        class="about-page__img"
                        width="480"
                        height="600"
                        loading="eager"
                    >
                    <figcaption>Harrison Muhoro</figcaption>
                </figure>
            </div>
        </div>
    </section>

    <section class="stack-page section" id="stack">
        <div class="container">
            <header class="section-heading">
                <span class="section-heading__label">Tools</span>
                <h2>What I reach for</h2>
            </header>

            <dl class="stack-inventory">
                <?php foreach ($stack_categories as $cat): ?>
                <div class="stack-inventory__row">
                    <dt><?= htmlspecialchars($cat['name']) ?></dt>
                    <dd><?= htmlspecialchars(implode(', ', $cat['items'])) ?></dd>
                </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </section>

    <section class="building-section section" id="building">
        <div class="container">
            <header class="section-heading">
                <span class="section-heading__label">On the desk</span>
                <h2>Currently building</h2>
            </header>

            <ul class="index-list">
                <li class="index-list__item">
                    <span class="index-list__meta">In development</span>
                    <h3>InduTrack KE</h3>
                    <p>A full-stack industrial attachment portal for Kenya — six roles, tamper-locked digital logbooks, escalation pipelines, and PDF certificates.</p>
                    <p class="index-list__stack">Laravel 11 · React · Vite · MySQL</p>
                </li>
                <li class="index-list__item">
                    <span class="index-list__meta">Ongoing</span>
                    <h3>Afya HMS — v2</h3>
                    <p>Clearer EMR workflows, billing reconciliation, and role-based audit trails for multi-branch hospital reporting.</p>
                    <p class="index-list__stack">Laravel 11 · React 18 · MySQL</p>
                </li>
            </ul>
        </div>
    </section>

</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
