<?php
$page_css = ['home'];
require_once __DIR__ . '/../includes/header.php';

$case_studies = [
    [
        'id'        => 'afya-hms',
        'number'    => '01',
        'title'     => 'Afya HMS',
        'category'  => 'Hospital management',
        'lede'      => 'One system for front desk, clinic, pharmacy, lab, and admin — so patient flow and billing are no longer a paper chase.',
        'problem'   => 'Staff were juggling paper files, spreadsheets, and verbal handoffs. Billing errors were common, queues were invisible, and nobody had a single picture of the hospital.',
        'solution'  => 'Afya HMS gives each role clear permissions and a live view of patients and money. It is built as a Laravel 11 modular monolith with a React/TypeScript PWA, MySQL, Redis, and live updates across branches.',
        'quote'     => 'I chose a modular monolith over microservices because healthcare workflows are deeply connected. Splitting them early would have made the system harder to audit and harder to keep.',
        'impact'    => 'Designed for 1,000–10,000+ concurrent users across branches. Duplicate entry goes away; administrators can see operations as they happen.',
        'stack'     => ['Laravel 11', 'PHP 8.3', 'React 18', 'TypeScript', 'MySQL 8', 'Redis', 'M-Pesa', 'PWA'],
        'github'    => 'https://github.com/harrisonmuhoro/afyahms',
        'demo'      => BASE_PATH . '/projects#afya-hms',
    ],
    [
        'id'        => 'taara-hms',
        'number'    => '02',
        'title'     => 'Taara HMS',
        'category'  => 'Hotel operations',
        'lede'      => 'Reservations, housekeeping, restaurant POS, and M-Pesa on one desk — including the reconciliation that used to live in a bank SMS thread.',
        'problem'   => 'Hotel teams ran the property in separate tools. Payments arrived as messages. End-of-day finance was a manual pile-up.',
        'solution'  => 'Taara HMS is a Laravel 11 server-rendered app with Blade, TypeScript/Vite, Daraja STK Push and C2B, and queued PDF/CSV exports. It stays usable on old POS terminals, tablets, and phones.',
        'quote'     => 'I kept it server-rendered instead of a full SPA because hotel staff work on mixed, often old hardware. The app has to load there, not only on a developer laptop.',
        'impact'    => 'Automatic M-Pesa matching, audit trails on financial changes, and rate limits that protect staff accounts from abuse.',
        'stack'     => ['Laravel 11', 'PHP 8.3', 'Blade', 'TypeScript', 'MySQL 8', 'M-Pesa Daraja'],
        'github'    => 'https://github.com/harrisonmuhoro/Taara-HMS',
        'demo'      => BASE_PATH . '/projects#taara-hms',
    ],
];

$stack_categories = [
    ['name' => 'Backend',  'items' => 'PHP 8.3, Laravel 11, Node.js, REST APIs'],
    ['name' => 'Frontend', 'items' => 'React 18, TypeScript, Blade, HTML, CSS'],
    ['name' => 'Mobile',   'items' => 'Progressive Web Apps, phones first'],
    ['name' => 'Data',     'items' => 'MySQL 8, Redis, PostgreSQL'],
    ['name' => 'Cloud',    'items' => 'Azure, Docker, CI/CD, Nginx'],
    ['name' => 'Tools',    'items' => 'Git, GitHub, VS Code, Postman, WAMP'],
];

$currently_building = [
    ['title' => 'InduTrack KE', 'status' => 'In development', 'desc' => 'An industrial attachment portal for Kenya: six roles, tamper-locked digital logbooks, and queued PDF certificates.', 'stack' => ['Laravel 11', 'React', 'Vite', 'MySQL']],
    ['title' => 'Afya HMS — v2', 'status' => 'Ongoing', 'desc' => 'Clearer billing, better EMR screens, and role-based audit trails for multi-branch reporting.', 'stack' => ['Laravel 11', 'React 18', 'MySQL']],
];

$certifications = [
    ['title' => 'Responsive Web Design', 'issuer' => 'freeCodeCamp', 'year' => '2023'],
    ['title' => 'Back End Development and APIs', 'issuer' => 'freeCodeCamp', 'year' => '2024'],
    ['title' => 'Microsoft Azure Fundamentals (AZ-900)', 'issuer' => 'Microsoft', 'year' => 'In progress'],
];
?>

