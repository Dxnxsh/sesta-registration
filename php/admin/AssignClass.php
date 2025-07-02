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
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for AssignClass.php */
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



$select = "SELECT * FROM student 
     WHERE CLASS_CODE IS NULL AND PARENT_ID IS NOT NULL";
$query = mysqli_query($con, $select);

$num = mysqli_num_rows($query);

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
            // Check if the specific submit button is set
            if (isset($_POST['submit2'][$studentID])) {
                // Update the class_code for each student
                $updateQuery = mysqli_query($con, "UPDATE student SET CLASS_CODE='$selectedClassCode' WHERE STUDENT_ID='$studentID'");
                if ($updateQuery) {
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
                $num = mysqli_num_rows($query);
                if ($num > 0) {
                    while ($result = mysqli_fetch_assoc($query)) {
                        echo "
                        <tr>
                            <td>" . $result["STUDENT_NAME"] . "</td>
                            <td>" . $result["STUDENT_ID"] . "</td>
                            <td>" . $result["STUDENT_LEVEL"] . " </td>
                            <td>
                                <select name='class_code[" . $result["STUDENT_ID"] . "]'>
                                    <option value='' selected disabled>Select Class</option>";

                        // Fetch and display class codes
                        $classQuery = mysqli_query($con, "SELECT * FROM class");
                        while ($classResult = mysqli_fetch_assoc($classQuery)) {
                            echo "<option value='" . $classResult["CLASS_CODE"] . "'>" . $classResult["CLASS_CODE"] . "</option>";
                        }
                        echo "</select>
                            </td>
                            <td class='manage-buttons' style='text-align: justify'>
                                <input class='button' type='submit' name='submit2[" . $result["STUDENT_ID"] . "]' value='Assign Students'>
                            </td>
                        </tr>";
                    }
                }
                ?>
            </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='StudentList.php'>Back</a></div>

    </div>

</body>

</html>
<?php include "../header/footer.php" ?>