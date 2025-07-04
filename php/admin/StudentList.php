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
// Handle student deletion
if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $delete = mysqli_query($con, "DELETE FROM `student` WHERE `STUDENT_ID`='$id'");
}

// Handle student search
$searchCondition = "";
$searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'STUDENT_ID';

if (isset($_GET['searchBox'])) {
    $searchValue = $_GET['searchBox'];
    if ($searchType === 'STUDENT_ID') {
        $searchCondition = "AND STUDENT_ID LIKE '%$searchValue%'";
    } elseif ($searchType === 'STUDENT_NAME') {
        $searchCondition = "AND STUDENT_NAME LIKE '%$searchValue%'";
    }
}

$select = "SELECT * FROM student WHERE 1 $searchCondition AND PARENT_ID IS NOT NULL"; // Include search condition here
$query = mysqli_query($con, $select);
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Student Management</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for StudentList.php */
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
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Student Management</h1>
            <div class="search-container">
                <div class="selectSearch"><select name="searchType" id="searchType">
                        <option value="STUDENT_ID">STUDENT ID</option>
                        <option value="STUDENT_NAME">STUDENT NAME</option>
                    </select></div>
                <input name="searchBox" type="text" id="searchBox" placeholder="Search...">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="StudentList.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <p>
                <input class="button" type="submit" name="submit2" id="submit2" formaction="AssignClass.php"
                    value="Assign Students">
            </p>
            <table width="163%">
                <tr>
                    <th>NAME</th>
                    <th>ID</th>
                    <th>CLASS</th>
                    <th>LEVEL</th>
                    <th>EMAIL</th>
                    <th colspan="6">MANAGE</th>
                </tr>
                <?php
                $num = mysqli_num_rows($query);
                if ($num > 0) {
                    while ($result = mysqli_fetch_assoc($query)) {
                        echo "
                    <tr>
                        <td>" . $result["STUDENT_NAME"] . "</td>
                        <td>" . $result["STUDENT_ID"] . "</td>              
                        <td>" . $result["CLASS_CODE"] . "</td>
                        <td>" . $result["STUDENT_LEVEL"] . "</td>
                        <td>" . $result["STUDENT_EMAIL"] . "</td>
    
                        <td class='manage-buttons' style='text-align: justify'><a class='view-button' href='adminViewStudent.php?id=" . $result["STUDENT_ID"] . "'>VIEW</a></td>
                        <td class='manage-buttons'><a class='update-button' href='adminUpdateStudent.php?id=" . $result["STUDENT_ID"] . "'>UPDATE</a></td>
                        <td class='manage-buttons'><a class='delete-button' onclick='confirmDelete(\"" . $result["STUDENT_ID"] . "\")'>DELETE</a></td>
       
                        </tr>

                    ";

                    }
                }

                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Back</a></div>

    </div>

    <script>
        function confirmDelete(studentCode) {
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
                        text: 'Student has been successfully deleted.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        // then trigger the actual delete via redirect-with-id
                        window.location.href = 'StudentList.php?id=' + studentCode;
                    });
                }
            });
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>