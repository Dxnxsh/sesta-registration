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
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in assignTeacher.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}

?>
<?php include "../header/adminHeader.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Class Information</title>
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

        .selectSearch {
            margin-right: 10px;
        }

        #searchType {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: aliceblue;
            cursor: pointer;
        }

        /* Style the dropdown arrow */
        #searchType::after {
            content: '\25BC';
            /* Unicode character for down arrow */
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
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
    </style>


<?php
include("../config.php");

// Use prepared statement to get classes without teachers
$stmt = $con->prepare("SELECT * FROM class WHERE TEACHER_ID IS NULL");
$stmt->execute();
$query = $stmt->get_result();

// Check if the query was successful
if (!$query) {
    SecuritySanitizer::logSecurityEvent("Database query failed in assignTeacher.php", 'HIGH');
    die('Error executing the query: ' . mysqli_error($con));
}

// Check if the query result is empty
if ($query->num_rows == 0) {
    echo "<script>
        // Use setTimeout to wait for 2 seconds before redirecting
        setTimeout(function() {
            Swal.fire({
                title: 'No Class available!',
                text: 'There are no class for teachers to assign.',
                icon: 'info'
            }).then(function() {
                window.location.href = 'TeacherList.php';
            });
        }, 500); // 0.5 milliseconds = 0.5 seconds
    </script>";

    // Prevent further execution of the script
    exit;
}


if (isset($_POST['submit2'])) {
    // Check if the teacher_id array is set
    if (isset($_POST['teacher_id']) && is_array($_POST['teacher_id'])) {
        foreach ($_POST['teacher_id'] as $classID => $selectedTeacherID) {
            // Sanitize inputs
            $classID = SecuritySanitizer::sanitize($classID, 'id', 'CLASS_CODE');
            $selectedTeacherID = SecuritySanitizer::sanitize($selectedTeacherID, 'id', 'TEACHER_ID');
            
            if (!$classID || !$selectedTeacherID) {
                SecuritySanitizer::logSecurityEvent("Invalid teacher assignment inputs: classID=$classID, teacherID=$selectedTeacherID", 'MEDIUM');
                continue;
            }
            
            // Check if the specific submit button is set
            if (isset($_POST['submit2'][$classID])) {
                
                // Check if teacher is already assigned to another class using prepared statement
                if (!empty($selectedTeacherID)) {
                    $checkStmt = $con->prepare("SELECT CLASS_CODE FROM class WHERE TEACHER_ID = ?");
                    $checkStmt->bind_param("s", $selectedTeacherID);
                    $checkStmt->execute();
                    $checkResult = $checkStmt->get_result();
                    
                    if ($checkResult->num_rows > 0) {
                        SecuritySanitizer::logSecurityEvent("Attempt to assign teacher $selectedTeacherID to multiple classes by admin $adminId", 'MEDIUM');
                        $checkStmt->close();
                        echo "<script>
                            Swal.fire({
                                title: 'Error!',
                                text: 'Teacher is already assigned to another class. Each teacher can only be assigned to one class.',
                                icon: 'error'
                            }).then(function() {
                                window.location.href = 'assignTeacher.php';
                            });
                        </script>";
                        exit;
                    }
                    $checkStmt->close();
                }
                
                // Update the teacher_id for each class using prepared statements
                $updateStmt = $con->prepare("UPDATE class SET TEACHER_ID = ? WHERE CLASS_CODE = ?");
                $updateStmt->bind_param("ss", $selectedTeacherID, $classID);
                $updateResult1 = $updateStmt->execute();
                $updateStmt->close();
                
                $updateStmt2 = $con->prepare("UPDATE class SET ADMIN_ID = ? WHERE CLASS_CODE = ?");
                $updateStmt2->bind_param("ss", $adminId, $classID);
                $updateResult2 = $updateStmt2->execute();
                $updateStmt2->close();
                
                if ($updateResult1 && $updateResult2) {
                    SecuritySanitizer::logSecurityEvent("Teacher $selectedTeacherID assigned to class $classID by admin $adminId", 'INFO');
                    echo "<script>
                        Swal.fire({
                            title: 'Assigned!',
                            text: 'Teacher assigned successfully.',
                            icon: 'success'
                        }).then(function() {
                            window.location.href = 'TeacherList.php';
                        });
                    </script>";
                } else {
                    // Handle the error scenario
                    SecuritySanitizer::logSecurityEvent("Failed to assign teacher $selectedTeacherID to class $classID by admin $adminId", 'HIGH');
                    echo "<script>
                            Swal.fire({
                                title: 'Error!',
                                text: 'Failed to assign teacher.',
                                icon: 'error'
                            }).then(function() {
                                window.location.href = 'assignTeacher.php';
                            });
                        </script>";
                }
            }
        }
        // Prevent form resubmission
        exit;
    }
}

?>
</head>

<body>
    <div class="container">
            <h1>Assign Teacher</h1>

        <form id="form1" name="form1" method="post">
            <table width="163%">
                <tr>
                    <th>NAME</th>
                    <th>ID</th>
                    <th>LEVEL</th>
                    <th colspan="6">MANAGE</th>
                </tr>
                <?php
                $num = $query->num_rows;
                if ($num > 0) {
                    while ($result = $query->fetch_assoc()) {
                        // Sanitize all outputs for XSS protection
                        $className = SecuritySanitizer::sanitize($result["CLASS_NAME"], 'class_name');
                        $classCode = SecuritySanitizer::sanitize($result["CLASS_CODE"], 'id');
                        $classLevel = SecuritySanitizer::sanitize($result["CLASS_LEVEL"], 'class_level');
                        $classCat = SecuritySanitizer::sanitize($result["CLASS_CAT"], 'class_category');
                        
                        echo "
                        <tr>
                            <td>" . htmlspecialchars($className, ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($classCode, ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($classLevel, ENT_QUOTES, 'UTF-8') . " </td>
                            <td>" . htmlspecialchars($classCat, ENT_QUOTES, 'UTF-8') . " </td>
                            <td>
                                <select name='teacher_id[" . htmlspecialchars($classCode, ENT_QUOTES, 'UTF-8') . "]'>
                                    <option value='' selected disabled>Select Teacher</option>";

                        // Fetch and display teacher codes (only unassigned teachers) using prepared statement
                        $teacherStmt = $con->prepare("SELECT * FROM teacher WHERE TEACHER_ID NOT IN (SELECT TEACHER_ID FROM class WHERE TEACHER_ID IS NOT NULL)");
                        $teacherStmt->execute();
                        $teacherQuery = $teacherStmt->get_result();
                        
                        while ($teacherResult = $teacherQuery->fetch_assoc()) {
                            $teacherId = SecuritySanitizer::sanitize($teacherResult["TEACHER_ID"], 'id');
                            $teacherName = SecuritySanitizer::sanitize($teacherResult["TEACHER_NAME"], 'name');
                            echo "<option value='" . htmlspecialchars($teacherId, ENT_QUOTES, 'UTF-8') . "'>" . 
                                 htmlspecialchars($teacherId, ENT_QUOTES, 'UTF-8') . " - " . 
                                 htmlspecialchars($teacherName, ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                        $teacherStmt->close();
                        
                        echo "</select>
                            </td>
                            <td class='manage-buttons' style='text-align: justify'>
                                <input class='button' type='submit' name='submit2[" . htmlspecialchars($classCode, ENT_QUOTES, 'UTF-8') . "]' value='Assign Teachers'>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='TeacherList.php'>Go Back</a></div>

    </div>

</body>

</html>
<?php include "../header/footer.php" ?>