<?php
include("../config.php");

$paymentID = $_POST['paymentID'];
$status = $_POST['status'];

// Perform the database update
// Use prepared statements to prevent SQL injection

$sql = "UPDATE payment SET PAYMENT_STATUS = ? WHERE PAYMENT_ID = ?";
$stmt = $con->prepare($sql);
$stmt->bind_param('si', $status, $paymentID);
$result = $stmt->execute();
$stmt->close();

// Return a response
if ($result) {
    echo 'success';
} else {
    echo 'error';
}

$con->close();
?>
