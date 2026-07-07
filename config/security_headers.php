<?php
/**
 * Security Headers Middleware
 * Harrison Muhoro — Developer Portfolio
 *
 * Sends all security headers via PHP header() calls.
 * Required because Wasmer Edge does not use Apache, so .htaccess
 * mod_headers directives are silently ignored.
 *
 * MUST be required BEFORE any output (before session_start, before HTML).
 */

// Prevent direct access
if (!defined('PROJECT_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

// ─── Only send headers if they haven't been sent yet ──────────
if (!headers_sent()) {

    // ── 1. Strict-Transport-Security (HSTS) ───────────────────
    // Force HTTPS for 1 year, include subdomains, allow preload list
    header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');

    // ── 2. Content-Security-Policy (CSP) ──────────────────────
    // Whitelist approach — block everything not explicitly allowed
    $csp_directives = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'",                                // Theme persistence inline script
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",  // Google Fonts CSS
        "font-src 'self' https://fonts.gstatic.com",                      // Google Fonts files
        "img-src 'self' data: https:",                                    // Own images + data URIs + HTTPS images
        "connect-src 'self'",                                             // Restrict fetch/XHR to same origin
        "frame-src 'none'",                                               // No iframes allowed
        "object-src 'none'",                                              // No plugins (Flash, Java, etc.)
        "base-uri 'self'",                                                // Prevent base tag hijacking
        "form-action 'self'",                                             // Forms only submit to same origin
        "frame-ancestors 'self'",                                         // Equivalent to X-Frame-Options in CSP
        "upgrade-insecure-requests",                                      // Auto-upgrade HTTP to HTTPS
    ];
    header('Content-Security-Policy: ' . implode('; ', $csp_directives));

    // ── 3. X-Frame-Options ────────────────────────────────────
    // Prevent clickjacking — only allow framing by same origin
    header('X-Frame-Options: SAMEORIGIN');

    // ── 4. X-Content-Type-Options ─────────────────────────────
    // Prevent MIME-type sniffing
    header('X-Content-Type-Options: nosniff');

    // ── 5. Referrer-Policy ────────────────────────────────────
    // Send full URL for same-origin, only origin for cross-origin
    header('Referrer-Policy: strict-origin-when-cross-origin');

    // ── 6. Permissions-Policy ─────────────────────────────────
    // Disable browser features/APIs not needed by a portfolio site
    $permissions = [
        'camera=()',
        'microphone=()',
        'geolocation=()',
        'payment=()',
        'usb=()',
        'magnetometer=()',
        'gyroscope=()',
        'accelerometer=()',
        'autoplay=()',
        'fullscreen=(self)',
    ];
    header('Permissions-Policy: ' . implode(', ', $permissions));

    // ── 7. Cross-Origin Headers (Upcoming) ────────────────────
    header('Cross-Origin-Opener-Policy: same-origin');
    header('Cross-Origin-Resource-Policy: same-origin');

    // ── 8. Remove server information leaks ────────────────────
    header_remove('X-Powered-By');
    header_remove('Server');
}
