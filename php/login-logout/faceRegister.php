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

$id = $data['username'];
$captured_base64 = $data['face_image'];
$role = $data['role'];

include('../config.php');

$prefix = "data:image/jpeg;base64,";
if (strpos($captured_base64, $prefix) !== 0) {
    $captured_base64 = $prefix . $captured_base64;
}

if ($role == 'student') {
    $stmt = $con->prepare("UPDATE student SET STUDENT_FACE = ? WHERE STUDENT_ID = ?");
} else if ($role == 'teacher') {
        $stmt = $con->prepare("UPDATE teacher SET TEACHER_FACE = ? WHERE TEACHER_USERNAME = ?");
} else {
    echo json_encode([
        "success" => false,
        "message" => "❌ Invalid role!"
    ]);
    exit;
}
$stmt->bind_param("ss", $captured_base64, $id);

if ($stmt->execute()) {
    echo json_encode(["success" => true, "message" => "✅ Registered successfully!"]);
} else {
    echo json_encode(["success" => false, "message" => "❌ Error: " . $stmt->error]);
}

$stmt->close();
$con->close();
?>
