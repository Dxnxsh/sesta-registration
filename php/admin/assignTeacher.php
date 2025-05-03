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



$select = "SELECT * FROM class 
     WHERE TEACHER_ID IS NULL";
$query = mysqli_query($con, $select);

// Check if the query was successful
if (!$query) {
    die('Error executing the query: ' . mysqli_error($con));
}

// Check if the query result is empty
if (mysqli_num_rows($query) == 0) {
    echo "<script>
        // Use setTimeout to wait for 2 seconds before redirecting
        setTimeout(function() {
            Swal.fire({
                title: 'No Class available!',
                text: 'There are no class for teachers to assign.',
                icon: 'info'
            }).then(function() {
                window.location.href = 'teacherList.php';
            });
        }, 500); // 0.5 milliseconds = 0.5 seconds
    </script>";

    // Prevent further execution of the script
    exit;
}


if (isset($_POST['submit2'])) {
    // Check if the teacher_id array is set
    if (isset($_POST['teacher_id']) && is_array($_POST['teacher_id'])) {
        foreach ($_POST['teacher_id'] as $classID => $selectedClassCode) {
            // Check if the specific submit button is set
            if (isset($_POST['submit2'][$classID])) {
                // Update the teacher_id for each class
                $updateQuery = mysqli_query($con, "UPDATE class SET TEACHER_ID='$selectedClassCode' WHERE CLASS_CODE='$classID'");
                $updateQuery2 = mysqli_query($con, "UPDATE class SET ADMIN_ID='" . $_SESSION['adminID'] . "' WHERE CLASS_CODE='$classID'");
                  if ($updateQuery && $updateQuery2) {
                    echo "<script>
                        Swal.fire({
                            title: 'Assigned!',
                            text: 'Teacher assigned successfully.',
                            icon: 'success'
                        }).then(function() {
                            window.location.href = 'teacherList.php';
                        });
                    </script>";
                } else {
                    // Handle the error scenario
                    echo "<script>
                        Swal.fire({
                            title: 'Error!',
                            text: 'Selected class already have Teacher.',
                            icon: 'error'
                        }).then(function() {
                            window.location.href = 'assignteacher.php';
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
                $num = mysqli_num_rows($query);
                if ($num > 0) {
                    while ($result = mysqli_fetch_assoc($query)) {
                        echo "
                        <tr>
                            <td>" . $result["CLASS_NAME"] . "</td>
                            <td>" . $result["CLASS_CODE"] . "</td>
                            <td>" . $result["CLASS_LEVEL"] . " </td>
                            <td>" . $result["CLASS_CAT"] . " </td>
                            <td>
                                <select name='teacher_id[" . $result["CLASS_CODE"] . "]'>
                                    <option value='' selected disabled>Select Class</option>";

                        // Fetch and display teacher codes
                        $teacherQuery = mysqli_query($con, "SELECT * FROM teacher");
                        while ($teacherResult = mysqli_fetch_assoc($teacherQuery)) {
                            echo "<option value='" . $teacherResult["TEACHER_ID"] . "'>" . $teacherResult["TEACHER_ID"] . "</option>";
                        }
                        echo "</select>
                            </td>
                            <td class='manage-buttons' style='text-align: justify'>
                                <input class='button' type='submit' name='submit2[" . $result["CLASS_CODE"] . "]' value='Assign Students'>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='teacherList.php'>Go Back</a></div>

    </div>

</body>

</html>
<?php include "../header/footer.php" ?>