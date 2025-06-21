<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize the student ID from GET parameter
$id = SecuritySanitizer::sanitize($_GET['id'] ?? '', 'id', 'STUDENT_ID');
$oldparIC = "";
$yearPrefix = substr($id, 0, 2);

// Add this block to adjust the year
if ($yearPrefix >= 30 && $yearPrefix <= 99) {
    $year = "19" . $yearPrefix;
} else {
    $year = "20" . $yearPrefix;
}

$monthPrefix = substr($id, 2, 2);
$dayPrefix = substr($id, 4, 2);

// Combine the variables to create $dobpredict
$dobpredict = "$year-$monthPrefix-$dayPrefix";

// Use prepared statement for secure query
$stmt = $con->prepare("SELECT * FROM student s INNER JOIN parent c ON s.PARENT_ID = c.PARENT_ID WHERE s.STUDENT_ID = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$queryStudParent = $stmt->get_result();

if (!$queryStudParent) {
    SecuritySanitizer::logSecurityEvent('admin_update_student_query_failed', [
        'student_id' => $id,
        'admin_id' => $_SESSION['adminID'] ?? '',
        'error' => 'Database query failed'
    ]);
    die('Error: Unable to retrieve student data.');
}

// Fetch and process the results
while ($row = $queryStudParent->fetch_assoc()) {
    // Sanitize all database outputs
    $studname = SecuritySanitizer::sanitize($row['STUDENT_NAME'], 'name');
    $studGender = SecuritySanitizer::sanitize($row['STUDENT_GENDER'], 'gender');
    $studLevel = SecuritySanitizer::sanitize($row['STUDENT_LEVEL'], 'class_level');
    $studDOB = SecuritySanitizer::sanitize($row['STUDENT_DOB'], 'date');
    $studPOB = SecuritySanitizer::sanitize($row['STUDENT_POB'], 'name');
    $studReligion = SecuritySanitizer::sanitize($row['STUDENT_RELIGION'], 'religion');
    $studRace = SecuritySanitizer::sanitize($row['STUDENT_RACE'], 'race');
    $studNationality = SecuritySanitizer::sanitize($row['STUDENT_NATIONALITY'], 'nationality');
    $studAddress = SecuritySanitizer::sanitize($row['STUDENT_ADDRESS'], 'address');
    $studDisease = SecuritySanitizer::sanitize($row['STUDENT_DISEASE'], 'name');
    $studDisable = SecuritySanitizer::sanitize($row['STUDENT_DISABILITY'], 'name');
    $studStatus = SecuritySanitizer::sanitize($row['STUDENT_STATUS'], 'status');

    $oldparIC = SecuritySanitizer::sanitize($row['PARENT_ID'], 'id');
    $parIC = SecuritySanitizer::sanitize($row['PARENT_ID'], 'id');
    $parName = SecuritySanitizer::sanitize($row['PARENT_NAME'], 'name');
    $parGender = SecuritySanitizer::sanitize($row['PARENT_GENDER'], 'gender');
    $parPhone = SecuritySanitizer::sanitize($row['PARENT_PHONENUM'], 'phone');
    $parJob = SecuritySanitizer::sanitize($row['PARENT_JOB'], 'job');
    $parSalary = SecuritySanitizer::sanitize($row['PARENT_MONTHLY_INCOME'], 'decimal');
}
$stmt->close();



if (isset($_POST['submit'])) {
    // Sanitize all form inputs with proper types
    $studname = SecuritySanitizer::sanitizeForDB($_POST['studentName'] ?? '', 'name', 'STUDENT_NAME');
    $studGender = SecuritySanitizer::sanitizeForDB($_POST['gender'] ?? '', 'gender', 'STUDENT_GENDER');
    $studLevel = SecuritySanitizer::sanitizeForDB($_POST['level'] ?? '', 'class_level', 'STUDENT_LEVEL');
    $studDOB = SecuritySanitizer::sanitizeForDB($_POST['dob'] ?? '', 'date', 'STUDENT_DOB');
    $studPOB = SecuritySanitizer::sanitizeForDB($_POST['placeOfBirth'] ?? '', 'name', 'STUDENT_POB');
    $studReligion = SecuritySanitizer::sanitizeForDB($_POST['religion'] ?? '', 'religion', 'STUDENT_RELIGION');
    $studRace = SecuritySanitizer::sanitizeForDB($_POST['race'] ?? '', 'race', 'STUDENT_RACE');
    $studNationality = SecuritySanitizer::sanitizeForDB($_POST['nationality'] ?? '', 'nationality', 'STUDENT_NATIONALITY');
    $studAddress = SecuritySanitizer::sanitizeForDB($_POST['address'] ?? '', 'address', 'STUDENT_ADDRESS');
    $studDisease = SecuritySanitizer::sanitizeForDB($_POST['disease'] ?? '', 'name', 'STUDENT_DISEASE');
    $studDisable = SecuritySanitizer::sanitizeForDB($_POST['disability'] ?? '', 'name', 'STUDENT_DISABILITY');
    $studStatus = SecuritySanitizer::sanitizeForDB($_POST['status'] ?? '', 'status', 'STUDENT_STATUS');

    $parIC = SecuritySanitizer::sanitizeForDB($_POST['parentIC'] ?? '', 'id', 'PARENT_ID');
    $parName = SecuritySanitizer::sanitizeForDB($_POST['parentName'] ?? '', 'name', 'PARENT_NAME');
    $parGender = SecuritySanitizer::sanitizeForDB($_POST['parentGender'] ?? '', 'gender', 'PARENT_GENDER');
    $parPhone = SecuritySanitizer::sanitizeForDB($_POST['parentPhone'] ?? '', 'phone', 'PARENT_PHONENUM');
    $parJob = SecuritySanitizer::sanitizeForDB($_POST['parentJob'] ?? '', 'job', 'PARENT_JOB');
    $parSalary = SecuritySanitizer::sanitizeForDB($_POST['parentIncome'] ?? '', 'decimal', 'PARENT_MONTHLY_INCOME');


    // Modify $studLevel based on student level to be insert into db
    if ($studLevel == "Form 4") {
        $studLevel = "4";
    } elseif ($studLevel == "Form 1") {
        $studLevel = "1";
    }

    // Update parent table using prepared statement
    $stmt = $con->prepare("UPDATE `parent` SET `PARENT_ID`=?, `PARENT_NAME`=?, `PARENT_GENDER`=?, `PARENT_PHONENUM`=?, `PARENT_JOB`=?, `PARENT_MONTHLY_INCOME`=? WHERE `PARENT_ID`=?");
    $stmt->bind_param("sssssds", $parIC, $parName, $parGender, $parPhone, $parJob, $parSalary, $oldparIC);
    
    if (!$stmt->execute()) {
        SecuritySanitizer::logSecurityEvent('admin_update_parent_failed', [
            'parent_id' => $oldparIC,
            'new_parent_id' => $parIC,
            'admin_id' => $_SESSION['adminID'] ?? '',
            'error' => $stmt->error
        ]);
        die("Error updating parent data.");
    }
    $stmt->close();

    // Update student table using prepared statement
    $stmt = $con->prepare("UPDATE `student` SET `STUDENT_NAME`=?, `STUDENT_GENDER`=?, `STUDENT_LEVEL`=?, `STUDENT_DOB`=?, `STUDENT_POB`=?, `STUDENT_RELIGION`=?, `STUDENT_RACE`=?, `STUDENT_NATIONALITY`=?, `STUDENT_ADDRESS`=?, `STUDENT_DISEASE`=?, `STUDENT_DISABILITY`=?, `STUDENT_STATUS`=? WHERE `STUDENT_ID`=?");
    $stmt->bind_param("sssssssssssss", $studname, $studGender, $studLevel, $studDOB, $studPOB, $studReligion, $studRace, $studNationality, $studAddress, $studDisease, $studDisable, $studStatus, $id);
    
    if (!$stmt->execute()) {
        SecuritySanitizer::logSecurityEvent('admin_update_student_failed', [
            'student_id' => $id,
            'admin_id' => $_SESSION['adminID'] ?? '',
            'error' => $stmt->error
        ]);
        die("Error updating student data.");
    }
    $stmt->close();

    // Log successful update
    SecuritySanitizer::logSecurityEvent('admin_updated_student', [
        'student_id' => $id,
        'parent_id' => $parIC,
        'admin_id' => $_SESSION['adminID'] ?? ''
    ]);

    // Redirect to student_home.php after processing the form data
    header("Location: StudentList.php");
    exit();
}


?>


<!doctype html>
<?php include "../header/adminHeader.php" ?>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="../../css/SRegis.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Student Update details</title>


</head>

<body>
    <div class="container">
        <div class='btn'><a class='btn btn-back' href='StudentList.php'>Go Back</a></div>
        <form name="studentRegister" method="post" id="studentRegister">
            <h1><img src="../../image/icon/student.png" alt="Search Icon" width="50" height="45" class="img-icon">
                Student Registration</h1>
            <div class="container2">
                <div style="display: block;">
                    <h2>Student Information :</h2>
                    <p><b>
                            <label>Education Level : </label></b>
                        <input type="radio" id="form1" name="level" value="Form 1" <?php echo ($studLevel == '1') ? 'checked' : ''; ?> required>
                        <label for="Form1">Form 1</label>
                        <input type="radio" id="form4" name="level" value="Form 4" <?php echo ($studLevel == '4') ? 'checked' : ''; ?> required>
                        <label for="Form4">Form 4</label>
                        <br><br><b>
                            <label for="studentName">Student Name :</label>
                            <input type="text" id="studentName" value="<?php echo $studname ?>" name="studentName" required></b>
                    </p>
                    <label>Gender : </label>
                    <input type="radio" id="male" name="gender" value="Male" <?php echo ($studGender == 'Male') ? 'checked' : ''; ?> required></b>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="Female" <?php echo ($studGender == 'Female') ? 'checked' : ''; ?> required>
                    <label for="female">Female</label>
                    <br>
                    <br><b>
                        <label>Status : </label>
                        <input type="radio" id="status" name="status" value="Single" <?php echo ($studStatus == 'Single') ? 'checked' : ''; ?> required>
                    </b>
                    <label for="stat">Single</label>
                    <input type="radio" id="statusM" name="status" value="Married" <?php echo ($studStatus == 'Married') ? 'checked' : ''; ?> required>
                    <label for="stat2">Married</label>
                    </p>
                    <p><b>
                            <label for="dob">
                                <br>
                                Date of Birth : </label>
                            <input type="date" id="dob" name="dob"
                                value="<?= date('Y-m-d', strtotime($dobpredict)); ?>" required>
                    </p>
                    <p><br>
                        <label for="placeOfBirth">Place of Birth :</label>
                        <input type="text" id="placeOfBirth" name="placeOfBirth" value="<?php echo $studDOB ?>"
                            required>

                        <label for="religion"><br>
                            Religion :</label>
                        <input type="text" id="religion" name="religion" value="<?php echo $studReligion ?>"
                            required>
                    </p>
                    <p>

                        <label for="race">Race :</label>
                        <input type="text" id="race" name="race" value="<?php echo $studRace ?>">

                        <label for="nationality"><br>Nationality :</label>
                        <input type="text" id="nationality" name="nationality"
                            value="<?php echo $studNationality ?>">
                    </p>
                    <p>

                        <label for="address">Address :</label>
                        <textarea id="address"
                            name="address"><?php echo isset($studAddress) ? $studAddress : ''; ?></textarea>
                        <label><br>
                            Disease :</label>
                        <input type="text" id="disease" name="disease"
                            value="<?php echo (!empty($studDisease) ? $studDisease : ''); ?>"
                            placeholder="Enter if there is a disease">
                    </p>
                    <p>

                        <label>Disability :</label>
                        <input type="text" id="disability" name="disability"
                            value="<?php echo (!empty($studDisable) ? $studDisable : ''); ?>"
                            placeholder="Enter if there is a diability">
                    </p>

                </div>
            </div>
            <div class="container2">
                <div style="display: block;">
                    <h2>Father/Mother/Guardian Information :</h2>

                    <p>
                        <label for="parentName">Name :</label>
                        <input type="text" id="parentName" name="parentName" value="<?php echo $parName ?>" required>

                        <label><br>
                            <br>
                            Gender :</label>
                        <input type="radio" id="parentMale" name="parentGender" value="Male" <?php echo ($parGender == 'Male') ? 'checked' : ''; ?> required></b>
                        <label for="parent1Male">Male</label>
                        <input type="radio" id="parentFemale" name="parentGender" value="Female" <?php echo ($parGender == 'Female') ? 'checked' : ''; ?> required>
                        <label for="parentFemale">Female</label>
                    </p>
                    <p>&nbsp;</p>
                    <p><b>
                            <label for="parent1IC">No.KP (IC Number) :</label>
                            <input type="text" id="parentIC" name="parentIC" pattern="\d{12}" required
                                value="<?php echo $parIC ?>">
                    </p>
                    <p>

                        <label for="parent1Phone">Phone No. :</label>
                        <input type="text" id="parentPhone" name="parentPhone" required value="<?php echo $parPhone ?>">

                    </p>
                    <p>
                        <label for="parent1Job">Job :</label>
                        <input type="text" id="parentJob" name="parentJob" value="<?php echo $parJob ?>">

                        <label for="parent1Income"><br>
                            Monthly Income :</label>
                        <input type="int" id="parentIncome" name="parentIncome" value="<?php echo $parSalary ?>">


                        </b>
                    </p>
                </div>
            </div>
            <div class="button-container">
                <button type="submit" name="submit" class="btn btn-admin">Save</button>
            </div>
        </form>

    </div>
</body>

</html>