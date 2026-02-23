<?php
/**
 * sections/hero.php — Hero section
 */
require_once __DIR__ . '/../config/config.php';
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/hero.css">

<section class="hero grid-bg" id="home" aria-label="Introduction">
    <div class="container">
        <div class="hero__grid">

            <!-- ── LEFT: Content ── -->
            <div class="hero__content">

                <div class="hero__meta reveal">
                    <span class="hero__status">
                        <span class="hero__status-dot" aria-hidden="true"></span>
                        Available for projects
                    </span>
                    <span class="hero__location" aria-label="Location: Nairobi, Kenya">
                        <svg width="12" height="12" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z"/></svg>
                        Nairobi, Kenya
                    </span>
                </div>

                <h1 class="hero__name reveal reveal-delay-1">
                    <span class="hero__name-line1">Harrison</span>
                    <span class="hero__name-line2">Muhoro</span>
                </h1>

                <div class="hero__titles reveal reveal-delay-2">
                    <span class="hero__title-primary"><?= PRIMARY_TITLE ?></span>
                    <span class="hero__title-divider" aria-hidden="true"></span>
                    <span class="hero__title-secondary"><?= SECONDARY_TITLE ?></span>
                </div>

                <p class="hero__tagline reveal reveal-delay-3">
                    <?= SITE_TAGLINE ?> — From business websites to government-grade management systems, I build web solutions that solve real problems.
                </p>

                <div class="hero__cta reveal reveal-delay-4">
                    <a href="/pages/contact.php" class="btn btn--primary btn--lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        Hire Me
                    </a>
                    <a href="/#projects" class="btn btn--outline btn--lg">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><rect x="2" y="3" width="20" height="14" rx="2" ry="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
                        View Projects
                    </a>
                    <a href="<?= CV_PATH ?>" class="btn btn--ghost" download aria-label="Download CV">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                        Download CV
                    </a>
                </div>

                <div class="hero__quick-contact reveal reveal-delay-5">
                    <span class="hero__quick-contact-label">Reach me:</span>
                    <div class="hero__social-icons">
                        <a href="<?= SOCIAL_LINKS['whatsapp'] ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="hero__social-icon"
                           aria-label="Chat on WhatsApp">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                        </a>
                        <a href="<?= SOCIAL_LINKS['email'] ?>"
                           class="hero__social-icon"
                           aria-label="Send an email">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        </a>
                        <a href="<?= SOCIAL_LINKS['github'] ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="hero__social-icon"
                           aria-label="GitHub Profile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M12 0C5.37 0 0 5.37 0 12c0 5.31 3.435 9.795 8.205 11.385.6.105.825-.255.825-.57 0-.285-.015-1.23-.015-2.235-3.015.555-3.795-.735-4.035-1.41-.135-.345-.72-1.41-1.23-1.695-.42-.225-1.02-.78-.015-.795.945-.015 1.62.87 1.845 1.23 1.08 1.815 2.805 1.305 3.495.99.105-.78.42-1.305.765-1.605-2.67-.3-5.46-1.335-5.46-5.925 0-1.305.465-2.385 1.23-3.225-.12-.3-.54-1.53.12-3.18 0 0 1.005-.315 3.3 1.23.96-.27 1.98-.405 3-.405s2.04.135 3 .405c2.295-1.56 3.3-1.23 3.3-1.23.66 1.65.24 2.88.12 3.18.765.84 1.23 1.905 1.23 3.225 0 4.605-2.805 5.625-5.475 5.925.435.375.81 1.095.81 2.22 0 1.605-.015 2.895-.015 3.3 0 .315.225.69.825.57A12.02 12.02 0 0 0 24 12c0-6.63-5.37-12-12-12z"/></svg>
                        </a>
                        <a href="<?= SOCIAL_LINKS['linkedin'] ?>"
                           target="_blank"
                           rel="noopener noreferrer"
                           class="hero__social-icon"
                           aria-label="LinkedIn Profile">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                        </a>
                    </div>
                </div>
            </div><!-- /hero__content -->

            <!-- ── RIGHT: Visual ── -->
            <div class="hero__visual" aria-hidden="true">

                <div class="hero__card reveal">
                    <div class="hero__card-title">Current Stack</div>
                    <div class="hero__stack">
                        <span class="hero__stack-item">HTML5</span>
                        <span class="hero__stack-item">CSS3</span>
                        <span class="hero__stack-item">JavaScript</span>
                        <span class="hero__stack-item">PHP</span>
                        <span class="hero__stack-item">MySQL</span>
                        <span class="hero__stack-item">MongoDB</span>
                        <span class="hero__stack-item">GitHub</span>
                        <span class="hero__stack-item">VS Code</span>
                    </div>
                </div>

                <div class="hero__stats reveal reveal-delay-2">
                    <div class="hero__stat">
                        <div class="hero__stat-value">4+</div>
                        <div class="hero__stat-label">Live Projects</div>
                    </div>
                    <div class="hero__stat">
                        <div class="hero__stat-value">2+</div>
                        <div class="hero__stat-label">Gov. Systems</div>
                    </div>
                    <div class="hero__stat">
                        <div class="hero__stat-value">1yr+</div>
                        <div class="hero__stat-label">Experience</div>
                    </div>
                </div>

                <div class="hero__card reveal reveal-delay-3">
                    <div class="hero__card-title">Currently Learning</div>
                    <div class="hero__stack">
                        <span class="hero__stack-item">REST APIs</span>
                        <span class="hero__stack-item">React.js</span>
                        <span class="hero__stack-item">Node.js</span>
                        <span class="hero__stack-item">DevOps</span>
                    </div>
                </div>

            </div><!-- /hero__visual -->

        </div><!-- /hero__grid -->
    </div><!-- /container -->

    <!-- Scroll indicator -->
    <div class="hero__scroll" aria-hidden="true">
        <span>Scroll</span>
        <span class="hero__scroll-line"></span>
    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/hero.js" defer></script>
