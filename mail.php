<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require '/usr/www/users/timjmo/phpmailer/src/Exception.php';
require '/usr/www/users/timjmo/phpmailer/src/PHPMailer.php';
require '/usr/www/users/timjmo/phpmailer/src/SMTP.php';



$errors = [
    "name" => false,
    "email" => false,
    "subject" => false,
    "message" => false
];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $any_error_found = false;
    if (!isset($_POST["name"]) || empty($_POST["name"])) {
        $errors["name"] = true;
        $any_error_found = true;
    }

    if (!isset($_POST["email"]) || empty($_POST["email"])) {
        $errors["email"] = true;
        $any_error_found = true;

    }

    if (!isset($_POST["subject"]) || empty($_POST["subject"])) {
        $errors["subject"] = true;
        $any_error_found = true;

    }

    if (!isset($_POST["message"]) || empty($_POST["message"])) {
        $any_error_found = true;
        $errors["message"] = true;
    }

    if ($any_error_found) {
        // return json_encode()
        http_response_code(400);        

        echo json_encode(["success" => false]);
        exit();
    }
        
        

    $name = $_POST["name"];
    $email = $_POST["email"];
    $subject = $_POST["subject"];
    $message = $_POST["message"];
    $mail = new PHPMailer(true);

    try {
        $mail->isSMTP();                                            //Send using SMTP
        $mail->Host       = 'smtp.gmail.com';                     //Set the SMTP server to send through
        $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
        $mail->Username   = 'developer.kevinfechner@gmail.com';                     //SMTP username
        $mail->Password   = 'cpmd lkgj uqcj pdad';                               //SMTP password

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                                 //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

        // OPTION B: implicit TLS on 465
        // $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        // $mail->Port       = 465;

        //Recipients
        // $mail->setFrom('developer.kevinfechner@gmail.com', 'Kevin');
        $mail->setFrom($email, $name);
        $mail->addAddress('developer.kevinfechner@gmail.com', 'Me');     //Add a recipient

        $mail->isHTML(false);

        $preMessage = "Du hast eine Email von " . $name . " mit der Email Adresse " . $email . " erhalten.\r\n";

        $mail->Subject = $subject;
        $mail->Body    = $preMessage . $message;
        $mail->AltBody = $preMessage . $message;

        $mail->send();
        http_response_code(200);
        echo json_encode(["success" => true]);
        exit();
    } catch (Exception $e) {
        echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
    }



}