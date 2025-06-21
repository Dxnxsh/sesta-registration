<?php 
session_start();

include("../../config.php");
if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
    exit();
}

$id = $_SESSION['valid'];

// Use prepared statements to get student information
$query = "SELECT STUDENT_NAME, STUDENT_ID, STUDENT_ADDRESS FROM student WHERE STUDENT_ID = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$student = mysqli_fetch_assoc($result);
mysqli_stmt_close($stmt);

$res_Name = $student['STUDENT_NAME'] ?? '';
$res_IC = $student['STUDENT_ID'] ?? '';
$res_Add = $student['STUDENT_ADDRESS'] ?? '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded without errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        
        // Validate file size (limit to 5MB)
        $max_file_size = 5 * 1024 * 1024; // 5MB
        if ($_FILES["file"]["size"] > $max_file_size) {
            echo "Sorry, file is too large. Maximum size is 5MB.";
            exit();
        }
        
        // Sanitize filename
        $original_filename = $_FILES["file"]["name"];
        $sanitized_filename = SecuritySanitizer::sanitize($original_filename, 'file_path');
        
        // Generate unique filename to prevent conflicts
        $file_extension = strtolower(pathinfo($sanitized_filename, PATHINFO_EXTENSION));
        $unique_filename = $res_IC . '_' . time() . '_' . uniqid() . '.' . $file_extension;
        
        $target_dir = "../../../uploads/";
        $target_file = $target_dir . $unique_filename;

        // Check if the file type is allowed
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");
        if (!in_array($file_extension, $allowed_types)) {
            SecuritySanitizer::logSecurityEvent('invalid_file_upload_attempt', [
                'student_id' => $res_IC,
                'filename' => $original_filename,
                'file_type' => $file_extension
            ]);
            echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
            exit();
        }
        
        // Validate file content (basic MIME type check)
        $finfo = finfo_open(FILEINFO_MIME_TYPE);
        $mime_type = finfo_file($finfo, $_FILES["file"]["tmp_name"]);
        finfo_close($finfo);
        
        $allowed_mimes = [
            'image/jpeg', 'image/jpg', 'image/png', 'image/gif', 
            'application/pdf'
        ];
        
        if (!in_array($mime_type, $allowed_mimes)) {
            SecuritySanitizer::logSecurityEvent('invalid_mime_type_upload', [
                'student_id' => $res_IC,
                'filename' => $original_filename,
                'mime_type' => $mime_type
            ]);
            echo "Sorry, file type not allowed based on content.";
            exit();
        }

        // Create uploads directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0755, true);
        }

        // Move the uploaded file to the specified directory
        if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
            
            // Update database using prepared statement
            $sql = "UPDATE payment SET PAYMENT_RECEIPT = ?, PAYMENT_STATUS = 'PENDING' WHERE STUDENT_ID = ? AND PAYMENT_TYPE = 'SCHOOL FEES'";
            $stmt = mysqli_prepare($con, $sql);
            
            if ($stmt) {
                mysqli_stmt_bind_param($stmt, "ss", $unique_filename, $res_IC);
                
                if (mysqli_stmt_execute($stmt)) {
                    SecuritySanitizer::logSecurityEvent('payment_receipt_uploaded', [
                        'student_id' => $res_IC,
                        'filename' => $unique_filename,
                        'payment_type' => 'SCHOOL FEES'
                    ]);
                    header("Location: test.php");
                    exit();
                } else {
                    SecuritySanitizer::logSecurityEvent('payment_update_failed', [
                        'student_id' => $res_IC,
                        'error' => mysqli_error($con)
                    ]);
                    echo "Sorry, there was an error updating the database: " . mysqli_error($con);
                }
                mysqli_stmt_close($stmt);
            } else {
                echo "Database preparation error.";
            }
        } else {
            SecuritySanitizer::logSecurityEvent('file_upload_failed', [
                'student_id' => $res_IC,
                'filename' => $unique_filename
            ]);
            echo "Sorry, there was an error uploading your file.";
        }
    } else {
        echo "No file was uploaded or file has errors.";
    }
}
?>
