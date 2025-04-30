<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/loginAdmin.php");
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

// Perform the insert query
$insertQuery = "INSERT INTO class (CLASS_CODE, CLASS_NAME, CLASS_LEVEL, CLASS_BLOCK, CLASS_FLOOR, CLASS_CAT, TEACHER_ID, ADMIN_ID) 
                VALUES ('$code', '$name', '$level', '$block', '$floor', '$category', '$teacherID', '$_SESSION[adminID]')";

if (mysqli_query($con, $insertQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
