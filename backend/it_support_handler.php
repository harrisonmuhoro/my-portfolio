<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../includes/i18n.php';
require_once __DIR__ . '/rate_limiter.php';
require_once __DIR__ . '/mailer.php';

// ── Response helper ─────────────────────────────────────────
function json_response(bool $success, string $message, int $http_code = 200): void {
    http_response_code($http_code);
    header('Content-Type: application/json; charset=utf-8');
    header('X-Content-Type-Options: nosniff');
    echo json_encode(['success' => $success, 'message' => $message]);
    exit;
}

// ── Only accept POST ─────────────────────────────────────────
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(false, t('err_generic'), 405);
}

// ── Start session for CSRF & rate limiting ───────────────────
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ── CSRF Validation ──────────────────────────────────────────
$submitted_token = trim($_POST[CSRF_TOKEN_NAME] ?? '');
$stored_token    = $_SESSION[CSRF_TOKEN_NAME] ?? '';

if (empty($submitted_token) || !hash_equals($stored_token, $submitted_token)) {
    json_response(false, t('err_generic'), 403);
}

// ── Honeypot check (bots fill hidden fields) ─────────────────
if (!empty($_POST['website'])) {
    // Silently succeed to not tip off bots
    json_response(true, t('f_success'));
}

// ── Rate Limiting (shared server-side store, IP-keyed) ──────
$rate_window = 3600; // 1 hour in seconds
$retry_after = rate_limit_hit(rl_client_key('it_support'), FORM_RATE_LIMIT, $rate_window);

if ($retry_after > 0) {
    header('Retry-After: ' . $retry_after);
    json_response(false, t('err_generic'), 429);
}

// ── Sanitize & validate inputs ───────────────────────────────
/**
 * Sanitize a plain text string (strips tags, trims, normalizes whitespace)
 */
function sanitize_text(string $input, int $max = 500): string {
    $clean = strip_tags(trim($input));
    $clean = preg_replace('/\s+/', ' ', $clean);
    return mb_substr($clean, 0, $max);
}

/**
 * Validate and sanitize email — also prevents header injection
 */
function sanitize_email(string $input): string {
    $email = filter_var(trim($input), FILTER_SANITIZE_EMAIL);
    $email = str_replace(["\r", "\n", "\t"], '', $email);
    return $email;
}

// Language is validated against the allowlist before it is trusted anywhere.
$request_lang = strtolower(trim((string) ($_POST['language'] ?? '')));
if (!in_array($request_lang, IT_SUPPORT_LANGS, true)) {
    $request_lang = it_lang();
}

$name        = sanitize_text($_POST['name']        ?? '', 100);
$phone       = sanitize_text($_POST['phone']       ?? '', 30);
$email       = sanitize_email($_POST['email']      ?? '');
$category    = sanitize_text($_POST['category']    ?? '', 30);
$method      = sanitize_text($_POST['method']      ?? '', 20);
$location    = sanitize_text($_POST['location']    ?? '', 150);
$description = sanitize_text($_POST['description'] ?? '', 3000);
$date        = sanitize_text($_POST['preferred_date'] ?? '', 20);

// ── Validation ───────────────────────────────────────────────
$errors = [];

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = t('err_name', $request_lang);
}

// Accept common Kenyan/international phone formats; reject anything else.
$phone_digits = preg_replace('/[^0-9+]/', '', $phone);
if (strlen($phone_digits) < 9 || strlen($phone_digits) > 15 || !preg_match('/^\+?[0-9]+$/', $phone_digits)) {
    $errors[] = t('err_phone', $request_lang);
}

if ($email !== '' && (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254)) {
    $errors[] = t('err_email', $request_lang);
}

// Service category & method come from server-side allowlists only.
if (!in_array($category, IT_SUPPORT_CATEGORIES, true)) {
    $errors[] = t('err_category', $request_lang);
}

if (!in_array($method, IT_SUPPORT_METHODS, true)) {
    $errors[] = t('err_method', $request_lang);
}

// Location is mandatory for on-site requests, optional otherwise.
if ($method === 'onsite' && mb_strlen($location) < 3) {
    $errors[] = t('err_location', $request_lang);
}
if (mb_strlen($location) > 150) {
    $location = mb_substr($location, 0, 150);
}

if (mb_strlen($description) < 20 || mb_strlen($description) > 3000) {
    $errors[] = t('err_description', $request_lang);
}

// Preferred date: must be a valid Y-m-d date and not in the past.
if ($date !== '') {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    if (!$d || $d->format('Y-m-d') !== $date || $date < date('Y-m-d')) {
        $errors[] = t('err_date', $request_lang);
    }
}

if (!empty($errors)) {
    json_response(false, implode(' ', $errors), 422);
}

// ── Build email ──────────────────────────────────────────────
$category_labels = [
    'network'       => 'Network installation & configuration',
    'troubleshooting' => 'Computer troubleshooting',
    'software'      => 'Software & IT support',
    'business'      => 'Business IT support',
    'other'         => 'Other / not sure',
];
$category_label = $category_labels[$category] ?? 'Other';
$method_label   = $method === 'onsite' ? 'On-site visit' : 'Remote support';
$language_label = $request_lang === 'sw' ? 'Kiswahili' : 'English';

$email_subject = '[IT Support Enquiry] ' . $category_label . ' — ' . $name;

$email_body  = "New IT support request from " . SITE_NAME . " portfolio (/it-support).\n\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "CUSTOMER DETAILS\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "Name:             " . $name . "\n";
$email_body .= "Phone / WhatsApp: " . $phone . "\n";
$email_body .= "Email:            " . ($email !== '' ? $email : 'Not provided') . "\n";
$email_body .= "Preferred language: " . $language_label . "\n";
$email_body .= "Date / Time:      " . date('D, d M Y H:i:s T') . "\n";
$email_body .= "IP Address:       " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "REQUEST\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "Service:          " . $category_label . "\n";
$email_body .= "Support method:   " . $method_label . "\n";
if ($location !== '') {
    $email_body .= "Location:         " . $location . "\n";
}
if ($date !== '') {
    $email_body .= "Preferred date:   " . $date . "\n";
}
$email_body .= "\n" . str_repeat('─', 50) . "\n";
$email_body .= "DESCRIPTION\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= $description . "\n";

// ── Send email via configured provider (Resend → mail() fallback) ──
$sent = portfolio_send_mail($email_subject, $email_body, $email, $name);

if ($sent) {
    // Regenerate CSRF token after successful submission
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));

    json_response(true, t('f_success', $request_lang));
} else {
    json_response(false, t('err_server', $request_lang), 503);
}
