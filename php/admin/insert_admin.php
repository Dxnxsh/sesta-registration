<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Assuming your form fields are named adminId, uname, fname, phone, pwd
$id = $_POST['adminId'];
$uname = $_POST['uname'];
$fullname = $_POST['fname'];
$phoneN = $_POST['phone'];
$pswd = $_POST['pwd'];

// Check if admin ID already exists
$checkQuery = "SELECT ADMIN_ID FROM admin WHERE ADMIN_ID = '$id'";
$checkResult = mysqli_query($con, $checkQuery);

if (mysqli_num_rows($checkResult) > 0) {
    // Admin ID already exists
    echo json_encode(['success' => false, 'message' => 'Admin ID already exists']);
} else {
    // Perform the insert query
    $insertQuery = "INSERT INTO admin (ADMIN_ID, ADMIN_NAME, ADMIN_USERNAME, ADMIN_PHONE, ADMIN_PWD) 
                    VALUES ('$id', '$fullname', '$uname', '$phoneN', '$pswd')";

    if (mysqli_query($con, $insertQuery)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
}
?>
