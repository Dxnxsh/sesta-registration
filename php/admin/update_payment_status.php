<?php
session_start();
include("../config.php");

// Check admin authorization
if (!isset($_SESSION['adminID'])) {
    echo 'unauthorized';
    exit();
}

try {
    // Sanitize and validate input data
    $paymentID = SecuritySanitizer::sanitizeForDB($_POST['paymentID'] ?? '', 'number', 'PAYMENT_ID');
    $status = SecuritySanitizer::sanitizeForDB($_POST['status'] ?? '', 'enum', 'PAYMENT_STATUS');

    // Validate required fields
    if (empty($paymentID) || empty($status)) {
        echo 'invalid_input';
        exit();
    }

    // Validate status is one of allowed values
    $allowedStatuses = ['Pending', 'Completed', 'Failed', 'Refunded'];
    if (!in_array($status, $allowedStatuses)) {
        echo 'invalid_status';
        exit();
    }

    // Check for malicious input
    if (detectMaliciousInput($paymentID) || detectMaliciousInput($status)) {
        SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
            'field' => 'payment_update',
            'admin_id' => $_SESSION['adminID']
        ]);
        echo 'invalid_input';
        exit();
    }

} catch (InvalidArgumentException $e) {
    echo 'validation_error';
    exit();
}

// Perform the database update using prepared statements
$sql = "UPDATE payment SET PAYMENT_STATUS = ? WHERE PAYMENT_ID = ?";
$stmt = $con->prepare($sql);

if (!$stmt) {
    echo 'database_error';
    exit();
}

$stmt->bind_param('si', $status, $paymentID);
$result = $stmt->execute();

if ($result) {
    SecuritySanitizer::logSecurityEvent('payment_status_updated', [
        'payment_id' => $paymentID,
        'new_status' => $status,
        'updated_by' => $_SESSION['adminID']
    ]);
    echo 'success';
} else {
    SecuritySanitizer::logSecurityEvent('payment_update_failed', [
        'payment_id' => $paymentID,
        'error' => $stmt->error,
        'attempted_by' => $_SESSION['adminID']
    ]);
    echo 'error';
}

$stmt->close();
$con->close();
?>
