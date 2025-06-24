<?php
session_start();
include "../config.php";

// Set content type to JSON
header('Content-Type: application/json');

$name = $_POST['name'];
$level = $_POST['level'];
$block = $_POST['block'];
$floor = $_POST['floor'];
$category = $_POST['category'];
$teacherID = ($_POST['teacherID'] === "") ? NULL : $_POST['teacherID'];
$adminID = $_SESSION['adminID'];
$classCode = $_POST['cCode'];

// If assigning a teacher, check if teacher is already assigned to another class
if ($teacherID !== NULL) {
    $checkTeacherQuery = "SELECT CLASS_CODE FROM class WHERE TEACHER_ID = '$teacherID' AND CLASS_CODE != '$classCode'";
    $checkResult = mysqli_query($con, $checkTeacherQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
        exit();
    }
}

// Perform the update query
$updateQuery = "UPDATE `class` SET `CLASS_NAME`='$name', `CLASS_LEVEL`='$level', `CLASS_FLOOR`='$floor',
                `CLASS_BLOCK`='$block', `CLASS_CAT`='$category', `TEACHER_ID`=" . ($teacherID === NULL ? "NULL" : "'$teacherID'") . ", `ADMIN_ID`='$adminID' WHERE `CLASS_CODE`='$classCode'";

// Log the query for debugging
error_log($updateQuery);

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true]);
} else {
    $error = mysqli_error($con);
    if (strpos($error, 'unique_teacher') !== false) {
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
    } else {
        echo json_encode(['success' => false, 'error' => $error]);
    }
}
?>