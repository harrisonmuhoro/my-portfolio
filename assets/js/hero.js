(function () {
    'use strict';

    // Typewriter effect for secondary title
    const target = document.querySelector('.hero__title-secondary');
    if (!target) return;

    const phrases = [
        'Full-Stack Developer in Training',
        'PHP & MySQL Specialist',
        'Business Systems Builder',
        'Government-Grade Developer',
    ];

    let phraseIndex = 0;
    let charIndex   = 0;
    let isDeleting  = false;
    const SPEED_TYPE   = 80;
    const SPEED_DELETE = 40;
    const PAUSE_END    = 2000;
    const PAUSE_START  = 400;

    function type() {
        const current = phrases[phraseIndex];
        const displayed = isDeleting
            ? current.substring(0, charIndex - 1)
            : current.substring(0, charIndex + 1);

        target.textContent = displayed;

        if (!isDeleting && charIndex === current.length) {
            setTimeout(function () { isDeleting = true; type(); }, PAUSE_END);
            return;
        }

        if (isDeleting && charIndex === 0) {
            isDeleting  = false;
            phraseIndex = (phraseIndex + 1) % phrases.length;
            setTimeout(type, PAUSE_START);
            return;
        }

        charIndex += isDeleting ? -1 : 1;
        setTimeout(type, isDeleting ? SPEED_DELETE : SPEED_TYPE);
    }

    // Start after hero is visible
    setTimeout(type, 1500);
})();
