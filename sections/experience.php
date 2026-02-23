<?php
/**
 * sections/experience.php
 */
require_once __DIR__ . '/../config/config.php';

$timeline = [
    [
        'date'   => '2026 — Present',
        'title'  => 'Freelance Web Developer',
        'org'    => 'Independent Practice — Nyeri, Kenya',
        'desc'   => 'Building custom websites and systems for private businesses, churches, and organizations. Working directly with clients from requirement gathering through deployment.',
        'active' => true,
    ],
    [
        'date'   => '2025',
        'title'  => 'Web Developer Intern',
        'org'    => 'Cognitara Technology',
        'desc'   => 'Developed the Student Internship & Attachment Portal — a full web application managing student applications, statuses, and approvals for the company.',
        'active' => false,
    ],
    [
        'date'   => '2025',
        'title'  => 'Government System Developer',
        'org'    => 'County Government — Kenya',
        'desc'   => 'Contributed to the design and development of a Loan Management System for a county government body, handling loan processing, tracking, and reporting.',
        'active' => false,
    ],
];

$credibility = [
    [
        'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M3 9l9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z"/><polyline points="9 22 9 12 15 12 15 22"/></svg>',
        'value' => '4+',
        'label' => 'Real-world projects delivered',
    ],
    [
        'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>',
        'value' => '2+',
        'label' => 'Government & institutional systems',
    ],
    [
        'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
        'value' => '3+',
        'label' => 'Distinct client verticals served',
    ],
    [
        'icon'  => '<svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>',
        'value' => '100%',
        'label' => 'Projects delivered on time',
    ],
];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/experience.css">

<section class="experience section" id="experience" aria-labelledby="experience-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Track Record</p>
            <h2 class="section-header__title" id="experience-title">
                Experience &amp; <em>Credibility</em>
            </h2>
            <p class="section-header__subtitle">
                Real projects. Real clients. A growing track record across business, institutional, and government domains.
            </p>
        </header>

        <div class="experience__grid">

            <!-- Timeline -->
            <div class="experience__timeline reveal">
                <?php foreach ($timeline as $item): ?>
                <div class="timeline-item <?= $item['active'] ? 'timeline-item--active' : '' ?>">
                    <div class="timeline-item__date"><?= htmlspecialchars($item['date']) ?></div>
                    <h3 class="timeline-item__title"><?= htmlspecialchars($item['title']) ?></h3>
                    <div class="timeline-item__org"><?= htmlspecialchars($item['org']) ?></div>
                    <p class="timeline-item__desc"><?= htmlspecialchars($item['desc']) ?></p>
                </div>
                <?php endforeach; ?>
            </div>

            <!-- Credibility Stats -->
            <div class="experience__credibility">
                <?php foreach ($credibility as $i => $stat): ?>
                <div class="credibility-stat reveal reveal-delay-<?= $i + 1 ?>">
                    <div class="credibility-stat__icon"><?= $stat['icon'] ?></div>
                    <div>
                        <div class="credibility-stat__value"><?= htmlspecialchars($stat['value']) ?></div>
                        <div class="credibility-stat__label"><?= htmlspecialchars($stat['label']) ?></div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>

        </div>

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/experience.js" defer></script>
