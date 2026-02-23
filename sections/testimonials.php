<?php
/**
 * sections/testimonials.php
 */
require_once __DIR__ . '/../config/config.php';

$testimonials = [
    [
        'text'    => 'Harrison delivered a professional, well-structured website for our business. He understood our goals clearly and built exactly what we needed. The system has been running without issues since launch.',
        'name'    => 'Business Client',
        'role'    => 'Private Business Owner, Nairobi',
        'initials'=> 'BC',
        'type'    => 'Business',
        'type_class' => 'badge--accent',
    ],
    [
        'text'    => 'Working with Harrison on our student portal was a seamless experience. He asked the right questions, structured the system correctly, and delivered a solution that actually solves our workflow problems.',
        'name'    => 'Institutional Client',
        'role'    => 'Operations Manager, Tech Company',
        'initials'=> 'IC',
        'type'    => 'Institutional',
        'type_class' => 'badge--muted',
    ],
    [
        'text'    => 'The loan management system Harrison built for us has significantly improved how we process and track applications. It is secure, reliable, and easy for our staff to use. We would work with him again.',
        'name'    => 'Government Client',
        'role'    => 'County Government Official, Kenya',
        'initials'=> 'GC',
        'type'    => 'Government',
        'type_class' => 'badge--muted',
    ],
];
?>
<link rel="stylesheet" href="<?= BASE_PATH ?>/assets/css/testimonials.css">

<section class="testimonials section" id="testimonials" aria-labelledby="testimonials-title">
    <div class="container">

        <header class="section-header reveal">
            <p class="section-header__label">Client Feedback</p>
            <h2 class="section-header__title" id="testimonials-title">
                What <em>Clients</em> Say
            </h2>
            <p class="section-header__subtitle">
                From private businesses to government bodies — here's what working with Harrison looks like.
            </p>
        </header>

        <div class="testimonials__grid">
            <?php foreach ($testimonials as $i => $t): ?>
            <blockquote class="testimonial-card reveal reveal-delay-<?= $i + 1 ?>">
                <div class="testimonial-card__type badge <?= $t['type_class'] ?>">
                    <?= htmlspecialchars($t['type']) ?>
                </div>
                <div class="testimonial-card__quote-icon" aria-hidden="true">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="currentColor"><path d="M14.017 21v-7.391c0-5.704 3.731-9.57 8.983-10.609l.995 2.151c-2.432.917-3.995 3.638-3.995 5.849h4v10h-9.983zm-14.017 0v-7.391c0-5.704 3.748-9.57 9-10.609l.996 2.151c-2.433.917-3.996 3.638-3.996 5.849h3.983v10h-9.983z"/></svg>
                </div>
                <p class="testimonial-card__text"><?= htmlspecialchars($t['text']) ?></p>
                <footer class="testimonial-card__author">
                    <div class="testimonial-card__avatar" aria-hidden="true"><?= htmlspecialchars($t['initials']) ?></div>
                    <div>
                        <cite class="testimonial-card__name"><?= htmlspecialchars($t['name']) ?></cite>
                        <div class="testimonial-card__role"><?= htmlspecialchars($t['role']) ?></div>
                    </div>
                </footer>
            </blockquote>
            <?php endforeach; ?>
        </div>

    </div>
</section>

<script src="<?= BASE_PATH ?>/assets/js/testimonials.js" defer></script>
