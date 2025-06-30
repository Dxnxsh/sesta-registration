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
    <h1>Assigned Class</h1>
    <div class="container">
        <h2>CLASS <?php echo htmlspecialchars($className ?? 'N/A'); ?></h2>
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
                <?php if (isset($row_rsClass) && $row_rsClass): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row_rsClass['CLASS_CODE'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['CLASS_LEVEL'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['CLASS_BLOCK'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['CLASS_FLOOR'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['CLASS_CAT'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['TEACHER_NAME'] ?? ''); ?></td>
                        <td><?php echo htmlspecialchars($row_rsClass['TEACHER_ID'] ?? ''); ?></td>
                    </tr>
                <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No class information found for this teacher.</td>
                </tr>
                <?php endif; ?>
            </thead>
            <tbody></tbody>
        </table>

        <br>

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
                </tr>
            </thead>

            <tbody>
                <?php
                if (isset($row_rsClass) && !empty($row_rsClass['CLASS_CODE'])) {
                    $selectData2 = "SELECT * FROM student
                    JOIN class ON class.CLASS_CODE = student.CLASS_CODE
                    JOIN parent ON student.PARENT_ID = parent.PARENT_ID
                    WHERE student.CLASS_CODE = '{$row_rsClass['CLASS_CODE']}'";

                    $queryClass2 = mysqli_query($con, $selectData2) or die(mysqli_error($con));

                    if (mysqli_num_rows($queryClass2) > 0) {
                        while ($row_rsClass_rsStudent = mysqli_fetch_assoc($queryClass2)) {
                ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_ID'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_NAME'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_GENDER'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_RELIGION'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_DOB'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['PARENT_PHONENUM'] ?? ''); ?></td>
                                <td><?php echo htmlspecialchars($row_rsClass_rsStudent['STUDENT_EMAIL'] ?? ''); ?></td>
                            </tr>
                <?php
                        }
                    } else {
                        echo '<tr><td colspan="7" style="text-align: center; padding: 20px;">No students found in this class.</td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="7" style="text-align: center; padding: 20px;">No class assigned to this teacher.</td></tr>';
                }
                ?>
            </tbody>
        </table>

        <button class="back-button" onclick="window.location.href = '../teacher/teacher_home.php';">Go Back</button>
    </div>
</body>

</html>
<?php include "../header/footer.php" ?>