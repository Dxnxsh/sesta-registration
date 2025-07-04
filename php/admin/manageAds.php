<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

$adsFolder = "../../image/ads/";
// Ensure the ads folder exists
if (!is_dir($adsFolder)) {
    mkdir($adsFolder, 0755, true);
}

// Handle image deletion
if (isset($_POST['delete'])) {
    $fileName = basename($_POST['delete']);
    $fileToDelete = $adsFolder . $fileName;
    
    // Debug: Check if file exists and path is correct
    if (file_exists($fileToDelete)) {
        // Check if file is writable
        if (is_writable($fileToDelete)) {
            if (unlink($fileToDelete)) {
                echo "<script>
                    Swal.fire({
                        title: 'Success!',
                        text: 'Image deleted successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'manageAds.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Failed to delete the image! Permission denied.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'File is not writable. Check permissions.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'File not found: " . htmlspecialchars($fileToDelete, ENT_QUOTES, 'UTF-8') . "',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
    }
}

// Handle image upload
if (isset($_POST['upload'])) {
    if (isset($_FILES['newImage']) && $_FILES['newImage']['error'] == 0) {
        $targetFile = $adsFolder . basename($_FILES['newImage']['name']);
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate file type
        if (in_array($fileType, ['jpg', 'jpeg', 'png', 'webp'])) {
            if (move_uploaded_file($_FILES['newImage']['tmp_name'], $targetFile)) {
                echo "<script>
                    Swal.fire({
                        title: 'Success!',
                        text: 'Image uploaded successfully!',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then(function() {
                        window.location.href = 'manageAds.php';
                    });
                </script>";
            } else {
                echo "<script>
                    Swal.fire({
                        title: 'Error!',
                        text: 'Error uploading file!',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
        } else {
            echo "<script>
                Swal.fire({
                    title: 'Error!',
                    text: 'Invalid file type! Only JPG, JPEG, PNG, and WEBP are allowed.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            </script>";
        }
    } else {
        echo "<script>
            Swal.fire({
                title: 'Error!',
                text: 'No file selected or an error occurred!',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        </script>";
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
    <title>Advertisement Management</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for manageAds.php */
        .manage-buttons a.back-button {
            background-color: #007BFF;
            width: fit-content;
            margin-top: 30px;
        }

        /* Custom hover animation for this page */
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

        .manage-buttons a:hover {
            animation: buttonHover 0.3s ease;
            opacity: 0.9;
        }

        /* Page-specific heading style */
        h1 {
            font-size: 40px;
            color: black;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Unique advertisement upload styles */
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

        .select-image {
            display: inline-block;
            background-color: #04AA6D;
            color: white;
            padding: 10px 20px;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .select-image:hover {
            background-color: #028a57;
        }

        .select-image input[type="file"] {
            position: absolute;
            left: -9999px;
            opacity: 0;
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
        
        td img {
            display: block;
            margin-left: auto;
            margin-right: auto;
        }

        td {
            text-align: center;
            vertical-align: middle;
        }

        .preview-container {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        #imagePreview {
            display: none;
            max-width: 200px;
            max-height: 200px;
        }
    </style>
</head>

<body>
    <div class="container">
        <h1>Advertisement Management</h1>
        <form id="form1" name="form1" method="post" enctype="multipart/form-data">
            <div class="upload-section">
                <h2>Upload New Advertisement</h2>
                <div class="preview-container">
                    <img id="imagePreview" src="" alt="Image Preview">
                </div>
                <div class="upload-container">
                    <label for="newImage" class="select-image">
                        <span>Select Image</span>
                        <input type="file" name="newImage" id="newImage" accept="image/*" required onchange="enableUploadButton()">
                    </label>
                    <button type="submit" name="upload" id="uploadButton" class="upload-button" disabled>Upload</button>
                </div>
            </div>
            <table>
                <tr>
                    <th>FILE NAME</th>
                    <th>IMAGE</th>
                    <th>ACTION</th>
                </tr>
                <?php
                $files = array_diff(scandir($adsFolder), ['.', '..']);
                foreach ($files as $file) {
                    $filePath = $adsFolder . $file;
                    // Check if it's actually an image file
                    $fileExtension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
                    if (in_array($fileExtension, ['jpg', 'jpeg', 'png', 'webp'])) {
                        echo "<tr>
                                <td>$file</td>
                                <td><img src='$filePath' alt='$file' style='width: 100px; height: auto;'></td>
                                <td>
                                    <button type='button' onclick='confirmDelete(\"$file\")' style='background-color: #DC3545; color: white; border: none; padding: 5px 10px; border-radius: 5px; cursor: pointer;'>DELETE</button>
                                </td>
                              </tr>";
                    }
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Back</a></div>
    </div>
    <script>
        function enableUploadButton() {
            const uploadButton = document.getElementById('uploadButton');
            const fileInput = document.getElementById('newImage');
            if (fileInput.files.length > 0) {
                uploadButton.disabled = false;
                previewImage(fileInput.files[0]);
            } else {
                uploadButton.disabled = true;
                clearPreview();
            }
        }

        function previewImage(file) {
            const preview = document.getElementById('imagePreview');
            if (file && file.type.startsWith('image/')) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.style.display = 'block';
                }
                reader.readAsDataURL(file);
            } else {
                clearPreview();
            }
        }

        function clearPreview() {
            const preview = document.getElementById('imagePreview');
            preview.src = '';
            preview.style.display = 'none';
        }

        function confirmDelete(fileName) {
            Swal.fire({
                title: 'Are you sure?',
                text: `Do you want to delete ${fileName}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#DC3545',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    // Create a hidden form to submit the delete request
                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.style.display = 'none';
                    
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'delete';
                    input.value = fileName;
                    
                    form.appendChild(input);
                    document.body.appendChild(form);
                    form.submit();
                }
            });
        }
    </script>
</body>

</html>
<?php include "../header/footer.php" ?>