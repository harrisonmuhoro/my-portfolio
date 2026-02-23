<?php
/**
 * Root index.php — Entry point for WAMP/Apache
 * Works at localhost/harrison/ or any subdirectory
 */

// Define the project root as a constant so all includes work
define('PROJECT_ROOT', __DIR__);

require_once PROJECT_ROOT . '/config/config.php';

// ── Start session here — before ANY output ─────────────────
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once PROJECT_ROOT . '/pages/index.php';
