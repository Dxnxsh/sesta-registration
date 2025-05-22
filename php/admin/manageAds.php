<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

$adsFolder = "../../image/ads/";

// Handle image deletion
if (isset($_POST['delete'])) {
    $fileToDelete = $adsFolder . basename($_POST['delete']);
    if (file_exists($fileToDelete)) {
        unlink($fileToDelete);
        echo "<script>alert('Image deleted successfully!');</script>";
    } else {
        echo "<script>alert('File not found!');</script>";
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
                echo "<script>alert('Image uploaded successfully!');</script>";
            } else {
                echo "<script>alert('Error uploading file!');</script>";
            }
        } else {
            echo "<script>alert('Invalid file type! Only JPG, JPEG, PNG, and WEBP are allowed.');</script>";
        }
    } else {
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
        <div class='manage-buttons'><a class='back-button' href='admin_home.php'>Go Back</a></div>
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