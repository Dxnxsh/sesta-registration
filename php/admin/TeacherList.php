<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
}

?>
<?php include "../header/adminHeader.php" ?>
<?php
include("../config.php");
// Handle class deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = mysqli_query($con, "DELETE FROM `teacher` WHERE `TEACHER_ID`='$id'");
}

// Handle class search
$searchCondition = "";
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'TEACHER_ID';

if (isset($_GET['searchBox'])) {
    $searchValue = $_GET['searchBox'];
    if ($searchType === 'TEACHER_ID') {
        $searchCondition = "AND t.TEACHER_ID LIKE '%$searchValue%'";
    } elseif ($searchType === 'TEACHER_NAME') {
        $searchCondition = "AND t.TEACHER_NAME LIKE '%$searchValue%'";
    }
}

$select = "SELECT t.*, c.CLASS_CODE 
           FROM teacher t 
           LEFT JOIN class c ON t.TEACHER_ID = c.TEACHER_ID 
           WHERE 1 $searchCondition";

$query = mysqli_query($con, $select);
// Check for errors during the query execution
if (!$query) {
    die('Error in SQL query: ' . mysqli_error($con));
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Teacher Management</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for TeacherList.php */
        .manage-buttons a.back-button {
            background-color: #007BFF;
            width: fit-content;
            margin-top: 30px;
        }

        /* Unique teacher assignment status styling */
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
            <h1>Teacher Management</h1>
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

                <td class='manage-buttons'><a class='view-button' href='adminViewteacher.php?id=" . $result["TEACHER_ID"] . "'>VIEW</a></td>
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
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Back</a></div>

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