<main id="main-content">
    <section class="hero-section" id="hero">
        <div class="container hero-container">
            <div class="hero__copy reveal-stagger">
                <p class="hero__eyebrow reveal">Software engineer · Nyeri, Kenya</p>
                <h1 class="hero__headline reveal">Digital solutions built for the way your business works.</h1>
                <p class="hero__sentence reveal">I build reliable digital systems that make everyday work simpler, faster, and easier to manage.</p>
                <p class="hero__sentence reveal">From hospitals and hotels to schools and businesses, I create software that helps teams manage their operations, reduce paperwork, organize information, and serve their customers better. I focus on building solutions that work reliably in the real world—not just impressive demos.</p>
                <p class="hero__subheadline reveal">I take complex business processes and turn them into clear, practical digital experiences. Whether you need a new system, want to improve an existing one, or have a process that is still running on paper, I build software around the way your organization actually works.</p>
                <div class="hero__actions reveal">
                    <a href="#contact" class="btn btn--primary">Write to me</a>
                    <a href="<?= BASE_PATH ?>/projects" class="btn btn--outline">See the work</a>
                </div>
            </div>

            
            <figure class="hero__photo reveal reveal--image">
                <img src="<?= BASE_PATH ?>/assets/images/harrison2.jpg" alt="<?= OWNER_NAME ?>, software engineer in Kenya" width="520" height="640" loading="eager">
                <figcaption><?= OWNER_NAME ?>, photographed in Kenya</figcaption>
            </figure>
        </div>
    </section>

    <section class="work-index section" id="work">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Selected work</span>
                <h2>Two systems that had to work in the real place, not only in a repo.</h2>
            </header>
        </div>
    </section>

    <?php foreach ($case_studies as $study): ?>
    <article class="case-study reveal" id="<?= htmlspecialchars($study['id']) ?>">
        <div class="container case-study__inner">
            <div class="case-study__kicker">
                <span class="case-study__number"><?= htmlspecialchars($study['number']) ?></span>
                <span class="case-study__category"><?= htmlspecialchars($study['category']) ?></span>
            </div>
            <h2 class="case-study__title"><?= htmlspecialchars($study['title']) ?></h2>
            <p class="case-study__lede"><?= htmlspecialchars($study['lede']) ?></p>

            <div class="case-study__body">
                <div>
                    <h3>The problem</h3>
                    <p><?= htmlspecialchars($study['problem']) ?></p>
                </div>
                <div>
                    <h3>What I built</h3>
                    <p><?= htmlspecialchars($study['solution']) ?></p>
                </div>
                <div>
                    <h3>What changed</h3>
                    <p><?= htmlspecialchars($study['impact']) ?></p>
                </div>
            </div>

            <blockquote class="case-study__quote">
                <p><?= htmlspecialchars($study['quote']) ?></p>
            </blockquote>

            <p class="case-study__stack"><?= htmlspecialchars(implode(' · ', $study['stack'])) ?></p>

            <div class="case-study__actions">
                <a href="<?= htmlspecialchars($study['demo']) ?>" class="btn btn--primary">Read the project</a>
                <a href="<?= htmlspecialchars($study['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">GitHub</a>
            </div>
        </div>
    </article>
    <?php endforeach; ?>

    <section class="stack-section section" id="stack">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Tools I use</span>
                <h2>A working stack, not a shopping list.</h2>
            </header>

            <dl class="stack-list reveal-group">
                <?php foreach ($stack_categories as $category): ?>
                <div class="stack-list__row reveal">
                    <dt><?= htmlspecialchars($category['name']) ?></dt>
                    <dd><?= htmlspecialchars($category['items']) ?></dd>
                </div>
                <?php endforeach; ?>
            </dl>
        </div>
    </section>

    <section class="building-section section" id="building">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">On the desk</span>
                <h2>What I am building now</h2>
            </header>

            <ul class="index-list reveal-group">
                <?php foreach ($currently_building as $item): ?>
                <li class="index-list__item reveal">
                    <span class="index-list__meta"><?= htmlspecialchars($item['status']) ?></span>
                    <h3><?= htmlspecialchars($item['title']) ?></h3>
                    <p><?= htmlspecialchars($item['desc']) ?></p>
                    <p class="index-list__stack"><?= htmlspecialchars(implode(' · ', $item['stack'])) ?></p>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="cert-section section" id="certifications">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Learning</span>
                <h2>Certificates</h2>
            </header>

            <ul class="cert-list reveal-group">
                <?php foreach ($certifications as $cert): ?>
                <li class="reveal">
                    <span class="cert-list__year"><?= htmlspecialchars($cert['year']) ?></span>
                    <span class="cert-list__title"><?= htmlspecialchars($cert['title']) ?></span>
                    <span class="cert-list__issuer"><?= htmlspecialchars($cert['issuer']) ?></span>
                </li>
                <?php endforeach; ?>
            </ul>
        </div>
    </section>

    <section class="about-section section" id="about">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">About</span>
                <h2>From Nyeri, building for places that still run on paper.</h2>
            </header>
            <div class="about-content reveal-stagger">
                <p>I'm Harrison. I got into this because I kept watching hospitals, hotels, government desks, and universities lose time to problems software could actually fix. So I started fixing them.</p>
                <p>I work mostly in PHP and Laravel on the server, React and TypeScript in the browser. I like code that is predictable six months later, when somebody else has to change it at 11pm.</p>
                <p>On my own time I am building InduTrack KE, because I lived through Kenya's industrial attachment process and it was a mess of paper logbooks and guesswork.</p>
                <p><a href="<?= BASE_PATH ?>/about" class="btn btn--text">More about me</a></p>
            </div>
        </div>
    </section>

    <section class="contact-section section" id="contact">
        <div class="container contact-close reveal-stagger">
            <span class="section-heading__label reveal">Contact</span>
            <h2 class="reveal">If you have a system that has to hold up, write.</h2>
            <p class="reveal">Roles, contracts, or a messy operational problem — I will answer with a clear next step, usually within a weekday.</p>
            <div class="contact-close__links reveal-group">
                <a href="<?= SOCIAL_LINKS['email'] ?>">Email</a>
                <a href="<?= SOCIAL_LINKS['whatsapp'] ?>" target="_blank" rel="noopener noreferrer">WhatsApp</a>
                <a href="<?= SOCIAL_LINKS['linkedin'] ?>" target="_blank" rel="noopener noreferrer">LinkedIn</a>
                <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer">GitHub</a>
                <a href="<?= BASE_PATH ?>/contact">Full contact page</a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
