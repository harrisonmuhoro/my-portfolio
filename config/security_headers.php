<?php
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
       $csp_directives = [
        "default-src 'self'",
        "script-src 'self' 'unsafe-inline'",                                
        "style-src 'self' 'unsafe-inline' https://fonts.googleapis.com",  
        "font-src 'self' https://fonts.gstatic.com",                     
        "img-src 'self' data: https:",                                    
        "connect-src 'self'",                                             
        "frame-src 'none'",                                               
        "object-src 'none'",                                              
        "base-uri 'self'",                                                
        "form-action 'self'",                                             
        "frame-ancestors 'self'",                                         
        "upgrade-insecure-requests",                                      
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
