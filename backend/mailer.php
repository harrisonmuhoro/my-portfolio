<?php
/**
 * backend/mailer.php — Shared transactional mailer.
 *
 * Uses the Resend HTTP API when RESEND_API_KEY is configured; otherwise
 * falls back to PHP mail() with the configured MAIL_FROM sender.
 * Never trusts client-supplied sender addresses — the From is always
 * MAIL_FROM and the visitor address is only ever used as Reply-To.
 */

if (!defined('PROJECT_ROOT')) {
    http_response_code(403);
    exit('Forbidden');
}

/**
 * Send a plain-text enquiry email.
 *
 * @param string $subject        Email subject line
 * @param string $body           Plain-text body
 * @param string $reply_to_email Visitor address used for Reply-To ('' = none)
 * @param string $reply_to_name  Visitor display name for Reply-To
 * @return bool True only when the provider accepted the message.
 */
function portfolio_send_mail(string $subject, string $body, string $reply_to_email = '', string $reply_to_name = ''): bool {
    // ── Preferred path: Resend API ────────────────────────────
    if (RESEND_API_KEY !== '') {
        return resend_send($subject, $body, $reply_to_email, $reply_to_name);
    }

    return mail_fallback($subject, $body, $reply_to_email, $reply_to_name);
}

/** Build the RFC-5322 display-name <addr> pair safely. */
function mail_addr(string $name, string $email): string {
    $name = trim(preg_replace('/[\r\n]+/', ' ', $name));
    $name = preg_replace('/[^\x20-\x7E\xC0-\xFF]/u', '', $name);
    return ($name !== '' ? '"' . addslashes($name) . '" ' : '') . '<' . $email . '>';
}

/** Resend API transport (https://resend.com/docs/api-reference/emails/send-email). */
function resend_send(string $subject, string $body, string $reply_to_email, string $reply_to_name): bool {
    $payload = [
        'from'    => MAIL_FROM,
        'to'      => [MAIL_TO],
        'subject' => $subject,
        'text'    => $body,
    ];
    if ($reply_to_email !== '' && filter_var($reply_to_email, FILTER_VALIDATE_EMAIL)) {
        $payload['reply_to'] = $reply_to_email;
    }

    $json = json_encode($payload, JSON_UNESCAPED_SLASHES);
    if ($json === false) {
        error_log('[Mailer] failed to encode Resend payload');
        return false;
    }

    // Some hosts (e.g. WAMP) have a cURL build without a CA bundle — if the
    // cURL attempt fails at transport level (HTTP code 0), retry over streams.
    if (function_exists('curl_init')) {
        [$resp, $code, $err] = resend_http_post($json);
        if ($code === 0) {
            [$resp, $code, $err] = resend_http_post_streams($json);
        }
    } else {
        [$resp, $code, $err] = resend_http_post_streams($json);
    }

    if ($code >= 200 && $code < 300) {
        return true;
    }

    error_log('[Mailer] Resend rejected message (HTTP ' . $code . '): '
        . substr((string) $resp, 0, 500) . ($err !== '' ? ' transport: ' . $err : ''));
    return false;
}

/** POST JSON to the Resend API via cURL. Returns [body, http_code, error]. */
function resend_http_post(string $json): array {
    $ch = curl_init('https://api.resend.com/emails');
    curl_setopt_array($ch, [
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_POST           => true,
        CURLOPT_POSTFIELDS     => $json,
        CURLOPT_HTTPHEADER     => [
            'Authorization: Bearer ' . RESEND_API_KEY,
            'Content-Type: application/json',
        ],
        CURLOPT_TIMEOUT        => 15,
    ]);
    $resp = curl_exec($ch);
    $code = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
    $err  = curl_error($ch);
    curl_close($ch);
    return [$resp === false ? '' : $resp, $code, $err];
}

/** POST JSON to the Resend API via stream contexts (no cURL needed). */
function resend_http_post_streams(string $json): array {
    $ctx = stream_context_create(['http' => [
        'method'        => 'POST',
        'header'        => "Authorization: Bearer " . RESEND_API_KEY . "\r\nContent-Type: application/json\r\n",
        'content'       => $json,
        'timeout'       => 15,
        'ignore_errors' => true,
    ]]);
    $resp = @file_get_contents('https://api.resend.com/emails', false, $ctx);
    $code = 0;
    if (isset($http_response_header[0]) && preg_match('#\s(\d{3})\s#', $http_response_header[0], $m)) {
        $code = (int) $m[1];
    }
    $err = $resp === false ? 'file_get_contents failed' : '';
    return [$resp === false ? '' : $resp, $code, $err];
}

/** PHP mail() fallback — still uses the configured MAIL_FROM sender. */
function mail_fallback(string $subject, string $body, string $reply_to_email, string $reply_to_name): bool {
    $from_email = MAIL_FROM;

    $headers   = [];
    $headers[] = "From: " . mail_addr(SITE_NAME, $from_email);
    if ($reply_to_email !== '' && filter_var($reply_to_email, FILTER_VALIDATE_EMAIL)) {
        $headers[] = "Reply-To: " . mail_addr($reply_to_name, $reply_to_email);
    } else {
        $headers[] = "Reply-To: " . mail_addr(SITE_NAME, $from_email);
    }
    $headers[] = "X-Mailer: PHP/" . phpversion();
    $headers[] = "MIME-Version: 1.0";
    $headers[] = "Content-Type: text/plain; charset=UTF-8";

    // Suppress the warning — mail() returns false on failure and we log it.
    $sent = @mail(MAIL_TO, $subject, $body, implode("\r\n", $headers), "-f" . $from_email);
    if (!$sent) {
        error_log('[Mailer] mail() fallback failed for subject: ' . $subject);
    }
    return (bool) $sent;
}
