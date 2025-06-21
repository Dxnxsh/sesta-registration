<?php
session_start();
include "../config.php";

// Sanitize input data
$name = SecuritySanitizer::sanitizeForDB($_POST['name'] ?? '', 'class_name', 'CLASS_NAME');
$level = SecuritySanitizer::sanitizeForDB($_POST['level'] ?? '', 'class_level', 'CLASS_LEVEL');
$block = SecuritySanitizer::sanitizeForDB($_POST['block'] ?? '', 'class_block', 'CLASS_BLOCK');
$floor = SecuritySanitizer::sanitizeForDB($_POST['floor'] ?? '', 'floor', 'CLASS_FLOOR');
$category = SecuritySanitizer::sanitizeForDB($_POST['category'] ?? '', 'class_category', 'CLASS_CAT');
$teacherID = ($_POST['teacherID'] === "") ? NULL : SecuritySanitizer::sanitizeForDB($_POST['teacherID'], 'id', 'TEACHER_ID');
$adminID = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');
$classCode = SecuritySanitizer::sanitizeForDB($_POST['cCode'] ?? '', 'class_code', 'CLASS_CODE');

// If assigning a teacher, check if teacher is already assigned to another class
if ($teacherID !== NULL) {
    $stmt = $con->prepare("SELECT CLASS_CODE FROM class WHERE TEACHER_ID = ? AND CLASS_CODE != ?");
    $stmt->bind_param("ss", $teacherID, $classCode);
    $stmt->execute();
    $checkResult = $stmt->get_result();
    
    if ($checkResult->num_rows > 0) {
        SecuritySanitizer::logSecurityEvent('teacher_multiple_class_attempt', [
            'teacher_id' => $teacherID,
            'existing_class' => $checkResult->fetch_assoc()['CLASS_CODE'],
            'attempted_class' => $classCode,
            'admin_id' => $adminID
        ]);
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
        exit();
    }
    $stmt->close();
}

// Perform the update query with prepared statement
$stmt = $con->prepare("UPDATE `class` SET `CLASS_NAME`=?, `CLASS_LEVEL`=?, `CLASS_FLOOR`=?, `CLASS_BLOCK`=?, `CLASS_CAT`=?, `TEACHER_ID`=?, `ADMIN_ID`=? WHERE `CLASS_CODE`=?");
$stmt->bind_param("ssssssss", $name, $level, $floor, $block, $category, $teacherID, $adminID, $classCode);

if ($stmt->execute()) {
    SecuritySanitizer::logSecurityEvent('class_updated', [
        'class_code' => $classCode,
        'admin_id' => $adminID,
        'teacher_id' => $teacherID
    ]);
    echo json_encode(['success' => true]);
} else {
    $error = SecuritySanitizer::sanitize($stmt->error, 'name');
    SecuritySanitizer::logSecurityEvent('class_update_failed', [
        'class_code' => $classCode,
        'admin_id' => $adminID,
        'error' => $error
    ]);
    if (strpos($error, 'unique_teacher') !== false) {
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
    } else {
        echo json_encode(['success' => false, 'error' => 'Database error occurred.']);
    }
}
$stmt->close();
?>