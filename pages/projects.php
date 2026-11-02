<?php

$page_css = ['projects'];
require_once __DIR__ . '/../includes/header.php';

$projects = [
    [
        'id'            => 'afya-hms',
        'title'         => 'Afya HMS',
        'category'      => 'Hospital management',
        'year'          => '2024–2025',
        'tagline'       => 'A multi-branch hospital system for Kenyan healthcare.',
        'problem'       => 'Facilities were drowning in paper and fragmented records. Front desk, labs, and pharmacy had no shared system — every handoff was friction, and billing errors were routine.',
        'what_i_built'  => 'I designed the domain boundaries, wired the Laravel 11 API, built the React/TypeScript PWA, implemented live patient queues with Laravel Reverb, and integrated M-Pesa.',
        'impact'        => 'Built to scale from 1,000 to 10,000+ concurrent users across branches. Duplicate entry goes away, and administrators get operational visibility they did not have before.',
        'stack'         => ['Laravel 11', 'PHP 8.3', 'React 18', 'TypeScript', 'MySQL 8', 'Redis', 'Laravel Reverb', 'M-Pesa API', 'PWA'],
        'github'        => 'https://github.com/harrisonmuhoro/afyahms',
        'demo'          => '#',
    ],
    [
        'id'            => 'taara-hms',
        'title'         => 'Taara HMS',
        'category'      => 'Hotel operations',
        'year'          => '2024–2025',
        'tagline'       => 'Front desk to back-office finance, including M-Pesa.',
        'problem'       => 'Hotel teams juggled reservations, housekeeping, restaurant POS, and finance in disconnected tools. Cash, card, and M-Pesa lived in different places.',
        'what_i_built'  => 'The whole system: Laravel 11 and Blade, Safaricom Daraja for STK Push and C2B, queued PDF/CSV exports, and audit logging on every financial change.',
        'impact'        => 'Hotels stop copy-pasting payment confirmations. Rate limits and audit trails protect against insider abuse and brute-force attacks.',
        'stack'         => ['Laravel 11', 'PHP 8.3', 'Blade', 'TypeScript', 'MySQL 8', 'M-Pesa Daraja API', 'Vite'],
        'github'        => 'https://github.com/harrisonmuhoro/Taara-HMS',
        'demo'          => '#',
    ],
    [
        'id'            => 'indutrack-ke',
        'title'         => 'InduTrack KE',
        'category'      => 'Industrial attachment',
        'year'          => '2024–2025',
        'tagline'       => 'Digitising Kenya’s student placement process.',
        'problem'       => 'Attachment in Kenya still runs on paper logbooks, cold-call applications, and no institutional visibility. Logs can be faked. Companies get unvetted piles of applicants.',
        'what_i_built'  => 'A six-role system (student, company, supervisors, institution admin, super admin), Laravel 11 API and React/Vite frontend, Africa’s Talking SMS, and queued PDFs for certificates and logbooks.',
        'impact'        => 'Institutions get live placement tracking, compliance escalation, and exportable reports — instead of a process that ran on paper and trust.',
        'stack'         => ['Laravel 11', 'React', 'Vite', 'TypeScript', 'MySQL 8', 'Laravel Sanctum', "Africa's Talking", 'Redis'],
        'github'        => 'https://github.com/harrisonmuhoro/InduTrack-Ke',
        'demo'          => '#',
    ],
    [
        'id'            => 'vuka-portal',
        'title'         => 'Vuka Attachment Portal',
        'category'      => 'Government internship',
        'year'          => '2024',
        'tagline'       => 'From application to deployment for a government internship desk.',
        'problem'       => 'Departments had no digital workflow. Applications arrived by hand, document checks were manual, and supervisors could not see where a candidate stood.',
        'what_i_built'  => 'Authentication with email verification, Bearer sessions, four-tier RBAC with department isolation, Chart.js dashboards, CSV exports, and skeleton loaders throughout.',
        'impact'        => 'A paper internship process becomes a logged, auditable one. Documents stay with authorised staff only.',
        'stack'         => ['PHP', 'Laravel', 'MySQL', 'JavaScript', 'Chart.js'],
        'github'        => 'https://github.com/harrisonmuhoro/vuka_attachment_portal',
        'demo'          => '#',
    ],
];
?>

<main id="main-content">
    <section class="projects-archive section">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Projects</span>
                <h1>Four systems I designed, built, and shipped</h1>
                <p class="section-heading__sub">Each one solves a concrete operational problem in Kenya — hospitals, hotels, students, government desks.</p>
            </header>

            <ol class="projects-toc reveal-group">
                <?php foreach ($projects as $i => $project): ?>
                <li class="reveal">
                    <a href="#<?= htmlspecialchars($project['id']) ?>">
                        <span class="projects-toc__year"><?= htmlspecialchars($project['year']) ?></span>
                        <span class="projects-toc__title"><?= htmlspecialchars($project['title']) ?></span>
                        <span class="projects-toc__line"><?= htmlspecialchars($project['tagline']) ?></span>
                    </a>
                </li>
                <?php endforeach; ?>
            </ol>

            <div class="projects-archive__list">
                <?php foreach ($projects as $i => $project): ?>
                <article class="project-article reveal" id="<?= htmlspecialchars($project['id']) ?>">
                    <p class="project-article__meta">
                        <span><?= htmlspecialchars($project['year']) ?></span>
                        <span><?= htmlspecialchars($project['category']) ?></span>
                    </p>
                    <h2 class="project-article__title"><?= htmlspecialchars($project['title']) ?></h2>
                    <p class="project-article__tagline"><?= htmlspecialchars($project['tagline']) ?></p>

                    <div class="project-article__details">
                        <div>
                            <h3>The problem</h3>
                            <p><?= htmlspecialchars($project['problem']) ?></p>
                        </div>
                        <div>
                            <h3>What I built</h3>
                            <p><?= htmlspecialchars($project['what_i_built']) ?></p>
                        </div>
                        <div>
                            <h3>What changed</h3>
                            <p><?= htmlspecialchars($project['impact']) ?></p>
                        </div>
                    </div>

                    <p class="project-article__stack"><?= htmlspecialchars(implode(' · ', $project['stack'])) ?></p>

                    <div class="project-article__actions">
                        <a href="<?= htmlspecialchars($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">
                            View on GitHub
                        </a>
                        <?php if ($project['demo'] !== '#'): ?>
                        <a href="<?= htmlspecialchars($project['demo']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">
                            Live demo
                        </a>
                        <?php endif; ?>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>

            <div class="projects-github-cta reveal">
                <p>The code is open. Read the READMEs, or write if you want to talk through any of them.</p>
                <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">
                    GitHub
                </a>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
