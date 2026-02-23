<?php
/**
 * sections/about.php
 */
require_once __DIR__ . '/../config/config.php';
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/about.css">

<section class="about section" id="about" aria-labelledby="about-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Who I Am</p>
            <h2 class="section-header__title" id="about-title">
                Building with <em>purpose</em>
            </h2>
            <p class="section-header__subtitle">
                A results-driven web developer focused on delivering systems that serve businesses, institutions, and government bodies across Kenya.
            </p>
        </header>

        <div class="about__grid">

            <!-- Visual -->
            <div class="about__visual reveal">
                <div class="about__img-frame">
                    <div class="about__img-placeholder" aria-hidden="true">HM</div>
                </div>
                <div class="about__badge">
                    <div class="about__badge-num">100%</div>
                    <span class="about__badge-label">Client Focused</span>
                </div>
            </div>

            <!-- Content -->
            <div class="about__content">

                <p class="about__bio reveal">
                    I'm <strong><?= OWNER_NAME ?></strong>, a Web Developer based in <strong>Nyeri, Kenya</strong>, currently growing into a Full-Stack Engineer. My journey started with a mission: use technology to solve real business problems — not just build websites, but engineer systems that work.
                </p>

                <p class="about__bio reveal reveal-delay-1">
                    I've delivered projects for <strong>private businesses</strong>, <strong>churches</strong>, <strong>tech startups</strong>, and most notably, <strong>county government systems</strong>. These experiences have shaped my approach to development — clean code, structured thinking, and solutions built to scale.
                </p>

                <p class="about__bio reveal reveal-delay-2">
                    My backend of choice is <strong>PHP with MySQL</strong>, complemented by clean HTML, CSS, and JavaScript on the front. I'm currently expanding my stack into modern backend architecture, API development, and deployment pipelines — positioning myself as a complete full-stack engineer.
                </p>

                <div class="about__values reveal reveal-delay-3">
                    <div class="about__value">
                        <div class="about__value-title">Business First</div>
                        <div class="about__value-desc">Every line of code should serve a business purpose. I design with outcomes in mind.</div>
                    </div>
                    <div class="about__value">
                        <div class="about__value-title">Built to Scale</div>
                        <div class="about__value-desc">Systems that grow with your organization, not break under it.</div>
                    </div>
                    <div class="about__value">
                        <div class="about__value-title">Clean Architecture</div>
                        <div class="about__value-desc">Structured, maintainable code that other developers can understand and extend.</div>
                    </div>
                    <div class="about__value">
                        <div class="about__value-title">Reliability</div>
                        <div class="about__value-desc">Deadlines met. Promises kept. Systems that stay online and perform.</div>
                    </div>
                </div>

                <div class="reveal reveal-delay-4">
                    <a href="<?= SOCIAL_LINKS['github'] ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                        View GitHub: Ziha546
                    </a>
                </div>

            </div><!-- /about__content -->

        </div><!-- /about__grid -->

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/about.js" defer></script>
