<?php
session_start();
include "../config.php";



$name = $_POST['name'];
$level = $_POST['level'];
$block = $_POST['block'];
$floor = $_POST['floor'];
$category = $_POST['category'];
$teacherID = ($_POST['teacherID'] === "") ? NULL : $_POST['teacherID'];
$adminID = $_SESSION['adminID']; // Corrected syntax
$classCode = $_POST['cCode'];

// Perform the insert query
$updateQuery = "UPDATE `class` SET `CLASS_NAME`='$name', `CLASS_LEVEL`='$level', `CLASS_FLOOR`='$floor',
                `CLASS_BLOCK`='$block', `CLASS_CAT`='$category', `TEACHER_ID`=" . ($teacherID === NULL ? "NULL" : "'$teacherID'") . ", `ADMIN_ID`='$adminID' WHERE `CLASS_CODE`='$classCode'";

// Log the query for debugging
error_log($updateQuery);

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => mysqli_error($con)]);
}