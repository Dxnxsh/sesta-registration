<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/loginAdmin.php");
    exit();
}

// Assuming your form fields are named adminId, uname, fname, phone, pwd
$id = $_POST['adminId'];
$uname = $_POST['uname'];
$fullname = $_POST['fname'];
$phoneN = $_POST['phone'];
$pswd = $_POST['pwd'];

// Perform the insert query
$insertQuery = "INSERT INTO admin (ADMIN_ID, ADMIN_NAME, ADMIN_USERNAME, ADMIN_PHONE, ADMIN_PWD) 
                VALUES ('$id', '$fullname', '$uname', '$phoneN', '$pswd')";

if (mysqli_query($con, $insertQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
