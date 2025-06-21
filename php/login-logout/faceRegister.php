<?php
header('Content-Type: application/json');

// Get raw POST data and decode JSON
$data = json_decode(file_get_contents("php://input"), true);

if (!$data) {
    echo json_encode(["success" => false, "message" => "Invalid JSON input"]);
    exit;
}

// Validate required keys
if (!isset($data['username'], $data['face_image'], $data['role'])) {
    echo json_encode(["success" => false, "message" => "Missing required fields"]);
    exit;
}

// Sanitize and validate input data
$id = SecuritySanitizer::sanitize($data['username'], 'username');
$captured_base64 = SecuritySanitizer::sanitize($data['face_image'], 'longtext');
$role = SecuritySanitizer::sanitize($data['role'], 'status');

// Additional validation
if (empty($id) || empty($captured_base64) || empty($role)) {
    SecuritySanitizer::logSecurityEvent('face_register_invalid_input', [
        'username' => $data['username'] ?? '',
        'role' => $data['role'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Invalid input data"]);
    exit;
}

include('../config.php');

// Validate and process base64 image data
$prefix = "data:image/jpeg;base64,";
if (strpos($captured_base64, $prefix) !== 0) {
    $captured_base64 = $prefix . $captured_base64;
}

// Additional validation for base64 image
if (!preg_match('/^data:image\/(jpeg|jpg|png);base64,[a-zA-Z0-9+\/=]+$/', $captured_base64)) {
    SecuritySanitizer::logSecurityEvent('face_register_invalid_image', [
        'username' => $id,
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "Invalid image format"]);
    exit;
}

// Validate role parameter
if ($role == 'student') {
    $stmt = $con->prepare("UPDATE student SET STUDENT_FACE = ? WHERE STUDENT_ID = ?");
} else if ($role == 'teacher') {
    $stmt = $con->prepare("UPDATE teacher SET TEACHER_FACE = ? WHERE TEACHER_USERNAME = ?");
} else {
    SecuritySanitizer::logSecurityEvent('face_register_invalid_role', [
        'username' => $id,
        'invalid_role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode([
        "success" => false,
        "message" => "❌ Invalid role!"
    ]);
    exit;
}
$stmt->bind_param("ss", $captured_base64, $id);

if ($stmt->execute()) {
    SecuritySanitizer::logSecurityEvent('face_register_success', [
        'username' => $id,
        'role' => $role,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => true, "message" => "✅ Registered successfully!"]);
} else {
    SecuritySanitizer::logSecurityEvent('face_register_failed', [
        'username' => $id,
        'role' => $role,
        'error' => $stmt->error,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    echo json_encode(["success" => false, "message" => "❌ Error: " . SecuritySanitizer::sanitize($stmt->error, 'name')]);
}

$stmt->close();
$con->close();
?>
