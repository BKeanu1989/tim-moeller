<?php 
session_start();

header('Content-Type: application/json');


function return_custom_error() {
	http_response_code(400);
	echo json_encode([
		'session_id' => session_id(),
		'session' => $_SESSION,
		'cookie' => $_COOKIE
	]);
}

if ( isset($_POST['captcha']) && ($_POST['captcha']!="") && isset($_SESSION["captcha"]) ){
// Validation: Checking entered captcha code with the generated captcha code
	if(strcasecmp($_SESSION['captcha'], $_POST['captcha']) != 0){
	// Note: the captcha code is compared case insensitively.
	// if you want case sensitive match, check above with strcmp()
		return_custom_error();
	} else {
		$_SESSION["verified"] = true;
		echo json_encode([
			'success' => true,
			'session' => $_SESSION 
		]);
	}
} else {
	return_custom_error();
}




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
// echo $status;

// error_log(print_r($_SESSION, 1));

// echo "<pre>";
// echo print_r($_SESSION, 1);

// echo "</pre>";