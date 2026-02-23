/**
 * navigation.js — Sticky nav, mobile menu, active link
 */
(function () {
    'use strict';

    const header    = document.getElementById('nav-header');
    const hamburger = document.getElementById('nav-hamburger');
    const mobileMenu= document.getElementById('mobile-menu');

    if (!header) return;

    // ── Scrolled state ─────────────────────────────────────
    const SCROLL_THRESHOLD = 60;

    function onScroll() {
        const scrolled = window.scrollY > SCROLL_THRESHOLD;
        header.classList.toggle('is-scrolled', scrolled);
    }

    window.addEventListener('scroll', onScroll, { passive: true });
    onScroll();

    // ── Mobile menu ────────────────────────────────────────
    function openMenu() {
        hamburger.classList.add('is-open');
        hamburger.setAttribute('aria-expanded', 'true');
        mobileMenu.classList.add('is-open');
        mobileMenu.setAttribute('aria-hidden', 'false');
        document.body.style.overflow = 'hidden';
    }

    function closeMenu() {
        hamburger.classList.remove('is-open');
        hamburger.setAttribute('aria-expanded', 'false');
        mobileMenu.classList.remove('is-open');
        mobileMenu.setAttribute('aria-hidden', 'true');
        document.body.style.overflow = '';
    }

    hamburger.addEventListener('click', function () {
        const isOpen = mobileMenu.classList.contains('is-open');
        isOpen ? closeMenu() : openMenu();
    });

    // Close on link click
    mobileMenu.querySelectorAll('.nav-mobile__link').forEach(function (link) {
        link.addEventListener('click', closeMenu);
        link.setAttribute('tabindex', '0');
    });

    // Close on outside click
    document.addEventListener('click', function (e) {
        if (mobileMenu.classList.contains('is-open') &&
            !header.contains(e.target)) {
            closeMenu();
        }
    });

    // Close on Escape
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && mobileMenu.classList.contains('is-open')) {
            closeMenu();
        }
    });

    // ── Active nav link on scroll ──────────────────────────
    const sections  = document.querySelectorAll('section[id]');
    const navLinks  = document.querySelectorAll('.nav-link');

    const observer = new IntersectionObserver(
        function (entries) {
            entries.forEach(function (entry) {
                if (entry.isIntersecting) {
                    navLinks.forEach(function (link) {
                        link.classList.remove('is-active');
                        if (link.getAttribute('href') === '/#' + entry.target.id) {
                            link.classList.add('is-active');
                        }
                    });
                }
            });
        },
        { rootMargin: '-40% 0px -55% 0px' }
    );

    sections.forEach(function (s) { observer.observe(s); });
})();
