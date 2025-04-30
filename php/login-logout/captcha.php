<?php
session_start();

// Generate random 6-character CAPTCHA text
$captcha_text = strtoupper(substr(md5(time()), 0, 6));
$_SESSION['captcha_text'] = $captcha_text;

// Create image
$width = 150;
$height = 50;
$image = imagecreate($width, $height);

// Set colors
$background_color = imagecolorallocate($image, 255, 255, 255); // White
$text_color = imagecolorallocate($image, 0, 0, 0); // Black
$line_color = imagecolorallocate($image, 200, 200, 200); // Light grey

// Add random lines for noise
for ($i = 0; $i < 5; $i++) {
    imageline($image, rand(0, $width), rand(0, $height), rand(0, $width), rand(0, $height), $line_color);
}

// Add CAPTCHA text
imagestring($image, 5, 35, 18, $captcha_text, $text_color);

// Output image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>