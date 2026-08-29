<?php
session_start();

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// require '/usr/www/users/timjmo/phpmailer/src/Exception.php';
// require '/usr/www/users/timjmo/phpmailer/src/PHPMailer.php';
// require '/usr/www/users/timjmo/phpmailer/src/SMTP.php';



header('Access-Control-Allow-Origin: http://localhost:4321');
// header('Access-Control-Allow-Origin: http://localhost:4322');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Headers: Content-Type');

require '/home/devkev/Projects/tim-moeller/phpmailer/src/Exception.php';
require '/home/devkev/Projects/tim-moeller/phpmailer/src/PHPMailer.php';
require '/home/devkev/Projects/tim-moeller/phpmailer/src/SMTP.php';



$ini_array = parse_ini_file("dev.ini");
// $ini_array = parse_ini_file("prod.ini");
error_log(print_r($ini_array, 1));
   

$errors = [
    "name" => false,
    "email" => false,
    "subject" => false,
    "message" => false
];
// print_r($_SESSION,1);

// fwrite('php://stdout', "Output to standard output\n");
// Write to STDOUT
// fwrite(STDOUT, "This is normal output\n");

// Write to STDERR
// fwrite(STDERR, "This is an error message\n");
error_log(print_r($_SESSION, 1));

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $any_error_found = false;
    $error_message = "";
    if (!isset($_SESSION["verified"])) {
        $any_error_found = true;
        $error_message = "not verified";
    }
    // if ( isset($_POST['captcha']) && ($_POST['captcha']!="") && isset($_SESSION["captcha"]) ){
    // // Validation: Checking entered captcha code with the generated captcha code
    //     if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){
    //         // Note: the captcha code is compared case insensitively.
    //         // if you want case sensitive match, check above with strcmp()
    //         $status = "<p style='color:#FFFFFF; font-size:20px'>
    //         <span style='background-color:#FF0000;'>Entered captcha code does not match! 
    //         Kindly try again.</span></p>";
    //         $any_error_found = true;
    //         $error_message = "captcha code does not match or is not present"; 
    //     }else{
    //         // $status = "<p style='color:#FFFFFF; font-size:20px'>
    //         // <span style='background-color:#46ab4a;'>Your captcha code is match.</span>
    //         // </p>";	
    // 		$_SESSION["verified"] = true;

    //     }
    // } else {
    //     $any_error_found = true;
    //     $error_message .= "captcha is not present";
    // }


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
        error_log("any error");
        error_log($error_message);
        http_response_code(400);        

        echo json_encode(["success" => false, "message" => $error_message]);
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

// APPPASSWORD = "" 



        $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $mail->Port       = 587;                                 

        $mail->setFrom($email, $name);
        $mail->addAddress($ini_array["ADDADDRESS"], 'Me');     //Add a recipient

        $mail->isHTML(false);

        // $mail->setTo();

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