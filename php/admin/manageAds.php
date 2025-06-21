<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize admin session ID
$adminId = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');
if (!$adminId) {
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in manageAds.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}

$adsFolder = "../../image/ads/";

// Handle image deletion with proper validation
if (isset($_POST['delete'])) {
    $deleteRequest = SecuritySanitizer::sanitize($_POST['delete'], 'file_path');
    
    if (!$deleteRequest) {
        SecuritySanitizer::logSecurityEvent("Invalid file deletion request by admin $adminId", 'MEDIUM');
        echo "<script>alert('Invalid file name!');</script>";
    } else {
        // Only allow deletion of files in the ads folder and prevent directory traversal
        $fileToDelete = $adsFolder . basename($deleteRequest);
        
        // Additional security: ensure the file is within the ads directory
        $realAdsPath = realpath($adsFolder);
        $realFilePath = realpath($fileToDelete);
        
        if ($realFilePath && strpos($realFilePath, $realAdsPath) === 0 && file_exists($fileToDelete)) {
            // Validate file extension before deletion
            $fileExtension = strtolower(pathinfo($fileToDelete, PATHINFO_EXTENSION));
            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            
            if (in_array($fileExtension, $allowedExtensions)) {
                if (unlink($fileToDelete)) {
                    SecuritySanitizer::logSecurityEvent("Ad image deleted: $deleteRequest by admin $adminId", 'INFO');
                    echo "<script>alert('Image deleted successfully!');</script>";
                } else {
                    SecuritySanitizer::logSecurityEvent("Failed to delete ad image: $deleteRequest by admin $adminId", 'HIGH');
                    echo "<script>alert('Error deleting file!');</script>";
                }
            } else {
                SecuritySanitizer::logSecurityEvent("Attempt to delete invalid file type: $deleteRequest by admin $adminId", 'MEDIUM');
                echo "<script>alert('Invalid file type!');</script>";
            }
        } else {
            SecuritySanitizer::logSecurityEvent("Attempt to delete file outside ads directory: $deleteRequest by admin $adminId", 'HIGH');
            echo "<script>alert('File not found or access denied!');</script>";
        }
    }
}

