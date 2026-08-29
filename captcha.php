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

session_start();

error_log('CAPTCHA.php');
error_log('SESSION ID: ' . session_id());
error_log('SESSION: ' . print_r($_SESSION, true));
error_log('COOKIE: ' . print_r($_COOKIE, true));
error_log('---');


// delete on prod
// header('Access-Control-Allow-Origin: http://localhost:4321');
// // header('Access-Control-Allow-Origin: http://localhost:4322');
// header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
// header('Access-Control-Allow-Headers: Content-Type');
// header('Access-Control-Allow-Credentials: true');





//You can customize your captcha settings here



$captcha_code = '';
$captcha_image_height = 50;
$captcha_image_width = 130;
$total_characters_on_image = 5;

//The characters that can be used in the CAPTCHA code.
//avoid all confusing characters and numbers (For example: l, 1 and i)
$possible_captcha_letters = 'bcdfghjkmnpqrstvwxyz23456789';
$captcha_font = './monospaced.ttf';

$random_captcha_dots = 50;
$random_captcha_lines = 25;
$captcha_text_color = "0x142864";
$captcha_noise_color = "0x142864";


$count = 0;
while ($count < $total_characters_on_image) { 
$captcha_code .= substr(
	$possible_captcha_letters,
	mt_rand(0, strlen($possible_captcha_letters)-1),
	1);
$count++;
}

$captcha_font_size = $captcha_image_height * 0.65;
$captcha_image = imagecreate(
	$captcha_image_width,
	$captcha_image_height
	);

/* setting the background, text and noise colours here */
$background_color = imagecolorallocate(
	$captcha_image,
	255,
	255,
	255
	);

$array_text_color = hextorgb($captcha_text_color);
$captcha_text_color = imagecolorallocate(
	$captcha_image,
	$array_text_color['red'],
	$array_text_color['green'],
	$array_text_color['blue']
	);

$array_noise_color = hextorgb($captcha_noise_color);
$image_noise_color = imagecolorallocate(
	$captcha_image,
	$array_noise_color['red'],
	$array_noise_color['green'],
	$array_noise_color['blue']
	);

/* Generate random dots in background of the captcha image */
for( $count=0; $count<$random_captcha_dots; $count++ ) {
imagefilledellipse(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	2,
	3,
	$image_noise_color
	);
}

/* Generate random lines in background of the captcha image */
for( $count=0; $count<$random_captcha_lines; $count++ ) {
imageline(
	$captcha_image,
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	mt_rand(0,$captcha_image_width),
	mt_rand(0,$captcha_image_height),
	$image_noise_color
	);
}

/* Create a text box and add 6 captcha letters code in it */
$text_box = imagettfbbox(
	$captcha_font_size,
	0,
	$captcha_font,
	$captcha_code
	); 
$x = ($captcha_image_width - $text_box[4])/2;
$y = ($captcha_image_height - $text_box[5])/2;
imagettftext(
	$captcha_image,
	$captcha_font_size,
	0,
	$x,
	$y,
	$captcha_text_color,
	$captcha_font,
	$captcha_code
	);


$_SESSION['captcha'] = $captcha_code;
/* Show captcha image in the html page */
// defining the image type to be shown in browser widow
// header('Content-Type: image/jpeg'); 
// imagejpeg($captcha_image); //showing the image
// imagedestroy($captcha_image); //destroying the image instance





error_log("setting captcha in .php");
error_log(print_r($_SESSION, 1));

ob_start();
imagejpeg($captcha_image); // Use imagepng() or imagegif() as needed
$imageData = ob_get_clean();

// Encode to Base64
$base64String = base64_encode($imageData);

// Optional: Create a Data URI for embedding in HTML/CSS
// $dataUri = 'data:image/jpeg;base64,' . $base64String;

// Clean up GD resource
imagedestroy($captcha_image);

echo json_encode([
	// "session_id" => session_id(),
	"session_id" => session_id(),
	"status" => 200,
	"image" => $base64String
]);




function hextorgb ($hexstring){
  $integar = hexdec($hexstring);
  return array("red" => 0xFF & ($integar >> 0x10),
               "green" => 0xFF & ($integar >> 0x8),
               "blue" => 0xFF & $integar);
}
