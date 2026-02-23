<?php
/**
 * sections/skills.php
 */
require_once __DIR__ . '/../config/config.php';

$skill_categories = [
    [
        'title' => 'Frontend',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'skills' => [
            ['name' => 'HTML5', 'level' => 92, 'label' => 'Advanced'],
            ['name' => 'CSS3', 'level' => 88, 'label' => 'Advanced'],
            ['name' => 'JavaScript', 'level' => 72, 'label' => 'Intermediate'],
        ],
    ],
    [
        'title' => 'Backend',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>',
        'skills' => [
            ['name' => 'PHP (No frameworks)', 'level' => 80, 'label' => 'Proficient'],
        ],
    ],
    [
        'title' => 'Databases',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>',
        'skills' => [
            ['name' => 'MySQL', 'level' => 80, 'label' => 'Proficient'],
            ['name' => 'MongoDB', 'level' => 60, 'label' => 'Learning'],
        ],
    ],
    [
        'title' => 'Tools & Environment',
        'icon'  => '<svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg>',
        'skills' => [
            ['name' => 'Git & GitHub', 'level' => 78, 'label' => 'Proficient'],
            ['name' => 'VS Code', 'level' => 90, 'label' => 'Advanced'],
        ],
    ],
];

$roadmap = [
    ['label' => 'REST API Development',       'active' => true],
    ['label' => 'Scalable Backend Architecture', 'active' => true],
    ['label' => 'Authentication Systems (JWT/OAuth)', 'active' => false],
    ['label' => 'React.js',                   'active' => false],
    ['label' => 'Node.js / Express',          'active' => false],
    ['label' => 'Deployment Pipelines (CI/CD)', 'active' => false],
    ['label' => 'Docker & Containerization',  'active' => false],
    ['label' => 'Cloud Hosting (AWS / DigitalOcean)', 'active' => false],
];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/skills.css">

<section class="skills section" id="skills" aria-labelledby="skills-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Technical Expertise</p>
            <h2 class="section-header__title" id="skills-title">
                Skills & <em>Stack</em>
            </h2>
            <p class="section-header__subtitle">
                A focused skill set built through real project delivery — evolving toward complete full-stack capability.
            </p>
        </header>

        <div class="skills__grid">
            <?php foreach ($skill_categories as $i => $category): ?>
            <div class="skills__category reveal reveal-delay-<?= min($i + 1, 5) ?>">
                <div class="skills__category-header">
                    <div class="skills__category-icon"><?= $category['icon'] ?></div>
                    <h3 class="skills__category-title"><?= htmlspecialchars($category['title']) ?></h3>
                </div>
                <div class="skills__list">
                    <?php foreach ($category['skills'] as $skill): ?>
                    <div class="skill-item">
                        <div class="skill-item__header">
                            <span class="skill-item__name"><?= htmlspecialchars($skill['name']) ?></span>
                            <span class="skill-item__level"><?= htmlspecialchars($skill['label']) ?></span>
                        </div>
                        <div class="skill-item__bar" role="progressbar" aria-valuenow="<?= $skill['level'] ?>" aria-valuemin="0" aria-valuemax="100" aria-label="<?= htmlspecialchars($skill['name']) ?> proficiency <?= $skill['level'] ?>%">
                            <div class="skill-item__fill" style="--skill-level: <?= $skill['level'] ?>%"></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <!-- Roadmap -->
        <div class="skills__roadmap reveal">
            <h3 class="skills__roadmap-title">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                Learning Roadmap — Growing Into Full-Stack
            </h3>
            <div class="skills__roadmap-items">
                <?php foreach ($roadmap as $item): ?>
                <div class="roadmap-item <?= $item['active'] ? 'roadmap-item--active' : '' ?>">
                    <span class="roadmap-item__dot"></span>
                    <?= htmlspecialchars($item['label']) ?>
                    <?php if ($item['active']): ?>
                        <span class="badge badge--accent" style="margin-left: auto; font-size: 0.65rem;">In Progress</span>
                    <?php endif; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/skills.js" defer></script>
