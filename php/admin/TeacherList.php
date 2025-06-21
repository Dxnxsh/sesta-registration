<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Teacher list access without admin session');
    header("Location: ../login-logout/login.php");
    exit();
}

?>
<?php include "../header/adminHeader.php" ?>
<?php
include("../config.php");

// Handle teacher deletion with proper sanitization
if (isset($_GET['id'])) {
    $id = SecuritySanitizer::sanitize($_GET['id'], 'id', 'TEACHER_ID');
    if (!empty($id)) {
        $deleteQuery = "DELETE FROM `teacher` WHERE `TEACHER_ID` = ?";
        $stmt = mysqli_prepare($con, $deleteQuery);
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $id);
            $deleteResult = mysqli_stmt_execute($stmt);
            if ($deleteResult) {
                SecuritySanitizer::logSecurityEvent('teacher_deletion', 'Teacher deleted: ' . $id);
            } else {
                SecuritySanitizer::logSecurityEvent('sql_error', 'Teacher deletion failed: ' . mysqli_error($con));
            }
            mysqli_stmt_close($stmt);
        } else {
            SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare delete statement: ' . mysqli_error($con));
        }
    } else {
        SecuritySanitizer::logSecurityEvent('invalid_input', 'Invalid teacher ID for deletion: ' . $_GET['id']);
    }
}

// Handle teacher search with proper sanitization
$searchType = 'TEACHER_ID'; // Default search type
$searchValue = '';

if (isset($_GET['searchType'])) {
    $searchType = SecuritySanitizer::sanitize($_GET['searchType'], 'name');
    if (!in_array($searchType, ['TEACHER_ID', 'TEACHER_NAME'])) {
        SecuritySanitizer::logSecurityEvent('invalid_input', 'Invalid search type: ' . $_GET['searchType']);
        $searchType = 'TEACHER_ID';
    }
}

if (isset($_GET['searchBox'])) {
    $searchValue = SecuritySanitizer::sanitize($_GET['searchBox'], 'name');
}

// Build the query with prepared statements
$baseQuery = "SELECT t.*, c.CLASS_CODE 
              FROM teacher t 
              LEFT JOIN class c ON t.TEACHER_ID = c.TEACHER_ID 
              WHERE 1";
$params = [];
$types = "";

if (!empty($searchValue)) {
    if ($searchType === 'TEACHER_ID') {
        $baseQuery .= " AND t.TEACHER_ID LIKE ?";
        $params[] = $searchValue . "%";
        $types .= "s";
    } elseif ($searchType === 'TEACHER_NAME') {
        $baseQuery .= " AND t.TEACHER_NAME LIKE ?";
        $params[] = "%" . $searchValue . "%";
        $types .= "s";
    }
}

$stmt = mysqli_prepare($con, $baseQuery);
if ($stmt) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
} else {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare teacher list query: ' . mysqli_error($con));
    die('Database error occurred');
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Teacher List</title>
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

        /* Styling for teacher assignment status */
        .teacher-assigned {
            background-color: #f8f9fa !important;
            border-left: 4px solid #28a745;
        }

        .teacher-unassigned {
            background-color: #fff !important;
            border-left: 4px solid #ffc107;
        }

        .assignment-status {
            font-weight: bold;
        }

        .assignment-status.assigned {
            color: #28a745;
        }

        .assignment-status.unassigned {
            color: #ffc107;
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
</head>

<body>
    <div class="container">
        <?php
        // Calculate assignment statistics
        $totalTeachers = mysqli_num_rows($query);
        $assignedCount = 0;
        mysqli_data_seek($query, 0); // Reset pointer to beginning
        while ($result = mysqli_fetch_assoc($query)) {
            if (!empty($result["CLASS_CODE"])) {
                $assignedCount++;
            }
        }
        $unassignedCount = $totalTeachers - $assignedCount;
        mysqli_data_seek($query, 0); // Reset pointer again for main display
        ?>
        
        <form id="form2" name="form2" method="get">
            <h1>Teacher List</h1>
            <div class="search-container">
                <div class="selectSearch"><select name="searchType" id="searchType">
                        <option value="TEACHER_ID">Teacher ID</option>
                        <option value="TEACHER_NAME">Teacher Name</option>
                    </select></div>
                <input name="searchBox" type="text" id="searchBox" placeholder="Search...">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="TeacherList.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <p>                <input class="button" type="submit" name="submit2" id="submit2" formaction="assignTeacher.php"
                    value="Assign teachers"><input class="button" type="submit" name="submit3" id="submit3" formaction="AddNewTeacher.php"
                    value="Add teachers">
            </p>
            <table width="163%">
                <tr>
                    <th>NAME</th>
                    <th>ID</th>
                    <th>CLASS</th>
                    <th>PHONE NUMBER</th>
                    <th>EMAIL</th>
                    <th colspan="6">MANAGE</th>
                </tr>
                <?php
                $num = mysqli_num_rows($query);
                if ($num > 0) {
                    while ($result = mysqli_fetch_assoc($query)) {
                        $classCode = $result["CLASS_CODE"];
                        $isAssigned = !empty($classCode);
                        $rowClass = $isAssigned ? 'teacher-assigned' : 'teacher-unassigned';
                        $statusClass = $isAssigned ? 'assigned' : 'unassigned';
                        $statusText = $isAssigned ? $classCode : 'Not Assigned';
                        
                        echo "
            <tr class='$rowClass'>
                <td>" . $result["TEACHER_NAME"] . "</td>
                <td>" . $result["TEACHER_ID"] . "</td>              
                <td><span class='assignment-status $statusClass'>$statusText</span></td>
                <td>" . $result["TEACHER_PHONENUM"] . "</td>
                <td>" . $result["TEACHER_EMAIL"] . "</td>

                <td class='manage-buttons' style='text-align: justify'><a class='view-button' href='adminViewteacher.php?id=" . $result["TEACHER_ID"] . "'>VIEW</a></td>
                <td class='manage-buttons'><a class='update-button' href='adminUpdateTeacher.php?id=" . $result["TEACHER_ID"] . "'>UPDATE</a></td>
                <td class='manage-buttons'><a class='delete-button' onclick='confirmDelete(\"" . $result["TEACHER_ID"] . "\")'>DELETE</a></td>

            </tr>
        ";
                    }
                } else {
                    echo "No records found.";
                }

                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Go Back</a></div>

    </div>

    <script>
        function confirmDelete(teacherID) {
            Swal.fire({
                title: 'Are you sure',
                text: 'You won\'t be able to revert this!',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        icon: 'success',
                        title: 'Deleted!',
                        text: 'Teacher has been successfully deleted.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // then trigger the actual delete via redirect-with-id
                        window.location.href = 'TeacherList.php?id=' + teacherID;
                    });
                }
            });
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>