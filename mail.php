<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

require './PHPMailer/src/Exception.php';
require './PHPMailer/src/PHPMailer.php';
require './PHPMailer/src/SMTP.php';



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
    //Server settings
    $mail->SMTPDebug = SMTP::DEBUG_SERVER;                      //Enable verbose debug output
    $mail->isSMTP();                                            //Send using SMTP
    $mail->Host       = 'smtp.example.com';                     //Set the SMTP server to send through
    $mail->SMTPAuth   = true;                                   //Enable SMTP authentication
    $mail->Username   = 'developer.kevinfechner@gmail.com';                     //SMTP username
    $mail->Password   = 'secret';                               //SMTP password
    $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;            //Enable implicit TLS encryption
    $mail->Port       = 465;                                    //TCP port to connect to; use 587 if you have set `SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS`

    //Recipients
    $mail->setFrom($email, $name);
    // $mail->addAddress('joe@example.net', 'Joe User');     //Add a recipient
    // $mail->addAddress('ellen@example.com');               //Name is optional
    // $mail->addReplyTo('info@example.com', 'Information');
    // $mail->addCC('cc@example.com');
    // $mail->addBCC('bcc@example.com');

    //Attachments
    // $mail->addAttachment('/var/tmp/file.tar.gz');         //Add attachments
    // $mail->addAttachment('/tmp/image.jpg', 'new.jpg');    //Optional name

    //Content
    // $mail->isHTML(true);                                  //Set email format to HTML
    $mail->Subject = $subject;
    $mail->Body    = $message;
    $mail->AltBody = $message;

    $mail->send();
    // echo 'Message has been sent';
    http_response_code(200);
    echo json_encode(["success" => true]);
    exit();
} catch (Exception $e) {
    echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
}



//     $empfaenger = "empfaenger@domain.de";
// $betreff = "Die Mail-Funktion";
// $from = "From: Vorname Nachname <absender@domain.de>";
// $text = "Hier lernt Ihr, wie man mit PHP Mails verschickt";
 
// mail($empfaenger, $betreff, $text, $from);

    mail("developer.kevinfechner@gmail.com", $subject, $message, "From: $name <" . $email . ">");


}