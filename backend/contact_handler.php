<?php
require_once __DIR__ . '/../config/config.php';

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
    json_response(false, 'Method not allowed.', 405);
}

// ── Start session for CSRF ───────────────────────────────────
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// ── CSRF Validation ──────────────────────────────────────────
$submitted_token = trim($_POST[CSRF_TOKEN_NAME] ?? '');
$stored_token    = $_SESSION[CSRF_TOKEN_NAME] ?? '';

if (empty($submitted_token) || !hash_equals($stored_token, $submitted_token)) {
    json_response(false, 'Invalid request. Please refresh the page and try again.', 403);
}

// ── Honeypot check (bots fill hidden fields) ─────────────────
if (!empty($_POST['website'])) {
    // Silently succeed to not tip off bots
    json_response(true, 'Message sent successfully!');
}

// ── Rate Limiting (session-based) ────────────────────────────
$rate_key      = 'contact_submissions';
$rate_window   = 3600; // 1 hour in seconds
$now           = time();

$_SESSION[$rate_key] = $_SESSION[$rate_key] ?? ['count' => 0, 'window_start' => $now];

if (($now - $_SESSION[$rate_key]['window_start']) > $rate_window) {
    $_SESSION[$rate_key] = ['count' => 0, 'window_start' => $now];
}

if ($_SESSION[$rate_key]['count'] >= FORM_RATE_LIMIT) {
    json_response(false, 'Too many submissions. Please try again in an hour.', 429);
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
    // Remove newlines to prevent header injection
    $email = str_replace(["\r", "\n", "\t"], '', $email);
    return $email;
}

$name         = sanitize_text($_POST['name']         ?? '', 100);
$email        = sanitize_email($_POST['email']       ?? '');
$phone        = sanitize_text($_POST['phone']        ?? '', 20);
$project_type = sanitize_text($_POST['project_type'] ?? '', 50);
$subject      = sanitize_text($_POST['subject']      ?? '', 150);
$message      = sanitize_text($_POST['message']      ?? '', 3000);

// ── Validation ───────────────────────────────────────────────
$errors = [];

if (mb_strlen($name) < 2 || mb_strlen($name) > 100) {
    $errors[] = 'Name must be between 2 and 100 characters.';
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL) || mb_strlen($email) > 254) {
    $errors[] = 'A valid email address is required.';
}

$allowed_types = ['website', 'system', 'backend', 'maintenance', 'other'];
if (!in_array($project_type, $allowed_types, true)) {
    $errors[] = 'Invalid project type selected.';
}

if (mb_strlen($subject) < 5 || mb_strlen($subject) > 150) {
    $errors[] = 'Subject must be between 5 and 150 characters.';
}

if (mb_strlen($message) < 20 || mb_strlen($message) > 3000) {
    $errors[] = 'Message must be between 20 and 3000 characters.';
}

if (!empty($errors)) {
    json_response(false, implode(' ', $errors), 422);
}

// ── Build email ──────────────────────────────────────────────
$type_labels = [
    'website'     => 'Website Development',
    'system'      => 'Business / Org System',
    'backend'     => 'Backend Integration',
    'maintenance' => 'Maintenance & Support',
    'other'       => 'Other',
];

$type_label = $type_labels[$project_type] ?? 'Other';

$email_subject = '[Portfolio Enquiry] ' . $subject;

$email_body  = "New contact form submission from " . SITE_NAME . " portfolio.\n\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "SENDER DETAILS\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "Name:          " . $name . "\n";
$email_body .= "Email:         " . $email . "\n";
$email_body .= "Phone:         " . ($phone ?: 'Not provided') . "\n";
$email_body .= "Project Type:  " . $type_label . "\n";
$email_body .= "Subject:       " . $subject . "\n";
$email_body .= "Date / Time:   " . date('D, d M Y H:i:s T') . "\n";
$email_body .= "IP Address:    " . ($_SERVER['REMOTE_ADDR'] ?? 'Unknown') . "\n\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "MESSAGE\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= $message . "\n\n";
$email_body .= str_repeat('─', 50) . "\n";
$email_body .= "Reply directly to: " . $email . "\n";

// ── Headers — prevent injection by using sanitized values ────
// Use a domain-relative From address to improve deliverability
$host         = $_SERVER['HTTP_HOST'] ?? 'harrisonmuhoro.wasmer.app';
$from_email   = 'no-reply@' . $host;
$from_name    = preg_replace('/[^a-zA-Z0-9 \-_]/', '', $name);

$headers   = [];
$headers[] = "From: Portfolio Contact <" . $from_email . ">";
$headers[] = "Reply-To: " . $from_name . " <" . $email . ">";
$headers[] = "X-Mailer: PHP/" . phpversion();
$headers[] = "MIME-Version: 1.0";
$headers[] = "Content-Type: text/plain; charset=UTF-8";

// ── Send email ───────────────────────────────────────────────
// We use the -f flag to set the envelope sender, which helps avoid spam filters
$sent = mail(EMAIL_ADDRESS, $email_subject, $email_body, implode("\r\n", $headers), "-f" . $from_email);

if ($sent) {
    // Increment rate limit counter
    $_SESSION[$rate_key]['count']++;

    // Regenerate CSRF token after successful submission
    $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));

    json_response(true, 'Message sent successfully! I will get back to you within 24 hours.');
} else {
    // Log failure in production
    if (ENVIRONMENT === 'production') {
        error_log('[ContactHandler] mail() failed for: ' . $email . ' at ' . date('Y-m-d H:i:s'));
    }
    // Return a 200 with success:false instead of a 500 to avoid scary browser console errors
    // but clearly indicate failure to the user.
    json_response(false, 'Email delivery failed. Please contact me directly at ' . EMAIL_ADDRESS, 200);
}
