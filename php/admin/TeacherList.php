<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/loginAdmin.php");
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
            <h1>Teacher List</h1>
            <div class="search-container">
                <div class="selectSearch"><select name="searchType" id="searchType">
                        <option value="TEACHER_ID">teacher ID</option>
                        <option value="TEACHER_NAME">teacher NAME</option>
                    </select></div>
                <input name="searchBox" type="text" id="searchBox" placeholder="Search...">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="teacherList.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <p>
                <input class="button" type="submit" name="submit2" id="submit2" formaction="AssignTeacher.php"
                    value="Assign teachers">
        
                <input class="button" type="submit" name="submit3" id="submit3" formaction="addnewteacher.php"
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
                        echo "
            <tr>
                <td>" . $result["TEACHER_NAME"] . "</td>
                <td>" . $result["TEACHER_ID"] . "</td>              
                <td>" . ($classCode ? $classCode : 'Not Assigned') . "</td>
                <td>" . $result["TEACHER_PHONENUM"] . "</td>
                <td>" . $result["TEACHER_EMAIL"] . "</td>

                <td class='manage-buttons' style='text-align: justify'><a class='view-button' href='adminViewteacher.php?id=" . $result["TEACHER_ID"] . "'>VIEW</a></td>
                <td class='manage-buttons'><a class='update-button' href='adminUpdateteacher.php?id=" . $result["TEACHER_ID"] . "'>UPDATE</a></td>
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
        <div class='manage-buttons'><a class='back-button' href='admin_home.php'>Go Back</a></div>

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
                    window.location.href = 'teacherList.php?id=' + classCode;
                }
            });
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>