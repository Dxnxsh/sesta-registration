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
    <title>Student Information</title>
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
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Student Information</h1>
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
        <div class='manage-buttons'><a class='back-button' href='admin_home.php'>Go Back</a></div>

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
                    window.location.href = 'studentList.php?id=' + studentCode;
                }
            });
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>