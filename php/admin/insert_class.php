<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Assuming your form fields are named code, name, level, block, floor, category, teacherID
$code = $_POST['code'];
$name = $_POST['name'];
$level = $_POST['level'];
$block = $_POST['block'];
$floor = $_POST['floor'];
$category = $_POST['category'];
$teacherID = $_POST['teacherID'];

// Check if teacher is already assigned to another class
if (!empty($teacherID)) {
    $checkTeacherQuery = "SELECT CLASS_CODE FROM class WHERE TEACHER_ID = '$teacherID'";
    $checkResult = mysqli_query($con, $checkTeacherQuery);
    
    if (mysqli_num_rows($checkResult) > 0) {
        echo json_encode(['success' => false, 'error' => 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.']);
        exit();
    }
}

// Perform the insert query
$insertQuery = "INSERT INTO class (CLASS_CODE, CLASS_NAME, CLASS_LEVEL, CLASS_BLOCK, CLASS_FLOOR, CLASS_CAT, TEACHER_ID, ADMIN_ID) 
                VALUES ('$code', '$name', '$level', '$block', '$floor', '$category', '$teacherID', '$_SESSION[adminID]')";

if (mysqli_query($con, $insertQuery)) {
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
