<?php

$page_css = ['projects'];
require_once __DIR__ . '/../includes/header.php';
// Header includes navigation.php

$projects = [
    [
        'id'          => 'afya-hms',
        'title'       => 'Afya Hospital Management System',
        'category'    => 'Enterprise Healthcare Solution',
        'problem'     => 'Local healthcare facilities struggled with fragmented patient records, inefficient queue management, and inaccurate billing systems resulting in slow service delivery.',
        'solution'    => 'Engineered a unified Hospital Management System (HMS) that centralizes electronic health records, automates billing, and streamlines patient triage.',
        'impact'      => 'Designed to reduce duplicate record entry, speed patient movement between departments, and give administrators clearer operational visibility.',
        'stack'       => ['PHP', 'Laravel', 'React', 'MySQL'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
    ],
    [
        'id'          => 'kenai',
        'title'       => 'KenAI Platform',
        'category'    => 'NLP Analytics Dashboard',
        'problem'     => 'Researchers lacked an accessible, localized platform to analyze Swahili text sentiment and extract meaningful entities from regional unstructured data.',
        'solution'    => 'Developed KenAI, an NLP-driven dashboard that processes local text data, offering real-time sentiment analysis and structured data extraction pipelines.',
        'impact'      => 'Supports faster review of large text sets and clearer, more localized analytics for African-language data.',
        'stack'       => ['TypeScript', 'Node.js', 'Python', 'Redis'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
    ],
    [
        'id'          => 'cognitara-portal',
        'title'       => 'Student Internship Portal',
        'category'    => 'Application Management System',
        'problem'     => 'Cognitara Technology needed a structured system to manage student internship applications — replacing manual, error-prone processes.',
        'solution'    => 'Built a digital portal that tracks applicants, statuses, and approvals in a centralized dashboard with role-based access.',
        'impact'      => 'Streamlines application review by replacing manual, error-prone tracking with a centralized, role-aware dashboard.',
        'stack'       => ['HTML', 'CSS', 'JavaScript', 'PHP'],
        'github'      => SOCIAL_LINKS['github'],
        'demo'        => '#',
    ],
];
?>

<main id="main-content">
    <section class="projects-archive section">
        <div class="container">
            <header class="section-heading reveal">
                <span class="section-heading__label">Archive</span>
                <h2>All Projects</h2>
            </header>

            <div class="projects-archive__list">
                <?php foreach ($projects as $i => $project): ?>
                <article class="project-list-card reveal reveal-delay-<?= min($i, 3) ?>">
                    <div class="project-list-card__visual">
                        <div class="project-list-card__placeholder">
                            <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><line x1="3" y1="9" x2="21" y2="9"/><line x1="9" y1="21" x2="9" y2="9"/></svg>
                        </div>
                    </div>
                    <div class="project-list-card__content">
                        <div class="project-list-card__category"><?= htmlspecialchars($project['category']) ?></div>
                        <h3 class="project-list-card__title"><?= htmlspecialchars($project['title']) ?></h3>
                        
                        <div class="project-list-card__details">
                            <div class="project-list-card__detail">
                                <h4>Problem</h4>
                                <p><?= htmlspecialchars($project['problem']) ?></p>
                            </div>
                            <div class="project-list-card__detail">
                                <h4>Solution & Impact</h4>
                                <p><?= htmlspecialchars($project['solution']) ?> <?= htmlspecialchars($project['impact']) ?></p>
                            </div>
                        </div>

                        <div class="project-list-card__stack">
                            <?php foreach ($project['stack'] as $tech): ?>
                            <span class="badge"><?= htmlspecialchars($tech) ?></span>
                            <?php endforeach; ?>
                        </div>

                        <div class="project-list-card__actions">
                            <a href="<?= htmlspecialchars($project['demo']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--primary">View Live</a>
                            <a href="<?= htmlspecialchars($project['github']) ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">Source Code</a>
                        </div>
                    </div>
                </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
