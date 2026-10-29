<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require __DIR__ . '/mailer.php';
require __DIR__ . '/turnstile.php';
require __DIR__ . '/SimpleLogger.php';

ini_set('display_errors', '0');
error_reporting(E_ALL);

// Local: start the PHP server with `APP_ENV=dev php -S localhost:8000`.
$isDev = getenv('APP_ENV') === 'dev';
$configFile = __DIR__ . ($isDev ? '/dev.ini' : '/prod.ini');

$ini_array = @parse_ini_file($configFile);

if (!empty($ini_array['LOG_FILE'])) {
    SimpleLogger::setFile($ini_array['LOG_FILE']);
} else {
    SimpleLogger::setFile(__DIR__ . '/mail.log');
}

function respond($status, array $body) {
    http_response_code($status);
    echo json_encode($body);
    exit();
}

header('Content-Type: application/json; charset=utf-8');

if ($isDev) {
    header('Access-Control-Allow-Origin: http://localhost:4321');
    header('Access-Control-Allow-Methods: POST, OPTIONS');
    header('Access-Control-Allow-Headers: Content-Type');
}

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    respond(204, []);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    respond(405, ["success" => false, "error" => "method_not_allowed"]);
}

if ($ini_array === false) {
    SimpleLogger::error('could not read config file', $configFile);
    respond(500, ["success" => false, "error" => "server_error"]);
}

foreach (['HOST', 'USERNAME', 'SMTP', 'ADDADDRESS', 'SECRET_KEY'] as $key) {
    if (empty($ini_array[$key])) {
        SimpleLogger::error('missing config key', $key);
        respond(500, ["success" => false, "error" => "server_error"]);
    }
}

$token = $_POST['cf-turnstile-response'] ?? '';
$validation = validateTurnstile($token, $ini_array["SECRET_KEY"], $_SERVER['REMOTE_ADDR'] ?? null);

if (!$validation['success']) {
    $errorCodes = $validation['error-codes'];
    $context = ['error-codes' => $errorCodes];
    if (isset($validation['internal-message'])) {
        $context['internal-message'] = $validation['internal-message'];
    }

    // internal-error / invalid-json mean *we* couldn't verify, not that the visitor is a bot.
    if (array_intersect($errorCodes, ['internal-error', 'invalid-json'])) {
        SimpleLogger::error('turnstile verification could not be performed', $context);
        respond(502, ["success" => false, "error" => "verification_unavailable"]);
    }

    SimpleLogger::warn('turnstile validation failed', $context);
    respond(403, ["success" => false, "error" => "verification_failed"]);
}

SimpleLogger::info('turnstile validation successful', ['hostname' => $validation['hostname'] ?? null]);

$name = trim((string) ($_POST["name"] ?? ''));
$email = trim((string) ($_POST["email"] ?? ''));
$subject = trim((string) ($_POST["subject"] ?? ''));
$message = trim((string) ($_POST["message"] ?? ''));

$errors = [
    "name" => $name === '' || mb_strlen($name) > 100,
    "email" => !PHPMailer::validateAddress($email) || mb_strlen($email) > 254,
    "subject" => $subject === '' || mb_strlen($subject) > 200,
    "message" => $message === '' || mb_strlen($message) > 10000
];

if (in_array(true, $errors, true)) {
    SimpleLogger::info('invalid form input', array_keys(array_filter($errors)));
    respond(400, ["success" => false, "error" => "invalid_input", "fields" => $errors]);
}

// Names/subjects end up in mail headers; strip line breaks so nothing can be injected.
$name = str_replace(["\r", "\n"], ' ', $name);
$subject = str_replace(["\r", "\n"], ' ', $subject);

// --- 3. Send via Hetzner SMTP -----------------------------------------------
$mail = null;

try {
    $mail = createMailer($ini_array);

    // Only enable when debugging SMTP problems; the transcript contains credentials (base64).
    // $mail->SMTPDebug   = \PHPMailer\PHPMailer\SMTP::DEBUG_SERVER;
    // $mail->Debugoutput = function ($str, $level) { SimpleLogger::debug('smtp', trim($str)); };

    $mail->addReplyTo($email, $name);
    $mail->addAddress($ini_array["ADDADDRESS"]);

    $mail->isHTML(false);

    $preMessage = "Du hast eine Email von " . $name . " mit der Email Adresse " . $email . " erhalten.\r\n\r\n";

    $mail->Subject = $subject;
    $mail->Body    = $preMessage . $message;

    $mail->send();
    SimpleLogger::info('mail sent', ['message-id' => $mail->getLastMessageID()]);
    respond(200, ["success" => true]);
} catch (Exception $e) {
    SimpleLogger::error('mail could not be sent', $mail ? $mail->ErrorInfo : null);
    SimpleLogger::exception($e, 'PHPMailer exception');
    respond(500, ["success" => false, "error" => "send_failed"]);
}
