<?php
/**
 * sections/projects.php
 */
require_once __DIR__ . '/../config/config.php';

$projects = [
    [
        'id'          => 'scotts-pork-city',
        'title'       => 'Scotts Pork City',
        'category'    => 'Business Advertising Website',
        'badge'       => 'Business',
        'badge_class' => 'business',
        'problem'     => '<strong>Problem solved:</strong> A local meat business needed an online presence to attract customers, display their menu, hours, and contact details — converting offline foot traffic into digital leads.',
        'stack'       => ['React.js', 'CSS', 'PHP'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
        'featured'    => false,
    ],
    [
        'id'          => 'deliverance-church',
        'title'       => 'Deliverance Church International',
        'category'    => 'Church Platform & Information System',
        'badge'       => 'Institutional',
        'badge_class' => 'institutional',
        'problem'     => '<strong>Problem solved:</strong> The church required a digital platform to advertise events, share sermons, and publish service schedules — enabling the congregation to stay connected online.',
        'stack'       => ['HTML', 'CSS', 'JavaScript', 'PHP'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
        'featured'    => false,
    ],
    [
        'id'          => 'cognitara-portal',
        'title'       => 'Student Internship & Attachment Portal',
        'category'    => 'Application Management System — Cognitara Technology',
        'badge'       => 'Institutional',
        'badge_class' => 'institutional',
        'problem'     => '<strong>Problem solved:</strong> Cognitara Technology needed a structured system to manage student internship applications — replacing manual processes with a digital portal that tracks applicants, statuses, and approvals.',
        'stack'       => ['HTML', 'CSS', 'JavaScript', 'PHP'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
        'featured'    => true,
    ],
    [
        'id'          => 'loan-management',
        'title'       => 'Loan Management System',
        'category'    => 'Loan Processing & Tracking — County Government',
        'badge'       => 'Government',
        'badge_class' => 'govt',
        'problem'     => '<strong>Problem solved:</strong> A county government required a secure, auditable loan management system for processing loan applications, disbursements, and repayment tracking — replacing fragile spreadsheet-based workflows with a robust web system.',
        'stack'       => ['HTML', 'CSS', 'JavaScript', 'PHP'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
        'featured'    => true,
    ],
];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/projects.css">

<section class="projects section" id="projects" aria-labelledby="projects-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Portfolio</p>
            <h2 class="section-header__title" id="projects-title">
                Real Work.<br><em>Real Impact.</em>
            </h2>
            <p class="section-header__subtitle">
                From business websites to government-grade data systems — each project solves a specific, real-world problem.
            </p>
        </header>

        <div class="projects__grid">
            <?php foreach ($projects as $i => $project): ?>
            <article
                class="project-card <?= $project['featured'] ? 'project-card--featured' : '' ?> reveal reveal-delay-<?= min($i + 1, 5) ?>"
                id="project-<?= htmlspecialchars($project['id']) ?>"
                aria-label="<?= htmlspecialchars($project['title']) ?>"
            >
                <!-- Thumbnail -->
                <div class="project-card__thumb">
                    <div class="project-card__thumb-placeholder">
                        <svg class="project-card__thumb-icon" width="80" height="80" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                    </div>
                    <span class="project-card__badge project-card__badge--<?= $project['badge_class'] ?>">
                        <?= htmlspecialchars($project['badge']) ?>
                    </span>
                </div>

                <!-- Body -->
                <div class="project-card__body">
                    <div class="project-card__category"><?= htmlspecialchars($project['category']) ?></div>
                    <h3 class="project-card__title"><?= htmlspecialchars($project['title']) ?></h3>
                    <p class="project-card__problem"><?= $project['problem'] ?></p>
                    <div class="project-card__stack">
                        <?php foreach ($project['stack'] as $tech): ?>
                        <span class="project-card__stack-tag"><?= htmlspecialchars($tech) ?></span>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Footer -->
                <footer class="project-card__footer">
                    <a href="<?= htmlspecialchars($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="project-card__link" aria-label="View <?= htmlspecialchars($project['title']) ?> on GitHub">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                        GitHub
                    </a>
                    <a href="<?= htmlspecialchars($project['demo']) ?>" target="_blank" rel="noopener noreferrer" class="project-card__link" aria-label="View live demo of <?= htmlspecialchars($project['title']) ?>">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/></svg>
                        Live Demo
                    </a>
                </footer>
            </article>
            <?php endforeach; ?>
        </div>

        <div class="section-header" style="margin-top: var(--space-12); margin-bottom: 0;">
            <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline btn--lg">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                View All on GitHub
            </a>
        </div>

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/projects.js" defer></script>
