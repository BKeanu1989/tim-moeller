<?php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require '/usr/www/users/timjmo/phpmailer/src/Exception.php';
// require '/usr/www/users/timjmo/phpmailer/src/PHPMailer.php';
// require '/usr/www/users/timjmo/phpmailer/src/SMTP.php';



header('Access-Control-Allow-Origin: http://localhost:4322');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');
require '/home/devkev/Projects/tim-moeller/phpmailer/src/Exception.php';
require '/home/devkev/Projects/tim-moeller/phpmailer/src/PHPMailer.php';
require '/home/devkev/Projects/tim-moeller/phpmailer/src/SMTP.php';

$ini_array = parse_ini_file("prod.ini");
// print_r($ini_array);
   

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
        $mail->isSMTP();                                            
        $mail->Host       = $ini_array["HOST"];                     
        $mail->SMTPAuth   = true;                                 
        $mail->Username   = $ini_array["USERNAME"];   
        $mail->Password   = $ini_array["SMTP"];                

        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                                 

        $mail->setFrom($email, $name);
        $mail->addAddress($ini_array["ADDADDRESS"], 'Me');     //Add a recipient

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
        // echo "Message could not be sent. Mailer Error: {$mail->ErrorInfo}";
        echo json_encode(["success" => false, "error" => $mail->ErrorInfo]);
        exit();
    }
} else {
    http_response_code(400);
    echo json_encode(["success" => false, "error" => "no post method used"]);
    exit();

}