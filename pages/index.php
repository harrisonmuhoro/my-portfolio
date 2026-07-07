<?php
/**
 * pages/index.php
 * This file is NOT used by the router (index.php at project root handles routing).
 * Redirect to home as a safety fallback.
 */
require_once __DIR__ . '/../config/config.php';
header('Location: ' . BASE_PATH . '/');
exit;
