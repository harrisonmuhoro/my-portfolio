/**
 * skills.js — Animate skill bars on scroll entry
 */
(function () {
    'use strict';

    const fills = document.querySelectorAll('.skill-item__fill');
    if (!fills.length) return;

    const barObserver = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    entry.target.classList.add('is-animated');
                    barObserver.unobserve(entry.target);
                }
            });
        },
        { threshold: 0.3 }
    );

    fills.forEach(function (fill) { barObserver.observe(fill); });
})();
