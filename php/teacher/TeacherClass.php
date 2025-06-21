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
    $teacherId = SecuritySanitizer::sanitize($_SESSION['validTC'], 'id', 'TEACHER_ID');
    $selectData = "SELECT * FROM teacher
    JOIN class ON teacher.TEACHER_ID = class.TEACHER_ID
    WHERE teacher.TEACHER_ID = '$teacherId'";

    $queryClass = mysqli_query($con, $selectData) or die(mysqli_error($con));

    // Fetch and process the results
    if ($row_rsClass = mysqli_fetch_assoc($queryClass)) {
        // Process data from the "teacher" and "class" tables
        $className = SecuritySanitizer::sanitize($row_rsClass['CLASS_NAME'], 'class_name');
        $classlvl = SecuritySanitizer::sanitize($row_rsClass['CLASS_LEVEL'], 'class_level');
        $blck = SecuritySanitizer::sanitize($row_rsClass['CLASS_BLOCK'], 'class_block');
        $flr = SecuritySanitizer::sanitize($row_rsClass['CLASS_FLOOR'], 'floor');
        $cat = SecuritySanitizer::sanitize($row_rsClass['CLASS_CAT'], 'class_category');
        $teachName = SecuritySanitizer::sanitize($row_rsClass['TEACHER_NAME'], 'name');
        $teachid = SecuritySanitizer::sanitize($row_rsClass['TEACHER_ID'], 'id');
        $uname = SecuritySanitizer::sanitize($row_rsClass['TEACHER_USERNAME'], 'username');
        $teachphone = SecuritySanitizer::sanitize($row_rsClass['TEACHER_PHONENUM'], 'phone');
        
        SecuritySanitizer::logSecurityEvent('teacher_class_access', [
            'teacher_id' => $teachid,
            'class_code' => $row_rsClass['CLASS_CODE'] ?? ''
        ]);
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
                    <th><?php echo htmlspecialchars($row_rsClass['CLASS_CODE'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['CLASS_LEVEL'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['CLASS_BLOCK'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['CLASS_FLOOR'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['CLASS_CAT'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['TEACHER_NAME'] ?? ''); ?></th>
                    <th><?php echo htmlspecialchars($row_rsClass['TEACHER_ID'] ?? ''); ?></th>
                </tr>
                <?php else: ?>
                <tr>
                    <td colspan="7" style="text-align: center; padding: 20px;">No class information found for this teacher.</td>
                </tr>
                <?php endif; ?>
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
                // Check if class data exists before querying students
                if (isset($row_rsClass) && !empty($row_rsClass['CLASS_CODE'])) {
                    $classCode = SecuritySanitizer::sanitize($row_rsClass['CLASS_CODE'], 'class_code', 'CLASS_CODE');
                    $selectData2 = "SELECT * FROM student
                    JOIN class ON class.CLASS_CODE = student.CLASS_CODE
                    JOIN parent ON student.PARENT_ID = parent.PARENT_ID
                    WHERE student.CLASS_CODE = '$classCode'";

                    $queryClass2 = mysqli_query($con, $selectData2) or die(mysqli_error($con));

                // Check if any records exist
                if (mysqli_num_rows($queryClass2) > 0) {
                    // Fetch and process the results
                    if ($row_rsClass2 = mysqli_fetch_assoc($queryClass2)) {
                        // Process and sanitize data from the "student" and "parent" tables
                        $stid = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_ID'], 'id');
                        $stname = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_NAME'], 'name');
                        $stgend = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_GENDER'], 'gender');
                        $strel = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_RELIGION'], 'religion');
                        $stdob = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_DOB'], 'date');
                        $stpar = SecuritySanitizer::sanitize($row_rsClass2['PARENT_NAME'], 'name');
                        $stctc = SecuritySanitizer::sanitize($row_rsClass2['PARENT_PHONENUM'], 'phone');
                        $email = SecuritySanitizer::sanitize($row_rsClass2['STUDENT_EMAIL'], 'email');
                    }

                    // Reset the pointer for the next fetch
                    mysqli_data_seek($queryClass2, 0);

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
                    // Display message when no students found
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No students found in this class.</td>
                    </tr>
                    <?php
                }
                } else {
                    // Display message when no class data found
                    ?>
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 20px;">No class assigned to this teacher.</td>
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