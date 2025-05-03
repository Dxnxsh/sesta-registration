<?php 
   session_start();

   include("../../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
   }
?>
<?php

include("../../config.php"); 
            
            $id = $_SESSION['valid'];
            $query = mysqli_query($con,"SELECT*FROM student WHERE STUDENT_ID=$id");
            $query2 = mysqli_query($con,"SELECT*FROM payment WHERE STUDENT_ID=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Name = $result['STUDENT_NAME'];
                $res_IC = $result['STUDENT_ID'];
                $res_Add = $result['STUDENT_ADDRESS'];
            }

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Check if a file was uploaded without errors
    if (isset($_FILES["file"]) && $_FILES["file"]["error"] == 0) {
        $target_dir = "../../../uploads/"; // Change this to the desired directory for uploaded files
        $target_file = $target_dir . basename($_FILES["file"]["name"]);
        $file_type = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Check if the file is allowed (you can modify this to allow specific file types)
        $allowed_types = array("jpg", "jpeg", "png", "gif", "pdf");
        if (!in_array($file_type, $allowed_types)) {
            echo "Sorry, only JPG, JPEG, PNG, GIF, and PDF files are allowed.";
        } else {
            // Move the uploaded file to the specified directory
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $target_file)) {
                // File upload success, now store information in the database
                $filename = $_FILES["file"]["name"];

                // Insert the file information into the database
                $sql = "UPDATE `payment` SET `PAYMENT_RECEIPT`='$filename',`PAYMENT_STATUS`='PENDING' WHERE `STUDENT_ID` = '$res_IC' AND `PAYMENT_TYPE` = 'SCHOOL FEES'";

                if ($con->query($sql) === TRUE) {
                    header("Location: test.php");
                } else {
                    echo "Sorry, there was an error uploading your file and storing information in the database: " . $con->error;
                }

                $con->close();
            } else {
                echo "Sorry, there was an error uploading your file.";
            }
        }
    } else {
        echo "No file was uploaded.";
    }
}
?>

