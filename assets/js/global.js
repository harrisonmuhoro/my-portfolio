/**
 * global.js — Scroll reveal animations
 */
(function () {
    'use strict';

    const revealEls = document.querySelectorAll('.reveal');
    if (!revealEls.length) return;

    const revealObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    // Apply staggered delays from CSS class
                    entry.target.classList.add('is-visible');
                    revealObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.08, rootMargin: '0px 0px -40px 0px' }
    );

    revealEls.forEach(function (el) { revealObserver.observe(el); });
})();
