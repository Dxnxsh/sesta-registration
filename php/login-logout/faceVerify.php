<?php
header('Content-Type: application/json');

include('../config.php');

// Get raw POST data and decode JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    SecuritySanitizer::logSecurityEvent('face_verify_invalid_json', [
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit;
}

// Validate required keys
if (!isset($data['username'], $data['face_image'], $data['role'])) {
    SecuritySanitizer::logSecurityEvent('face_verify_missing_fields', [
        'provided_keys' => array_keys($data),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Sanitize inputs
$id = SecuritySanitizer::sanitize($data['username'], 'id');
$role = SecuritySanitizer::sanitize($data['role'], 'status');

// Validate role
$validRoles = ['student', 'teacher'];
if (!in_array($role, $validRoles)) {
    SecuritySanitizer::logSecurityEvent('face_verify_invalid_role', [
        'username' => $data['username'] ?? '',
        'invalid_role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode([
        "success" => false,
        "message" => "❌ Invalid role!"
    ]);
    exit;
}

// Validate face image data
$captured_base64 = $data['face_image'];
if (empty($captured_base64) || !is_string($captured_base64)) {
    SecuritySanitizer::logSecurityEvent('face_verify_invalid_image', [
        'username' => $data['username'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Invalid face image data"]);
    exit;
}

// Validate ID format based on role
if ($role == 'student') {
    $id = SecuritySanitizer::sanitize($id, 'id', 'STUDENT_ID');
} else if ($role == 'teacher') {
    $id = SecuritySanitizer::sanitize($id, 'username', 'TEACHER_USERNAME');
}

if (!$id) {
    SecuritySanitizer::logSecurityEvent('face_verify_invalid_id', [
        'username' => $data['username'] ?? '',
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Invalid user ID"]);
    exit;
}

// Connect to MySQL and fetch stored face data
if ($role == 'student') {
    $stmt = $con->prepare("SELECT STUDENT_FACE FROM student WHERE STUDENT_ID = ?");
} else if ($role == 'teacher') {
    $stmt = $con->prepare("SELECT TEACHER_FACE FROM teacher WHERE TEACHER_USERNAME = ?");
}

$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->bind_result($stored_base64);
$stmt->fetch();
$stmt->close();

if (!$stored_base64) {
    SecuritySanitizer::logSecurityEvent('face_verify_user_not_found', [
        'username' => $id,
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode([
        "success" => false,
        "message" => "❌ User not found!"
    ]);
    $con->close();
    exit;
}

// Sanitize MIME prefix if provided via POST
$prefix = 'data:image/jpeg;base64'; // Default safe value
if (isset($_POST['mime_prefix'])) {
    $providedPrefix = SecuritySanitizer::sanitize($_POST['mime_prefix'], 'name');
    // Only allow specific image MIME types
    $allowedPrefixes = [
        'data:image/jpeg;base64',
        'data:image/png;base64',
        'data:image/jpg;base64'
    ];
    if (in_array($providedPrefix, $allowedPrefixes)) {
        $prefix = $providedPrefix;
    }
}

if (strpos($captured_base64, $prefix) !== 0) {
    $captured_base64 = $prefix . "," . $captured_base64;
}
// Send base64 directly to DeepFace API
$payload = json_encode([
    "img1" => $stored_base64,
    "img2" => $captured_base64,
    "detector_backend"=> "retinaface"
]);

//echo "<h3>Captured image</h3>";
//echo "<img src='" . htmlspecialchars($captured_base64) . "' style='border: 2px solid #333;'><br><br>";

$ch = curl_init('https://deepface.meordnsh.dev/verify'); // Docker url
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);

$response = curl_exec($ch);
curl_close($ch);

$verified = false;
$result = json_decode($response, true);
//echo $result;
if ($result && isset($result['verified']) && $result['verified']) {
    //echo "✅ Login successful!";
    $verified = true;
} else {
    //echo "❌ Face verification failed!";
}

if ($verified) {
    SecuritySanitizer::logSecurityEvent('face_verify_success', [
        'username' => $id,
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode([
        "success" => true,
        "message" => "Face verified successfully"
    ]);
} else {
    SecuritySanitizer::logSecurityEvent('face_verify_failed', [
        'username' => $id,
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode([
        "success" => false,
        "message" => "Face verification failed"
    ]);
}

$con->close();
?>