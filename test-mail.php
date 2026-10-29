<?php
/**
 * CLI client to test SMTP sending on its own, without the form and Turnstile.
 * Uses the same config and SMTP setup as mail.php (see mailer.php).
 *
 * Usage:
 *   php test-mail.php                          # dev.ini, sends to ADDADDRESS
 *   php test-mail.php --env=prod               # prod.ini
 *   php test-mail.php --to=me@example.com      # different recipient
 *   php test-mail.php --reply-to=a@b.de        # simulate a visitor's address
 *   php test-mail.php --debug=0                # no SMTP transcript (default: 2)
 *   php test-mail.php --dry-run                # only connect + authenticate, send nothing
 */

use PHPMailer\PHPMailer\Exception;

// Must never be reachable through the web server.
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit();
}

require __DIR__ . '/mailer.php';
require __DIR__ . '/SimpleLogger.php';

$opts = getopt('', ['env:', 'to:', 'reply-to:', 'debug:', 'dry-run', 'help']);

if (isset($opts['help'])) {
    echo "php test-mail.php [--env=dev|prod] [--to=addr] [--reply-to=addr] [--debug=0-4] [--dry-run]\n";
    exit(0);
}

$env = $opts['env'] ?? 'dev';
if (!in_array($env, ['dev', 'prod'], true)) {
    fwrite(STDERR, "--env must be dev or prod\n");
    exit(1);
}

$configFile = __DIR__ . "/$env.ini";
$ini_array = @parse_ini_file($configFile);
if ($ini_array === false) {
    fwrite(STDERR, "could not read $configFile\n");
    exit(1);
}

if (!empty($ini_array['LOG_FILE'])) {
    SimpleLogger::setFile($ini_array['LOG_FILE']);
} else {
    SimpleLogger::setFile(__DIR__ . '/email-test.log');
}

$missing = array_filter(['HOST', 'USERNAME', 'SMTP', 'ADDADDRESS'], function ($key) use ($ini_array) {
    return empty($ini_array[$key]);
});
if ($missing) {
    fwrite(STDERR, "missing config keys in $configFile: " . implode(', ', $missing) . "\n");
    exit(1);
}

$to = $opts['to'] ?? $ini_array['ADDADDRESS'];
$debugLevel = isset($opts['debug']) ? (int) $opts['debug'] : 2;

echo "Config:    $configFile\n";
echo "Host:      {$ini_array['HOST']}:587 (STARTTLS)\n";
echo "Username:  {$ini_array['USERNAME']}\n";
echo "Recipient: $to\n\n";

$mail = null;

try {
    $mail = createMailer($ini_array);
    $mail->SMTPDebug = $debugLevel;
    $mail->Debugoutput = 'echo';

    if (isset($opts['dry-run'])) {
        // Connect, STARTTLS and AUTH only. Verifies host, port and credentials without sending.
        $mail->smtpConnect();
        $mail->smtpClose();
        echo "\nOK: connected and authenticated, nothing sent.\n";
        SimpleLogger::info('test-mail dry-run successful', ['env' => $env]);
        exit(0);
    }

    if (isset($opts['reply-to'])) {
        $mail->addReplyTo($opts['reply-to'], '');
        // $mail->addReplyTo($opts['reply-to'], 'Testbesucher');
    }
    $mail->addAddress($to);

    $mail->isHTML(false);
    $mail->Subject = 'Testmail Kontaktformular (' . $env . ') ' . date('Y-m-d H:i:s');
    $mail->Body    = "Dies ist eine Testmail von test-mail.php.\r\n\r\nUmlaute: äöüß ÄÖÜ\r\nHost: " . gethostname() . "\r\n";

    $mail->send();
    echo "\nOK: mail sent, Message-ID " . $mail->getLastMessageID() . "\n";
    SimpleLogger::info('test-mail sent', ['env' => $env, 'message-id' => $mail->getLastMessageID()]);
    exit(0);
} catch (Exception $e) {
    $info = $mail ? $mail->ErrorInfo : $e->getMessage();
    fwrite(STDERR, "\nFAILED: $info\n");
    SimpleLogger::error('test-mail failed', ['env' => $env, 'error' => $info]);
    SimpleLogger::exception($e, 'PHPMailer exception');
    exit(1);
}
