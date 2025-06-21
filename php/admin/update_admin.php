<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

try {
    // Sanitize and validate input data
    $fullname = SecuritySanitizer::sanitizeForDB($_POST['fname'] ?? '', 'name', 'ADMIN_NAME');
    $phoneN = SecuritySanitizer::sanitizeForDB($_POST['phone'] ?? '', 'phone', 'ADMIN_PHONE');
    $pswd = SecuritySanitizer::sanitizeForDB($_POST['pwd'] ?? '', 'password', 'ADMIN_PWD');
    $oldID = SecuritySanitizer::sanitizeForDB($_POST['old_id'] ?? '', 'id', 'ADMIN_ID');

    // Validate required fields
    if (empty($fullname) || empty($phoneN) || empty($pswd) || empty($oldID)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Check for malicious input
    $inputs = [$fullname, $phoneN, $pswd, $oldID];
    foreach ($inputs as $input) {
        if (detectMaliciousInput($input)) {
            SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                'field' => 'admin_update',
                'admin_id' => $_SESSION['adminID']
            ]);
            echo json_encode(['success' => false, 'message' => 'Invalid input detected']);
            exit();
        }
    }

} catch (InvalidArgumentException $e) {
    echo json_encode(['success' => false, 'message' => 'Invalid input format: ' . $e->getMessage()]);
    exit();
}

// Update the admin's data using prepared statement
$updateQuery = "UPDATE admin SET ADMIN_NAME = ?, ADMIN_PHONE = ?, ADMIN_PWD = ? WHERE ADMIN_ID = ?";
$stmt = mysqli_prepare($con, $updateQuery);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database preparation error']);
    exit();
}

mysqli_stmt_bind_param($stmt, "ssss", $fullname, $phoneN, $pswd, $oldID);

if (mysqli_stmt_execute($stmt)) {
    SecuritySanitizer::logSecurityEvent('admin_updated', [
        'admin_id' => $oldID,
        'updated_by' => $_SESSION['adminID']
    ]);
    echo json_encode(['success' => true]);
} else {
    SecuritySanitizer::logSecurityEvent('admin_update_failed', [
        'admin_id' => $oldID,
        'error' => mysqli_error($con),
        'attempted_by' => $_SESSION['adminID']
    ]);
    echo json_encode(['success' => false, 'message' => 'Database update failed']);
}

mysqli_stmt_close($stmt);
?>
