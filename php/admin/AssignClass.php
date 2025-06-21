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
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in AssignClass.php', 'HIGH');
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

// Use prepared statement to get students without class assignment
$stmt = $con->prepare("SELECT * FROM student WHERE CLASS_CODE IS NULL AND PARENT_ID IS NOT NULL");
$stmt->execute();
$query = $stmt->get_result();

$num = $query->num_rows;

if ($num === 0) {
    echo "<script>
        Swal.fire({
            title: 'No Students Available',
            text: 'There are no students available for assignment.',
            icon: 'info'
        }).then(function() {
            window.location.href = 'StudentList.php';
        });
    </script>";
    exit; // Prevent further execution of the code
}

if (isset($_POST['submit2'])) {
    // Check if the class_code array is set
    if (isset($_POST['class_code']) && is_array($_POST['class_code'])) {
        foreach ($_POST['class_code'] as $studentID => $selectedClassCode) {
            // Sanitize inputs
            $studentID = SecuritySanitizer::sanitize($studentID, 'id', 'STUDENT_ID');
            $selectedClassCode = SecuritySanitizer::sanitize($selectedClassCode, 'id', 'CLASS_CODE');
            
            if (!$studentID || !$selectedClassCode) {
                SecuritySanitizer::logSecurityEvent("Invalid class assignment inputs: studentID=$studentID, classCode=$selectedClassCode", 'MEDIUM');
                continue;
            }
            
            // Check if the specific submit button is set
            if (isset($_POST['submit2'][$studentID])) {
                // Update the class_code for each student using prepared statement
                $updateStmt = $con->prepare("UPDATE student SET CLASS_CODE = ? WHERE STUDENT_ID = ?");
                $updateStmt->bind_param("ss", $selectedClassCode, $studentID);
                
                if ($updateStmt->execute()) {
                    SecuritySanitizer::logSecurityEvent("Student $studentID assigned to class $selectedClassCode by admin $adminId", 'INFO');
                    $updateStmt->close();
                    echo "<script>
                        Swal.fire({
                            title: 'Assigned!',
                            text: 'Student assigned successfully.',
                            icon: 'success'
                        }).then(function() {
                            window.location.href = 'StudentList.php';
                        });
                    </script>";
                } else {
                    // Handle the error scenario
                    SecuritySanitizer::logSecurityEvent("Failed to assign student $studentID to class $selectedClassCode by admin $adminId", 'HIGH');
                    $updateStmt->close();
                    echo "<script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Failed to assign student.',
                            icon: 'error'
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
            <h1>Class Information</h1>

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
                        $studentName = SecuritySanitizer::sanitize($result["STUDENT_NAME"], 'name');
                        $studentId = SecuritySanitizer::sanitize($result["STUDENT_ID"], 'id');
                        $studentLevel = SecuritySanitizer::sanitize($result["STUDENT_LEVEL"], 'class_level');
                        
                        echo "
                        <tr>
                            <td>" . htmlspecialchars($studentName, ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') . "</td>
                            <td>" . htmlspecialchars($studentLevel, ENT_QUOTES, 'UTF-8') . " </td>
                            <td>
                                <select name='class_code[" . htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') . "]'>
                                    <option value='' selected disabled>Select Class</option>";

                        // Fetch and display class codes using prepared statement
                        $classStmt = $con->prepare("SELECT * FROM class");
                        $classStmt->execute();
                        $classQuery = $classStmt->get_result();
                        
                        while ($classResult = $classQuery->fetch_assoc()) {
                            $classCode = SecuritySanitizer::sanitize($classResult["CLASS_CODE"], 'id');
                            echo "<option value='" . htmlspecialchars($classCode, ENT_QUOTES, 'UTF-8') . "'>" . 
                                 htmlspecialchars($classCode, ENT_QUOTES, 'UTF-8') . "</option>";
                        }
                        $classStmt->close();
                        
                        echo "</select>
                            </td>
                            <td class='manage-buttons' style='text-align: justify'>
                                <input class='button' type='submit' name='submit2[" . htmlspecialchars($studentId, ENT_QUOTES, 'UTF-8') . "]' value='Assign Students'>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='StudentList.php'>Go Back</a></div>

    </div>

</body>

</html>
<?php include "../header/footer.php" ?>