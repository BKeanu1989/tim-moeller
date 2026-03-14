<?php

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



//     $empfaenger = "empfaenger@domain.de";
// $betreff = "Die Mail-Funktion";
// $from = "From: Vorname Nachname <absender@domain.de>";
// $text = "Hier lernt Ihr, wie man mit PHP Mails verschickt";
 
// mail($empfaenger, $betreff, $text, $from);

    mail("developer.kevinfechner@gmail.com", $subject, $message, "From: $name <" . $email . ">");

    http_response_code(200);
    echo json_encode(["success" => true]);
    exit();

}