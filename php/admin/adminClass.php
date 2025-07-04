<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
}

?>
<?php include "../header/adminHeader.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Class Management</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for adminClass.php */
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

        /* Custom search styling for this page */
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

        #searchType::after {
            content: '\25BC';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Override common searchBox style for this page */
        #searchBox {
            width: fit-content;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: aliceblue;
        }

        /* Custom submit button for this page */
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
// Handle class deletion
if (isset($_GET['id'])) {
    $classCode = $_GET['id'];

    // Check if there are associated teachers in the class
    $checkTeachersQuery = mysqli_query($con, "SELECT COUNT(*) as teacherCount FROM teacher WHERE TEACHER_ID IN (SELECT TEACHER_ID FROM class WHERE CLASS_CODE='$classCode')");
    $checkTeachersResult = mysqli_fetch_assoc($checkTeachersQuery);
    $teacherCount = $checkTeachersResult['teacherCount'];

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

    // If no associated teachers, proceed with deletion
    $delete = mysqli_query($con, "DELETE FROM `class` WHERE `CLASS_CODE`='$classCode'");
}
// Handle class search
$searchCondition = "";
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'CLASS_CODE';

if (isset($_GET['searchBox'])) {
    $searchValue = $_GET['searchBox'];
    if ($searchType === 'CLASS_CODE') {
        $searchCondition = "AND CLASS_CODE LIKE '%$searchValue%'";
    } elseif ($searchType === 'CLASS_NAME') {
        $searchCondition = "AND CLASS_NAME LIKE '%$searchValue%'";
    } elseif ($searchType === 'TEACHER_ID') {
        $searchCondition = "AND TEACHER_ID LIKE '%$searchValue%'";
    }
}

$select = "SELECT * FROM class WHERE 1 $searchCondition";
$query = mysqli_query($con, $select);
?>
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Class Management</h1>
            <div class="search-container">
                <div class="selectSearch"><select name="searchType" id="searchType">
                        <option value="CLASS_CODE">Class Code</option>
                        <option value="CLASS_NAME">Class Name</option>
                        <option value="TEACHER_ID">Teacher ID</option>
                    </select></div>
                <input name="searchBox" type="text" id="searchBox" placeholder="Search...">
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
                        echo "
                    <tr>
                        <td>" . $result["CLASS_CODE"] . "</td>
                        <td>" . $result["CLASS_NAME"] . "</td>
                        <td>" . $result["CLASS_LEVEL"] . "</td>
                        <td>" . $result["CLASS_BLOCK"] . "</td>
                        <td>" . $result["CLASS_FLOOR"] . "</td>
                        <td>" . $result["CLASS_CAT"] . "</td>
                        <td>" . $result["TEACHER_ID"] . "</td>
                        <td class='manage-buttons' style='text-align: justify'><a class='view-button' href='adminViewClass.php?id=" . $result["CLASS_CODE"] . "'>VIEW</a></td>
                        <td class='manage-buttons'><a class='update-button' href='adminUpdateClass.php?id=" . $result["CLASS_CODE"] . "'>UPDATE</a></td>
                        <td class='manage-buttons'><a class='delete-button' onclick='confirmDelete(\"" . $result["CLASS_CODE"] . "\")'>DELETE</a></td>
       
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
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Back</a></div>

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