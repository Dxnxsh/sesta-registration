<?php
// Include security configuration
include('../config.php');

// Basic rate limiting - prevent too frequent requests
if (isset($_SESSION['last_captcha_time'])) {
    $time_diff = time() - $_SESSION['last_captcha_time'];
    if ($time_diff < 2) { // Minimum 2 seconds between requests
        SecuritySanitizer::logSecurityEvent('captcha_rate_limit_exceeded', [
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
            'time_diff' => $time_diff
        ]);
        // Output a simple error image instead of blocking completely
        $width = 150;
        $height = 50;
        $image = imagecreate($width, $height);
        $bg = imagecolorallocate($image, 255, 255, 255);
        $text = imagecolorallocate($image, 255, 0, 0);
        imagestring($image, 3, 20, 20, 'WAIT...', $text);
        header('Content-Type: image/png');
        imagepng($image);
        imagedestroy($image);
        exit;
    }
}

// Generate more secure random 6-character CAPTCHA text
$captcha_text = '';
$characters = 'ABCDEFGHJKLMNPQRSTUVWXYZ23456789'; // Avoid confusing chars like 0, O, 1, I
for ($i = 0; $i < 6; $i++) {
    $captcha_text .= $characters[random_int(0, strlen($characters) - 1)];
}

$_SESSION['captcha_text'] = $captcha_text;
$_SESSION['last_captcha_time'] = time();

// Log CAPTCHA generation for security monitoring
SecuritySanitizer::logSecurityEvent('captcha_generated', [
    'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown',
    'session_id' => session_id()
]);

// Create image with enhanced security
$width = 150;
$height = 50;
$image = imagecreate($width, $height);

// Set colors with random variations for security
$bg_variation = random_int(240, 255);
$background_color = imagecolorallocate($image, $bg_variation, $bg_variation, $bg_variation);
$text_color = imagecolorallocate($image, random_int(0, 50), random_int(0, 50), random_int(0, 50));
$line_color = imagecolorallocate($image, random_int(180, 220), random_int(180, 220), random_int(180, 220));

// Add random lines for noise (more secure)
for ($i = 0; $i < random_int(3, 7); $i++) {
    imageline($image, random_int(0, $width), random_int(0, $height), 
              random_int(0, $width), random_int(0, $height), $line_color);
}

// Add CAPTCHA text with slight random positioning
$x_pos = random_int(30, 40);
$y_pos = random_int(15, 22);
imagestring($image, 5, $x_pos, $y_pos, $captcha_text, $text_color);

// Output image
header('Content-Type: image/png');
imagepng($image);
imagedestroy($image);
?>