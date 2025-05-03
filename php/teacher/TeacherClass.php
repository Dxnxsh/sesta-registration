<?php 
	session_start();
   include("../config.php");
   if(!isset($_SESSION['validTC'])){
    header("Location: ../login-logout/login.php");
   }

if (!isset($_SESSION['validTC'])) {
    header("Location: ../login-logout/login.php");
    exit(); // Add exit to stop further script execution
}


// Assuming 'validTC' is the session variable you want to use
if (isset($_SESSION['validTC'])) {
    $selectData = "SELECT * FROM teacher
    JOIN class ON teacher.TEACHER_ID = class.TEACHER_ID
    WHERE teacher.TEACHER_ID = '" . $_SESSION['validTC'] . "'";

    $queryClass = mysqli_query($con, $selectData) or die(mysqli_error($con));

    // Fetch and process the results
    if ($row_rsClass = mysqli_fetch_assoc($queryClass)) {
        // Process data from the "teacher" and "class" tables
        $className = $row_rsClass['CLASS_NAME'];
        $classlvl = $row_rsClass['CLASS_LEVEL'];
        $blck = $row_rsClass['CLASS_BLOCK'];
        $flr = $row_rsClass['CLASS_FLOOR'];
        $cat = $row_rsClass['CLASS_CAT'];
        $teachName = $row_rsClass['TEACHER_NAME'];
        $teachid = $row_rsClass['TEACHER_ID'];
        $uname = $row_rsClass['TEACHER_USERNAME'];
        $teachphone = $row_rsClass['TEACHER_PHONENUM'];
    }

    // Reset the pointer for the next fetch
    mysqli_data_seek($queryClass, 0);
}
?>
<!DOCTYPE html>


<html lang="en">

<head>
<?php include "../header/teacherHeader.php";?>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../../css/TC.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <title>Teacher Class</title>
</head>

<body>

<h1>CLASS INFORMATION </h1>
    <div class="container">
        <h2>CLASS <?php echo $className ?></h2>
        <table>
            <thead>
                <tr>
                    <th>CODE</th>
                    <th>LEVEL</th>
                    <th>BLOCK</th>
                    <th>FLOOR</th>
                    <th>CATEGORY</th>
                    <th>TEACHER NAME</th>
                    <th>ID</th>
                </tr>
                <tr>
                    <th><?php echo $row_rsClass['CLASS_CODE']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_LEVEL']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_BLOCK']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_FLOOR']; ?></th>
                    <th><?php echo $row_rsClass['CLASS_CAT']; ?></th>
                    <th><?php echo $row_rsClass['TEACHER_NAME']; ?></th>
                    <th><?php echo $row_rsClass['TEACHER_ID']; ?></th>
                </tr>
            </thead>
            <tbody>
            </tbody>
        </table>
    </div>

    <p>&nbsp;</p>

    <div class="container2">
        <h2>LIST OF STUDENTS IN YOUR CLASS</h2>
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>GENDER</th>
                    <th>RELIGION</th>
                    <th>DOB</th>
                    <th>GUARDIAN CONTACT NUMBER</th>
                    <th>EMAIL</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <?php
                $selectData2 = "SELECT * FROM student
                JOIN class ON class.CLASS_CODE = student.CLASS_CODE
                JOIN parent ON student.STUDENT_ID = parent.STUDENT_ID
                WHERE student.CLASS_CODE = '{$row_rsClass['CLASS_CODE']}'";
            
                $queryClass2 = mysqli_query($con, $selectData2) or die(mysqli_error($con));
            
                // Fetch and process the results
                if ($row_rsClass2 = mysqli_fetch_assoc($queryClass2)) {
                    // Process data from the "teacher" and "class" tables
                    $stid = $row_rsClass2['STUDENT_ID'];
                    $stname = $row_rsClass2['STUDENT_NAME'];
                    $stgend = $row_rsClass2['STUDENT_GENDER'];
                    $strel = $row_rsClass2['STUDENT_RELIGION'];
                    $stdob = $row_rsClass2['STUDENT_DOB'];
                    $stpar = $row_rsClass2['PARENT_NAME'];
                    $stctc = $row_rsClass2['PARENT_PHONENUM'];
                    $email = $row_rsClass2['STUDENT_EMAIL'];
                }
            
                // Reset the pointer for the next fetch
                mysqli_data_seek($queryClass2, 0);

                while ($row_rsClass_rsStudent = mysqli_fetch_assoc($queryClass2)) {
                    ?>
                    <tr>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_ID']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_NAME']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_GENDER']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_RELIGION']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_DOB']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['PARENT_PHONENUM']; ?></td>
                        <td><?php echo $row_rsClass_rsStudent['STUDENT_EMAIL']; ?></td>
                    </tr>
                    <?php
                }
                ?>
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
<?php include "../header/footer.php" ?>