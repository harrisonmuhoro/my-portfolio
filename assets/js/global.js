/**
 * global.js — Scroll reveal observer, shared utilities
 */
(function () {
    'use strict';

    // ── Reveal on scroll ─────────────────────────────────────
    const revealEls = document.querySelectorAll('.reveal');

    if (!revealEls.length) return;

    const revealObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );

    revealEls.forEach(function (el) { revealObserver.observe(el); });

    // ── Smooth scroll for anchor links ───────────────────────
    // Works for both root and subdirectory installs (e.g. localhost/harrison)
    document.querySelectorAll('a[href*="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            // Get just the hash part
            const hashIndex = href.indexOf('#');
            if (hashIndex === -1) return;
            const hash = href.substring(hashIndex); // e.g. '#about'
            const target = document.querySelector(hash);
            if (target) {
                e.preventDefault();
                target.scrollIntoView({ behavior: 'smooth' });
                history.pushState(null, null, hash);
            }
        });
    });
})();
