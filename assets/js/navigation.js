(function () {
    'use strict';

    const header    = document.getElementById('nav-header');
    const hamburger = document.getElementById('nav-hamburger');
    const mobileMenu= document.getElementById('mobile-menu');
    const navLinks  = document.querySelectorAll('.nav-link, .nav-mobile__link');
    const themeToggles = document.querySelectorAll('.theme-toggle');

    // ── Scroll styling ─────────────────────────────
    function handleScroll() {
        if (window.scrollY > 50) {
            header.classList.add('is-scrolled');
        } else {
            header.classList.remove('is-scrolled');
        }
    }

    if (header) {
        window.addEventListener('scroll', handleScroll, { passive: true });
        handleScroll(); // Run on load
    }

    // ── Mobile Menu Toggle ──────────────────────────
    function toggleMobileMenu() {
        const isExpanded = hamburger.getAttribute('aria-expanded') === 'true';
        const nextExpanded = !isExpanded;
        hamburger.setAttribute('aria-expanded', nextExpanded);
        mobileMenu.classList.toggle('is-open');
        mobileMenu.setAttribute('aria-hidden', isExpanded);
        mobileMenu.querySelectorAll('.nav-mobile__link').forEach(function (link) {
            link.tabIndex = nextExpanded ? 0 : -1;
        });
        document.body.style.overflow = nextExpanded ? 'hidden' : ''; // Prevent scrolling
        if (nextExpanded) {
            const firstLink = mobileMenu.querySelector('.nav-mobile__link');
            if (firstLink) firstLink.focus();
        } else {
            hamburger.focus();
        }
    }

    if (hamburger) {
        hamburger.addEventListener('click', toggleMobileMenu);
    }

    document.addEventListener('keydown', function (event) {
        if (event.key === 'Escape' && mobileMenu && mobileMenu.classList.contains('is-open')) {
            toggleMobileMenu();
        }
    });

    // Close menu when clicking a mobile link
    navLinks.forEach(function (link) {
        if (link.classList.contains('nav-mobile__link')) {
            link.addEventListener('click', function () {
                if (mobileMenu.classList.contains('is-open')) {
                    toggleMobileMenu();
                }
            });
        }
    });

    // ── Smooth Scroll & Active Link ──────────────────────────
    // Since we're using anchor links for the homepage
    navLinks.forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href.includes('#')) {
                const hashIndex = href.indexOf('#');
                const hash = href.substring(hashIndex);
                
                // If we are already on the homepage (or the page with the anchor)
                if (window.location.pathname === '/' || window.location.pathname === '/harrison-portfolio/') {
                    const target = document.querySelector(hash);
                    if (target) {
                        e.preventDefault();
                        target.scrollIntoView({ behavior: 'smooth' });
                        history.pushState(null, null, hash);
                    }
                }
            }
        });
    });

    // Active link based on scroll position
    const sections = document.querySelectorAll('section[id]');
    const observer = 'IntersectionObserver' in window ? new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navLinks.forEach(function (link) {
                        link.classList.remove('is-active');
                        if (link.getAttribute('href').includes('#' + entry.target.id)) {
                            link.classList.add('is-active');
                        }
                    });
                }
            });
        },
        { rootMargin: '-40% 0px -55% 0px' }
    ) : null;
    if (observer) sections.forEach(function (s) { observer.observe(s); });


    // ── Theme Toggle ───────────────────────────────
    function toggleTheme() {
        const currentTheme = document.documentElement.getAttribute('data-theme');
        const newTheme = currentTheme === 'light' ? 'dark' : 'light';
        document.documentElement.setAttribute('data-theme', newTheme);
        localStorage.setItem('theme', newTheme);
    }

    themeToggles.forEach(toggle => {
        toggle.addEventListener('click', toggleTheme);
    });

})();
