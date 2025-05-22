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

// Connect to MySQL
include('../config.php');
if ($role == 'student') {
    $stmt = $con->prepare("SELECT STUDENT_FACE FROM student WHERE STUDENT_ID = ?");
} else if ($role == 'teacher') {
        $stmt = $con->prepare("SELECT TEACHER_FACE FROM teacher WHERE TEACHER_USERNAME = ?");
} else {
    echo json_encode([
        "success" => false,
        "message" => "❌ Invalid role!"
    ]);
    exit;
}
$stmt->bind_param("s", $id);
$stmt->execute();
$stmt->bind_result($stored_base64);
$stmt->fetch();
$stmt->close();
$con->close();
if (!$stored_base64) {
    echo json_encode([
        "success" => false,
        "message" => "❌ User not found!"
    ]);
    exit;
}

$prefix = $_POST['mime_prefix'] ?? 'data:image/jpeg;base64';

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

$ch = curl_init('http://100.92.169.116:5005/verify'); // service name in Docker
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
    echo json_encode([
        "success" => true,
        "message" => "Face verified successfully"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "Face verification failed"
    ]);
}
?>