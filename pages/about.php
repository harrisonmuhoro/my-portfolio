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
                <div class="about-page__bio reveal-stagger">
                    <span class="section-heading__label">About</span>
                    <h1 class="about-page__name reveal"><?= OWNER_NAME ?></h1>
                    <p class="about-page__title"><?= PRIMARY_TITLE ?>, Nyeri</p>

                    <p class="about-page__para reveal">
                        I'm Harrison, a software developer from Kenya focused on creating practical digital solutions for businesses and organizations.
                    </p>
                    <p class="about-page__para reveal">
                        I build systems that help teams replace repetitive manual work, organize information, manage daily operations, and deliver better services to the people they serve.
                    </p>
                    <p class="about-page__para reveal">
                        My approach is simple: understand how the work is actually done, identify what can be improved, and build software that makes the process easier without making it unnecessarily complicated.
                    </p>
                    <p class="about-page__para reveal">
                        From healthcare and hospitality to education and business operations, I enjoy turning challenging workflows into reliable, easy-to-use digital solutions.
                    </p>
                    <p class="about-page__para reveal">
                        I care about readable architecture and leaving a team better than I found it. The best system is the one someone else can still understand six months after it ships.
                    </p>

                    <div class="about-page__cta reveal">
                        <a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">Download CV</a>
                        <a href="<?= BASE_PATH ?>/contact" class="btn btn--outline">Write to me</a>
                    </div>
                </div>

                <figure class="about-page__photo reveal reveal--image">
                    <img
                        src="<?= BASE_PATH ?>/assets/images/harrison2.webp"
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
            <header class="section-heading reveal">
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
            <header class="section-heading reveal">
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
