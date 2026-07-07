<?php
/**
 * Navigation Include
 * Sticky minimal navbar with theme toggle
 */
require_once __DIR__ . '/../config/config.php';
?>
<header class="nav-header" id="nav-header" role="banner">
    <nav class="nav-container" aria-label="Primary Navigation">

        <!-- Logo -->
        <a href="<?= BASE_PATH ?>/" class="nav-logo" aria-label="<?= SITE_NAME ?> - Home">
            <span class="nav-logo__initials">HM</span>
            <span class="nav-logo__text">Harrison <em>Muhoro</em></span>
        </a>

        <!-- Desktop Links -->
        <ul class="nav-links" role="list">
            <li><a href="<?= BASE_PATH ?>/#work"       class="nav-link">Work</a></li>
            <li><a href="<?= BASE_PATH ?>/#stack"      class="nav-link">Stack</a></li>
            <li><a href="<?= BASE_PATH ?>/#about"      class="nav-link">About</a></li>
            <li><a href="<?= BASE_PATH ?>/#contact"    class="nav-link">Contact</a></li>
            <li>
                <button class="theme-toggle" id="theme-toggle" aria-label="Toggle theme" title="Toggle dark/light mode">
                    <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                    <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
                </button>
            </li>
            <li><a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="btn btn--outline" style="padding: var(--space-2) var(--space-4);">Download CV</a></li>
        </ul>

        <!-- Hamburger & Theme Toggle Mobile -->
        <div class="nav-mobile-controls">
            <button class="theme-toggle theme-toggle--mobile" id="theme-toggle-mobile" aria-label="Toggle theme">
                <svg class="sun-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="5"></circle><line x1="12" y1="1" x2="12" y2="3"></line><line x1="12" y1="21" x2="12" y2="23"></line><line x1="4.22" y1="4.22" x2="5.64" y2="5.64"></line><line x1="18.36" y1="18.36" x2="19.78" y2="19.78"></line><line x1="1" y1="12" x2="3" y2="12"></line><line x1="21" y1="12" x2="23" y2="12"></line><line x1="4.22" y1="19.78" x2="5.64" y2="18.36"></line><line x1="18.36" y1="5.64" x2="19.78" y2="4.22"></line></svg>
                <svg class="moon-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12.79A9 9 0 1 1 11.21 3 7 7 0 0 0 21 12.79z"></path></svg>
            </button>
            <button class="nav-hamburger" id="nav-hamburger" aria-label="Toggle navigation menu" aria-expanded="false" aria-controls="mobile-menu">
                <span class="nav-hamburger__bar"></span>
                <span class="nav-hamburger__bar"></span>
                <span class="nav-hamburger__bar"></span>
            </button>
        </div>
    </nav>

    <!-- Mobile Menu -->
    <div class="nav-mobile" id="mobile-menu" aria-hidden="true" role="navigation" aria-label="Mobile Navigation">
        <ul class="nav-mobile__links" role="list">
            <li><a href="<?= BASE_PATH ?>/#work"       class="nav-mobile__link" tabindex="-1">Work</a></li>
            <li><a href="<?= BASE_PATH ?>/#stack"      class="nav-mobile__link" tabindex="-1">Stack</a></li>
            <li><a href="<?= BASE_PATH ?>/#about"      class="nav-mobile__link" tabindex="-1">About</a></li>
            <li><a href="<?= BASE_PATH ?>/#contact"    class="nav-mobile__link" tabindex="-1">Contact</a></li>
            <li><a href="<?= CV_PATH ?>" target="_blank" rel="noopener noreferrer" class="nav-mobile__link" tabindex="-1">Download CV</a></li>
        </ul>
    </div>
</header>
