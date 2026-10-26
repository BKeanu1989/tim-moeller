<?php
/**
 * Validates a Cloudflare Turnstile token server-side.
 * Docs: https://developers.cloudflare.com/turnstile/get-started/server-side-validation/
 *
 */
function validateTurnstile($token, $secret, $remoteip = null, $timeout = 10) {
    $url = 'https://challenges.cloudflare.com/turnstile/v0/siteverify';

    // Tokens are at most 2048 chars; reject obviously invalid input without a network call.
    if (!is_string($token) || $token === '' || strlen($token) > 2048) {
        return ['success' => false, 'error-codes' => ['missing-input-response']];
    }

    $data = [
        'secret' => $secret,
        'response' => $token
    ];

    if ($remoteip) {
        $data['remoteip'] = $remoteip;
    }

    $options = [
        'http' => [
            'header' => "Content-type: application/x-www-form-urlencoded\r\n",
            'method' => 'POST',
            'content' => http_build_query($data),
            'timeout' => $timeout,
            // Return the body on 4xx/5xx too, so we can read Cloudflare's error-codes.
            'ignore_errors' => true
        ]
    ];

    $context = stream_context_create($options);
    $response = @file_get_contents($url, false, $context);

    if ($response === false) {
        $err = error_get_last();
        return [
            'success' => false,
            'error-codes' => ['internal-error'],
            'internal-message' => $err ? $err['message'] : 'request to siteverify failed'
        ];
    }

    $result = json_decode($response, true);

    if (!is_array($result) || !isset($result['success'])) {
        return [
            'success' => false,
            'error-codes' => ['invalid-json'],
            'internal-message' => substr($response, 0, 200)
        ];
    }

    $result['success'] = $result['success'] === true;
    if (!isset($result['error-codes']) || !is_array($result['error-codes'])) {
        $result['error-codes'] = [];
    }

    return $result;
}
