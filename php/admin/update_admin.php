<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/loginAdmin.php");
    exit();
}

// Assuming your form fields are named adminId, uname, fname, phone, pwd
$fullname = $_POST['fname']; // Corrected variable name
$phoneN = $_POST['phone'];
$pswd = $_POST['pwd'];
$oldID = $_POST['old_id'];

// Update the admin's data in the database
$updateQuery = "UPDATE admin SET  
    `ADMIN_NAME`='$fullname', 
    `ADMIN_PHONE`='$phoneN', 
    `ADMIN_PWD`='$pswd' 
    WHERE `ADMIN_ID`='$oldID'";

if (mysqli_query($con, $updateQuery)) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false]);
}
?>
