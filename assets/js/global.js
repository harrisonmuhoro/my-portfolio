/**
 * global.js — Motion system: scroll reveal, hero entrance, scroll progress
 */
(function () {
    'use strict';

    var prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
    var supportsIO = 'IntersectionObserver' in window;

    var revealEls = document.querySelectorAll('.reveal');

    // ── No-motion or no-IO: show everything immediately ────────
    if (prefersReducedMotion || !supportsIO) {
        revealEls.forEach(function (el) { el.classList.add('is-visible'); });
    } else if (revealEls.length) {
        document.documentElement.classList.add('motion-ready');
    }

    // ── Hero entrance: animate above-fold elements on load ─────
    // IntersectionObserver fires immediately for in-viewport elements,
    // so we handle hero children manually for a deliberate entrance sequence.
    var heroSection = document.querySelector('.hero-section');
    if (heroSection && !prefersReducedMotion && supportsIO) {
        var heroReveals = Array.prototype.slice.call(
            heroSection.querySelectorAll('.reveal:not(.reveal--image)')
        );
        var heroImage = heroSection.querySelector('.reveal--image');

        heroReveals.forEach(function (el) { el.dataset.heroManaged = '1'; });
        if (heroImage) { heroImage.dataset.heroManaged = '1'; }

        window.requestAnimationFrame(function () {
            heroReveals.forEach(function (el, i) {
                setTimeout(function () {
                    el.classList.add('is-visible');
                }, i * 95 + 60);
            });
            if (heroImage) {
                setTimeout(function () {
                    heroImage.classList.add('is-visible');
                }, heroReveals.length * 95 + 60);
            }
        });
    }

    // ── Scroll-driven reveal for all other .reveal elements ─────
    var revealObserver = supportsIO && !prefersReducedMotion ? new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting && !entry.target.dataset.heroManaged) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.07, rootMargin: '0px 0px -40px 0px' }
    ) : null;

    revealEls.forEach(function (el) {
        if (revealObserver && !el.dataset.heroManaged) {
            revealObserver.observe(el);
        }
    });

    // ── Scroll progress bar ─────────────────────────────────────
    var progress = document.querySelector('.scroll-progress__bar');
    if (progress) {
        var scheduled = false;
        function updateProgress() {
            var distance = document.documentElement.scrollHeight - window.innerHeight;
            var ratio = distance > 0 ? Math.min(1, Math.max(0, window.scrollY / distance)) : 0;
            progress.style.transform = 'scaleX(' + ratio + ')';
            scheduled = false;
        }
        window.addEventListener('scroll', function () {
            if (!scheduled) { scheduled = true; window.requestAnimationFrame(updateProgress); }
        }, { passive: true });
        updateProgress();
    }

})();