// Handle image upload with comprehensive security
if (isset($_POST['upload'])) {
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == 0) {
        $originalFilename = $_FILES['newImage']['name'];
        $tmpName = $_FILES['newImage']['tmp_name'];
        $fileSize = $_FILES['newImage']['size'];
        
        // Sanitize filename
        $filename = SecuritySanitizer::sanitize($originalFilename, 'file_path');
        if (!$filename) {
            SecuritySanitizer::logSecurityEvent("Invalid filename attempted for ad upload: $originalFilename by admin $adminId", 'MEDIUM');
            echo "<script>alert('Invalid filename!');</script>";
        } else {
            // Check file size (max 2MB for ad images)
            if ($fileSize > 2 * 1024 * 1024) {
                SecuritySanitizer::logSecurityEvent("Ad image upload too large: $fileSize bytes by admin $adminId", 'LOW');
                echo "<script>alert('File size must be less than 2MB!');</script>";
            } else {
                $fileExtension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
                
                // Validate file type
                if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
                    // Validate MIME type for additional security
                    $finfo = finfo_open(FILEINFO_MIME_TYPE);
                    $mimeType = finfo_file($finfo, $tmpName);
                    finfo_close($finfo);
                    
                    $allowedMimes = [
                        'image/jpeg', 'image/jpg', 'image/png', 'image/webp'
                    ];
                    
                    if (in_array($mimeType, $allowedMimes)) {
                        // Generate unique filename to prevent conflicts
                        $uniqueFilename = uniqid('ad_', true) . '.' . $fileExtension;
                        $targetFile = $adsFolder . $uniqueFilename;
                        
                        // Ensure upload directory exists
                        if (!is_dir($adsFolder)) {
                            mkdir($adsFolder, 0755, true);
                        }
                        
                        if (move_uploaded_file($tmpName, $targetFile)) {
                            SecuritySanitizer::logSecurityEvent("Ad image uploaded successfully: $uniqueFilename by admin $adminId", 'INFO');
                            echo "<script>alert('Image uploaded successfully!');</script>";
                        } else {
                            SecuritySanitizer::logSecurityEvent("Failed to upload ad image by admin $adminId", 'HIGH');
                            echo "<script>alert('Error uploading file!');</script>";
                        }
                    } else {
                        SecuritySanitizer::logSecurityEvent("Invalid MIME type for ad upload: $mimeType by admin $adminId", 'MEDIUM');
                        echo "<script>alert('Invalid file type detected!');</script>";
                    }
                } else {
                    SecuritySanitizer::logSecurityEvent("Invalid file extension for ad upload: $fileExtension by admin $adminId", 'MEDIUM');
                    echo "<script>alert('Invalid file type! Only JPG, JPEG, PNG, and WEBP are allowed.');</script>";
                }
            }
        }
    } else {
        SecuritySanitizer::logSecurityEvent("No file selected for ad upload by admin $adminId", 'LOW');
        echo "<script>alert('No file selected or an error occurred!');</script>";
    }
}
?>
<?php include "../header/adminHeader.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Manage Advertisement</title>
    <style>
        body {
            background-image: url("../../image/admin.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        .button {
            background-color: #04AA6D;
            border: none;
            color: white;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 15px;
            margin: 10px 2px;
            cursor: pointer;
            border-radius: 5px;
        }

        #form2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 5px;
            padding: 10px;
        }

        #searchBox {
            margin-left: auto;
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            text-align: center;
        }

        th,
        td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: center;
            align-content: center;
        }

        th {
            background-color: #04AA6D;
            color: white;
            text-align: center;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        button {
            padding: 8px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            font-size: 14px;
        }

        .manage-buttons a {
            display: inline-block;
            background-color: #04AA6D;
            color: white;
            padding: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            margin: 4px;
            transition: background-color 0.3s;
            width: 60%;
        }

        .manage-buttons a.view-button {
            background-color: #007BFF;
            width: fit-content;
        }

        .manage-buttons a.update-button {
            background-color: #28A745;
            width: fit-content;
        }

        .manage-buttons a.delete-button {
            background-color: #DC3545;
            width: fit-content;
        }

        .manage-buttons a.back-button {
            background-color: #007BFF;
            width: fit-content;
            margin-top: 30px;
        }

        @keyframes buttonHover {
            0% {
                transform: translateY(0);
            }

            50% {
                transform: translateY(-5px);
            }

            100% {
                transform: translateY(0);
            }
        }

        a.reset-button {
            background-color: #0072ffc2;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }

        a.reset-button:hover {
            background-color: #DC3545;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }

        .manage-buttons a:hover {
            animation: buttonHover 0.3s ease;
            opacity: 0.9;
        }

        h1 {
            font-size: 40px;
            color: black;
            margin-bottom: 10px;
            text-align: center;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container img {
            margin-right: 10px;
            cursor: pointer;
        }

        #searchBox {
            width: fit-content;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: aliceblue;
        }

        #submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }

        #submit:hover {
            background-color: #45a049;
        }

        .upload-section {
            margin-top: 30px;
            text-align: center;
            background-color: #f9f9f9;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .upload-section h2 {
            font-size: 24px;
            color: #333;
            margin-bottom: 15px;
        }

        .upload-container {
            display: flex;
            flex-direction: row;
            align-items: center;
            gap: 15px;
            justify-content: center;
        }

        .upload-label {
            display: inline-block;
            background-color: #04AA6D;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .upload-label:hover {
            background-color: #028a57;
        }

        .upload-label input[type="file"] {
            display: none;
        }

        .upload-button {
            background-color: #007BFF;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 5px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .upload-button:hover {
            background-color: #0056b3;
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Manage Advertisement</h1>
        </form>
        <form id="form1" name="form1" method="post" enctype="multipart/form-data">
            <div class="upload-section">
                <h2>Upload New Advertisement</h2>
                <div class="upload-container">
                    <label for="newImage" class="upload-label">
                        <span>Select Image</span>
                        <input type="file" name="newImage" id="newImage" accept="image/*" required onchange="enableUploadButton()">
                    </label>
                    <button type="submit" name="upload" id="uploadButton" class="upload-button" disabled>Upload</button>
                </div>
            </div>
            <table>
                <tr>
                    <th>File Name</th>
                    <th>Image</th>
                    <th>Actions</th>
                </tr>
                <?php
                $files = array_diff(scandir($adsFolder), ['.', '..']);
                foreach ($files as $file) {
                    $filePath = $adsFolder . $file;
                    echo "<tr>
                            <td>$file</td>
                            <td><img src='$filePath' alt='$file' style='width: 100px; height: auto;'></td>
                            <td>
                                <button type='submit' name='delete' value='$file' style='background-color: #DC3545; color: white; border: none; padding: 5px 10px; border-radius: 5px;'>Delete</button>
                            </td>
                          </tr>";
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Go Back</a></div>
    </div>
    <script>
        function enableUploadButton() {
            const uploadButton = document.getElementById('uploadButton');
            const fileInput = document.getElementById('newImage');
            if (fileInput.files.length > 0) {
                uploadButton.disabled = false;
            } else {
                uploadButton.disabled = true;
            }
        }
    </script>
</body>

</html>
<?php include "../header/footer.php" ?>