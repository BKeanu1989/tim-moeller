<?php 

$allowedOrigins = [
    'http://localhost:4321',
    'https://www.example.com',
];

$origin = $_SERVER['HTTP_ORIGIN'] ?? '';

if (in_array($origin, $allowedOrigins, true)) {
    header("Access-Control-Allow-Origin: $origin");
    header('Access-Control-Allow-Credentials: true');
}

header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

session_set_cookie_params([
    'lifetime' => 0,
    'path' => '/',
    'secure' => false,
    'httponly' => true,
    'samesite' => 'Lax',
]);


// delete on prod
// header('Access-Control-Allow-Origin: http://localhost:4321');
// header('Content-Type: application/json');
// // header('Access-Control-Allow-Origin: http://localhost:4322');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true');





session_start();
error_log('verify2.php');

error_log('SESSION ID: ' . session_id());
error_log('SESSION: ' . print_r($_SESSION, true));
error_log('COOKIE: ' . print_r($_COOKIE, true));
error_log('---');

if (isset($_POST["captcha"]) && !empty($_POST["captcha"])) {
    error_log("catcha check");

    if (!isset($_SESSION["captcha"])) {
        error_log("captcha in session not found");
        // http_response_code(400);
        // echo json_encode([
        //     'session_id' => session_id()
        // ]);
    }

} else {

}

// $_SESSION["verified"] = true;


echo json_encode([
    'session_id' => session_id(),
    'session' => $_SESSION,
    'cookie' => $_COOKIE
]);



// session_start();

// header('Access-Control-Allow-Origin: http://localhost:4321');
// // header('Access-Control-Allow-Origin: http://localhost:4322');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true');

// $_SESSION["foobar"] = true;


// // header('Access-Control-Allow-Origin: http://localhost:4321');
// // header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// // header('Access-Control-Allow-Headers: Content-Type');

// $status = 'noch nix';
// if ( isset($_POST['captcha']) && ($_POST['captcha']!="") ){
// // Validation: Checking entered captcha code with the generated captcha code
// if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){
// // Note: the captcha code is compared case insensitively.
// // if you want case sensitive match, check above with strcmp()
// $status = "<p style='color:#FFFFFF; font-size:20px'>
// <span style='background-color:#FF0000;'>Entered captcha code does not match! 
// Kindly try again.</span></p>";
// }else{
// $status = "<p style='color:#FFFFFF; font-size:20px'>
// <span style='background-color:#46ab4a;'>Your captcha code is match.</span>
// </p>";	
// 	}
// }
// echo $status;

// error_log(print_r($_SESSION, 1));

// echo "<pre>";
// echo print_r($_SESSION, 1);

// echo "</pre>";