<?php 
session_start();

include("../../config.php");

if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
    exit();
}

// Validate and sanitize session data
$id = SecuritySanitizer::sanitize($_SESSION['valid'], 'id', 'STUDENT_ID');
if (!$id) {
    SecuritySanitizer::logSecurityEvent('Invalid session ID in upload2.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}

// Use prepared statements for database queries
$stmt = $con->prepare("SELECT STUDENT_NAME, STUDENT_ID, STUDENT_ADDRESS FROM student WHERE STUDENT_ID = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $res_Name = $row['STUDENT_NAME'];
    $res_IC = $row['STUDENT_ID'];
    $res_Add = $row['STUDENT_ADDRESS'];
} else {
    SecuritySanitizer::logSecurityEvent("Student not found for ID: $id", 'MEDIUM');
    header("Location: ../login-logout/login.php");
    exit();
}
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded without errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $uploadDir = "../../../uploads/";
        $originalFilename = $_FILES["file"]["name"];
        $tmpName = $_FILES["file"]["tmp_name"];
        $fileSize = $_FILES["file"]["size"];
        
        // Sanitize and validate filename
        $filename = SecuritySanitizer::sanitize($originalFilename, 'file_path');
        if (!$filename) {
            SecuritySanitizer::logSecurityEvent("Invalid filename attempted: $originalFilename", 'MEDIUM');
            echo "Invalid filename.";
            exit();
        }
        
        // Check file size (max 5MB for dormitory fee receipts)
        if ($fileSize > 5 * 1024 * 1024) {
            SecuritySanitizer::logSecurityEvent("File too large attempted: $fileSize bytes", 'LOW');
            echo "File size must be less than 5MB.";
            exit();
        }
        
        // Validate file type
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif', 'pdf'];
        $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        
        if (!in_array($fileExtension, $allowedTypes)) {
            SecuritySanitizer::logSecurityEvent("Invalid file type attempted: $fileExtension", 'MEDIUM');
            echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
            exit();
        }
        
        // Validate MIME type for additional security
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mimeType = finfo_file($finfo, $tmpName);
        finfo_close($finfo);
        
        $allowedMimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'application/pdf'
        ];
        
        if (!in_array($mimeType, $allowedMimes)) {
            SecuritySanitizer::logSecurityEvent("Invalid MIME type attempted: $mimeType", 'MEDIUM');
            echo "Invalid file type detected.";
            exit();
        }
        
        // Generate unique filename to prevent conflicts and directory traversal
        $uniqueFilename = uniqid('dorm_receipt_', true) . '.' . $fileExtension;
        $targetFile = $uploadDir . $uniqueFilename;
        
        // Ensure upload directory exists and is writable
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Move uploaded file
        if (move_uploaded_file($tmpName, $targetFile)) {
            // Update database with prepared statement
            $stmt = $con->prepare("UPDATE payment SET PAYMENT_RECEIPT = ?, PAYMENT_STATUS = 'PENDING' WHERE STUDENT_ID = ? AND PAYMENT_TYPE = 'DORMITORY FEES'");
            $stmt->bind_param("ss", $uniqueFilename, $res_IC);
            
            if ($stmt->execute()) {
                SecuritySanitizer::logSecurityEvent("Dormitory fee receipt uploaded successfully for student: $res_IC", 'INFO');
                $stmt->close();
                header("Location: test2.php");
                exit();
            } else {
                SecuritySanitizer::logSecurityEvent("Database error during receipt upload for student: $res_IC", 'HIGH');
                // Remove uploaded file if database update fails
                unlink($targetFile);
                echo "Sorry, there was an error storing the file information in the database.";
            }
            $stmt->close();
        } else {
            SecuritySanitizer::logSecurityEvent("File upload failed for student: $res_IC", 'MEDIUM');
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        SecuritySanitizer::logSecurityEvent("No file uploaded or upload error for student: " . ($res_IC ?? 'unknown'), 'LOW');
        echo "No file was uploaded or there was an upload error.";
    }
}
?>

