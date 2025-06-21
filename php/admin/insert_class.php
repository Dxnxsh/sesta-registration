<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize all input data with proper types
$code = SecuritySanitizer::sanitizeForDB($_POST['code'] ?? '', 'class_code', 'CLASS_CODE');
$name = SecuritySanitizer::sanitizeForDB($_POST['name'] ?? '', 'class_name', 'CLASS_NAME');
$level = SecuritySanitizer::sanitizeForDB($_POST['level'] ?? '', 'class_level', 'CLASS_LEVEL');
$block = SecuritySanitizer::sanitizeForDB($_POST['block'] ?? '', 'class_block', 'CLASS_BLOCK');
$floor = SecuritySanitizer::sanitizeForDB($_POST['floor'] ?? '', 'floor', 'CLASS_FLOOR');
$category = SecuritySanitizer::sanitizeForDB($_POST['category'] ?? '', 'class_category', 'CLASS_CAT');
$teacherID = !empty($_POST['teacherID']) ? SecuritySanitizer::sanitizeForDB($_POST['teacherID'], 'id', 'TEACHER_ID') : NULL;
$adminID = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');

// Check if teacher is already assigned to another class using prepared statement
if ($teacherID !== NULL) {
    $stmt = $con->prepare("SELECT CLASS_CODE FROM class WHERE TEACHER_ID = ?");
    $stmt->bind_param("s", $teacherID);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        SecuritySanitizer::logSecurityEvent('teacher_duplicate_assignment_attempt', [
            'teacher_id' => $teacherID,
            'admin_id' => $adminID,
            'attempted_class' => $code
        ]);
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
        exit();
    }
    $stmt->close();
}

// Perform the insert query using prepared statement
$stmt = $con->prepare("INSERT INTO class (CLASS_CODE, CLASS_NAME, CLASS_LEVEL, CLASS_BLOCK, CLASS_FLOOR, CLASS_CAT, TEACHER_ID, ADMIN_ID) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
$stmt->bind_param("ssssssss", $code, $name, $level, $block, $floor, $category, $teacherID, $adminID);

if ($stmt->execute()) {
    SecuritySanitizer::logSecurityEvent('class_created', [
        'class_code' => $code,
        'class_name' => $name,
        'teacher_id' => $teacherID,
        'admin_id' => $adminID
    ]);
    echo json_encode(['success' => true]);
} else {
    $error = SecuritySanitizer::sanitize($stmt->error, 'name');
    SecuritySanitizer::logSecurityEvent('class_creation_failed', [
        'class_code' => $code,
        'admin_id' => $adminID,
        'error' => $error
    ]);
    
    if (strpos($error, 'unique_teacher') !== false || strpos($error, 'Duplicate') !== false) {
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error occurred while creating class.']);
    }
}
$stmt->close();
?>
