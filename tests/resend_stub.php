<?php
/**
 * tests/resend_stub.php — Minimal Resend API stub for tests.
 * Run via: php -S 127.0.0.1:PORT tests/resend_stub.php
 * Always returns 200 with a fake message id. No email is sent.
 */
http_response_code(200);
header('Content-Type: application/json');
echo json_encode(['id' => 'test-message-id', 'object' => 'email']);
