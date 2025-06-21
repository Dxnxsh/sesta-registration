<?php
session_start();
include("../config.php");

if (!isset($_SESSION['validAD'])) {
    SecuritySanitizer::logSecurityEvent('admin_class_unauthorized_access', [
        'session_id' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    header("Location: ../login-logout/login.php");
    exit();
}

if (!isset($_SESSION['adminID'])) {
    SecuritySanitizer::logSecurityEvent('admin_class_missing_admin_id', [
        'session_id' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
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

        a.reset-button:hover  {
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
// Handle class deletion with proper sanitization
if (isset($_GET['id'])) {
    $classCode = SecuritySanitizer::sanitize($_GET['id'], 'id', 'CLASS_CODE');
    
    if (empty($classCode)) {
        SecuritySanitizer::logSecurityEvent('admin_class_invalid_deletion_id', [
            'invalid_id' => $_GET['id'] ?? '',
            'admin_id' => $_SESSION['adminID'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
    } else {
        // Check if there are associated teachers in the class using prepared statements
        $checkTeachersQuery = "SELECT COUNT(*) as teacherCount FROM teacher WHERE TEACHER_ID IN (SELECT TEACHER_ID FROM class WHERE CLASS_CODE = ?)";
        $stmt = mysqli_prepare($con, $checkTeachersQuery);
        
        if ($stmt) {
            mysqli_stmt_bind_param($stmt, "s", $classCode);
            mysqli_stmt_execute($stmt);
            $checkTeachersResult = mysqli_stmt_get_result($stmt);
            $teacherRow = mysqli_fetch_assoc($checkTeachersResult);
            $teacherCount = $teacherRow['teacherCount'];
            mysqli_stmt_close($stmt);

            if ($teacherCount > 0) {
                // Display a popup if there are associated teachers
                echo "<script>
                        Swal.fire({
                            title: 'Delete Denied',
                            text: 'Revoke assigned teacher before deleting the class.',
                            icon: 'error'
                        }).then(function() {
                            window.location.href = 'adminClass.php';
                        });
                    </script>";
                exit; // Prevent further execution of the code
            }

            // If no associated teachers, proceed with deletion using prepared statement
            $deleteQuery = "DELETE FROM `class` WHERE `CLASS_CODE` = ?";
            $deleteStmt = mysqli_prepare($con, $deleteQuery);
            
            if ($deleteStmt) {
                mysqli_stmt_bind_param($deleteStmt, "s", $classCode);
                $deleteResult = mysqli_stmt_execute($deleteStmt);
                
                if ($deleteResult) {
                    SecuritySanitizer::logSecurityEvent('admin_class_deleted', [
                        'class_code' => $classCode,
                        'admin_id' => $_SESSION['adminID'] ?? '',
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                } else {
                    SecuritySanitizer::logSecurityEvent('admin_class_deletion_failed', [
                        'class_code' => $classCode,
                        'error' => mysqli_error($con),
                        'admin_id' => $_SESSION['adminID'] ?? '',
                        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
                    ]);
                }
                
                mysqli_stmt_close($deleteStmt);
            }
        } else {
            SecuritySanitizer::logSecurityEvent('admin_class_teacher_check_failed', [
                'error' => mysqli_error($con),
                'admin_id' => $_SESSION['adminID'] ?? '',
                'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
            ]);
        }
    }
}

// Handle class search with proper sanitization
$searchType = 'CLASS_CODE'; // Default search type
$searchValue = '';

if (isset($_GET['searchType'])) {
    $searchType = SecuritySanitizer::sanitize($_GET['searchType'], 'name');
    if (!in_array($searchType, ['CLASS_CODE', 'CLASS_NAME', 'TEACHER_ID'])) {
        SecuritySanitizer::logSecurityEvent('admin_class_invalid_search_type', [
            'search_type' => $_GET['searchType'],
            'admin_id' => $_SESSION['adminID'] ?? '',
            'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
        ]);
        $searchType = 'CLASS_CODE';
    }
}

if (isset($_GET['searchBox'])) {
    $searchValue = SecuritySanitizer::sanitize($_GET['searchBox'], 'name');
}

// Build the query with prepared statements
$baseQuery = "SELECT * FROM class WHERE 1=1";
$params = [];
$types = "";

if (!empty($searchValue)) {
    if ($searchType === 'CLASS_CODE') {
        $baseQuery .= " AND CLASS_CODE LIKE ?";
        $params[] = $searchValue . "%";
        $types .= "s";
    } elseif ($searchType === 'CLASS_NAME') {
        $baseQuery .= " AND CLASS_NAME LIKE ?";
        $params[] = "%" . $searchValue . "%";
        $types .= "s";
    } elseif ($searchType === 'TEACHER_ID') {
        $baseQuery .= " AND TEACHER_ID LIKE ?";
        $params[] = $searchValue . "%";
        $types .= "s";
    }
}

// Execute the search query using prepared statements
$stmt = mysqli_prepare($con, $baseQuery);
if ($stmt) {
    if (!empty($params)) {
        mysqli_stmt_bind_param($stmt, $types, ...$params);
    }
    mysqli_stmt_execute($stmt);
    $query = mysqli_stmt_get_result($stmt);
    mysqli_stmt_close($stmt);
} else {
    SecuritySanitizer::logSecurityEvent('admin_class_query_failed', [
        'error' => mysqli_error($con),
        'admin_id' => $_SESSION['adminID'] ?? '',
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    // Fallback to basic query
    $query = mysqli_query($con, "SELECT * FROM class");
}
?>
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Class Information</h1>
            <div class="search-container">
                <div class="selectSearch"><select name="searchType" id="searchType">
                        <option value="CLASS_CODE"<?php echo ($searchType === 'CLASS_CODE') ? ' selected' : ''; ?>>Class Code</option>
                        <option value="CLASS_NAME"<?php echo ($searchType === 'CLASS_NAME') ? ' selected' : ''; ?>>Class Name</option>
                        <option value="TEACHER_ID"<?php echo ($searchType === 'TEACHER_ID') ? ' selected' : ''; ?>>Teacher ID</option>
                    </select></div>
                <input name="searchBox" type="text" id="searchBox" placeholder="Search..." value="<?php echo htmlspecialchars($searchValue); ?>">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="adminClass.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <p>
                <input class="button" type="submit" name="submit2" id="submit2" formaction="adminNewClass.php"
                    value="Add New Class">
            </p>
            <table width="163%">
                <tr>
                    <th>CODE</th>
                    <th>NAME</th>
                    <th>LEVEL</th>
                    <th>BLOCK</th>
                    <th>FLOOR</th>
                    <th>CATEGORY</th>
                    <th>TEACHER ID</th>
                    <th colspan="3">MANAGE</th>
                </tr>
                <?php
                $num = mysqli_num_rows($query);
                if ($num > 0) {
                    while ($result = mysqli_fetch_assoc($query)) {
                        // Sanitize all outputs for safe display
                        $classCode = SecuritySanitizer::sanitize($result["CLASS_CODE"], 'class_code');
                        $className = SecuritySanitizer::sanitize($result["CLASS_NAME"], 'class_name');
                        $classLevel = SecuritySanitizer::sanitize($result["CLASS_LEVEL"], 'class_level');
                        $classBlock = SecuritySanitizer::sanitize($result["CLASS_BLOCK"], 'class_block');
                        $classFloor = SecuritySanitizer::sanitize($result["CLASS_FLOOR"], 'floor');
                        $classCat = SecuritySanitizer::sanitize($result["CLASS_CAT"], 'class_category');
                        $teacherId = SecuritySanitizer::sanitize($result["TEACHER_ID"], 'id');
                        
                        echo "
                    <tr>
                        <td>" . htmlspecialchars($classCode) . "</td>
                        <td>" . htmlspecialchars($className) . "</td>
                        <td>" . htmlspecialchars($classLevel) . "</td>
                        <td>" . htmlspecialchars($classBlock) . "</td>
                        <td>" . htmlspecialchars($classFloor) . "</td>
                        <td>" . htmlspecialchars($classCat) . "</td>
                        <td>" . htmlspecialchars($teacherId) . "</td>
                        <td class='manage-buttons' style='text-align: justify'><a class='view-button' href='adminViewClass.php?id=" . urlencode($classCode) . "'>VIEW</a></td>
                        <td class='manage-buttons'><a class='update-button' href='adminUpdateClass.php?id=" . urlencode($classCode) . "'>UPDATE</a></td>
                        <td class='manage-buttons'><a class='delete-button' onclick='confirmDelete(\"" . htmlspecialchars($classCode) . "\")'>DELETE</a></td>
       
                        </tr>

                    ";

                    }
                } else {
                    // Display message when no classes found
                    echo "
                    <tr>
                        <td colspan='8'>No classes found.</td>
                    </tr>
                    ";
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Go Back</a></div>

    </div>

    <script>
        function confirmDelete(classCode) {
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
                    window.location.href = 'adminClass.php?id=' + classCode;
                }
            });
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>