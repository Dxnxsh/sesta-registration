<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize and validate input data
try {
    $id = SecuritySanitizer::sanitizeForDB($_POST['adminId'] ?? '', 'id', 'ADMIN_ID');
    $uname = SecuritySanitizer::sanitizeForDB($_POST['uname'] ?? '', 'username', 'ADMIN_USERNAME');
    $fullname = SecuritySanitizer::sanitizeForDB($_POST['fname'] ?? '', 'name', 'ADMIN_NAME');
    $phoneN = SecuritySanitizer::sanitizeForDB($_POST['phone'] ?? '', 'phone', 'ADMIN_PHONE');
    $pswd = SecuritySanitizer::sanitizeForDB($_POST['pwd'] ?? '', 'password', 'ADMIN_PWD');

    // Additional validation
    if (empty($id) || empty($uname) || empty($fullname) || empty($phoneN) || empty($pswd)) {
        echo json_encode(['success' => false, 'message' => 'All fields are required']);
        exit();
    }

    // Check for malicious input
    $inputs = [$id, $uname, $fullname, $phoneN, $pswd];
    foreach ($inputs as $input) {
        $maliciousType = detectMaliciousInput($input);
        if ($maliciousType) {
            SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                'type' => $maliciousType,
                'field' => 'admin_registration',
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

// Check if admin ID already exists using prepared statement
$checkQuery = "SELECT ADMIN_ID FROM admin WHERE ADMIN_ID = ?";
$stmt = mysqli_prepare($con, $checkQuery);

if (!$stmt) {
    echo json_encode(['success' => false, 'message' => 'Database preparation error']);
    exit();
}

mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$checkResult = mysqli_stmt_get_result($stmt);

if (mysqli_num_rows($checkResult) > 0) {
    mysqli_stmt_close($stmt);
    echo json_encode(['success' => false, 'message' => 'Admin ID already exists']);
} else {
    mysqli_stmt_close($stmt);
    
    // Perform the insert query using prepared statement
    $insertQuery = "INSERT INTO admin (ADMIN_ID, ADMIN_NAME, ADMIN_USERNAME, ADMIN_PHONE, ADMIN_PWD) 
                    VALUES (?, ?, ?, ?, ?)";
    
    $stmt = mysqli_prepare($con, $insertQuery);
    
    if (!$stmt) {
        echo json_encode(['success' => false, 'message' => 'Database preparation error']);
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "sssss", $id, $fullname, $uname, $phoneN, $pswd);
    
    if (mysqli_stmt_execute($stmt)) {
        SecuritySanitizer::logSecurityEvent('admin_created', [
            'new_admin_id' => $id,
            'created_by' => $_SESSION['adminID']
        ]);
        echo json_encode(['success' => true]);
    } else {
        SecuritySanitizer::logSecurityEvent('admin_creation_failed', [
            'error' => mysqli_error($con),
            'attempted_by' => $_SESSION['adminID']
        ]);
        echo json_encode(['success' => false, 'message' => 'Database error occurred']);
    }
    
    mysqli_stmt_close($stmt);
}
?>
