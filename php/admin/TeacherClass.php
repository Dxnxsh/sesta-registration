
<?php
session_start();

include("config.php");
if (!isset($_SESSION['validTc'])) {
    header("Location: LoginTeacher.php");
}
require_once('../Connections/registersekolah.php');
?>
<?php
if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($conn, $theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "")
    {
        if (PHP_VERSION < 6) {
            $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
        }

        $theValue = $conn->real_escape_string($theValue);

        switch ($theType) {
            case "text":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "long":
            case "int":
                $theValue = ($theValue != "") ? intval($theValue) : "NULL";
                break;
            case "double":
                $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
                break;
            case "date":
                $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
                break;
            case "defined":
                $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
                break;
        }
        return $theValue;
    }
}

$conn = new mysqli($hostname_registersekolah, $username_registersekolah, $password_registersekolah, $database_registersekolah);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Assuming TEACHER_ID is stored in the session, modify this part accordingly
$teacherId = $_SESSION['validTc'];

// Query to fetch class information
$query_rsClass = "SELECT * FROM `class` WHERE TEACHER_ID = '$teacherId'";
$rsClass = $conn->query($query_rsClass) or die($conn->error);
$row_rsClass = $rsClass->fetch_assoc();
$totalRows_rsClass = $rsClass->num_rows;

// Query to fetch students registered under the specific CLASS_CODE
$classCode = $row_rsClass['CLASS_CODE'];
$query_rsStudent = "SELECT student.*
                    FROM student
                    JOIN class ON student.CLASS_CODE = class.CLASS_CODE
                    WHERE class.CLASS_CODE = '$classCode'";
$rsStudent = $conn->query($query_rsStudent) or die($conn->error);

?>
<?php include "teacherHeader.php" ?>
<!DOCTYPE html>


<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../style/TC.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <title>Teacher Class</title>
</head>

<body>
    
	<h1>TEACHER ASSIGNED CLASS</h1>
    <div class="container">
      <h2>Welcome, Teacher</h2>
        <table>
            <thead>
                <tr>
                  <th>CLASS CODE</th>
                  <th>CLASS NAME</th>
                  <th>CLASS CATEGORY</th>
                  <th>TEACHER NAME</th>
                </tr>
                <tr>
                    <th><?php echo $row_rsClass['CLASS_CODE']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_NAME']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_CAT']; ?></th>
                    <th><?php echo $row_rsClass['TEACHER_ID']; ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <p>&nbsp;</p>

    <div class="container2">
        <h2>CLASS STUDENT</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>GENDER</th>
                    <th>DOB</th>
                    <th>POB</th>
                    <th>RELIGION</th>
                    <th>RACE</th>
                    <th>NATIONALITY</th>
                    <th>ADDRESS</th>
                    <th>DISEASE</th>
                    <th>DISABILITY</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row_rsStudent = $rsStudent->fetch_assoc()) { ?>
                    <tr>
                        <td><?php echo $row_rsStudent['STUDENT_ID']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_NAME']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_GENDER']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_DOB']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_POB']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_RELIGION']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_RACE']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_NATIONALITY']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_ADDRESS']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_DISEASE']; ?></td>
                        <td><?php echo $row_rsStudent['STUDENT_DISABILITY']; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
        <button class="back-button" onclick="goBack()">Go Back</button>
    </div>

    <script>
        function goBack() {
            window.history.back();
        }
    </script>
</body>
</html>
<?php
$rsClass->free_result();
$rsStudent->free_result();
$conn->close();
?>
<?php include "footer.php" ?>