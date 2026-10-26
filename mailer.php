<?php

use PHPMailer\PHPMailer\PHPMailer;

require_once __DIR__ . '/phpmailer/src/Exception.php';
require_once __DIR__ . '/phpmailer/src/PHPMailer.php';
require_once __DIR__ . '/phpmailer/src/SMTP.php';


function createMailer(array $ini_array) {
    $mail = new PHPMailer(true);

    $mail->isSMTP();
    $mail->Host       = $ini_array["HOST"];       
    $mail->SMTPAuth   = true;
    $mail->Username   = $ini_array["USERNAME"];   // full mailbox address
    $mail->Password   = $ini_array["SMTP"];
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
    $mail->Port       = 587;
    $mail->Timeout    = 15;
    $mail->CharSet    = PHPMailer::CHARSET_UTF8;

    $sender = !empty($ini_array["SENDER"]) ? $ini_array["SENDER"] : $ini_array["USERNAME"];
    $mail->setFrom($sender, 'Kontaktformular');

    return $mail;
}
