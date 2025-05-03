<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}


$id = $_GET['id'];
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

$selectStudParent = "SELECT * FROM student s
INNER JOIN parent c ON s.PARENT_ID = c.PARENT_ID
WHERE s.STUDENT_ID = '$id'";
$queryStudParent = mysqli_query($con, $selectStudParent);

if (!$queryStudParent) {
    die('Error: ' . mysqli_error($con));
}

// Fetch and process the results
while ($row = mysqli_fetch_assoc($queryStudParent)) {
    // Process each row of data
    // $row contains the combined data from both "student" and "class" tables
    $studname = $row['STUDENT_NAME'];
    $studGender = $row['STUDENT_GENDER'];
    $studLevel = $row['STUDENT_LEVEL'];
    $studDOB = $row['STUDENT_DOB'];
    $studPOB = $row['STUDENT_POB'];
    $studReligion = $row['STUDENT_RELIGION'];
    $studRace = $row['STUDENT_RACE'];
    $studNationality = $row['STUDENT_NATIONALITY'];
    $studAddress = $row['STUDENT_ADDRESS'];
    $studDisease = $row['STUDENT_DISEASE'];
    $studDisable = $row['STUDENT_DISABILITY'];
    $studStatus = $row['STUDENT_STATUS'];

    $oldparIC = $row['PARENT_ID'];
    $parIC = $row['PARENT_ID'];
    $parName = $row['PARENT_NAME'];
    $parGender = $row['PARENT_GENDER'];
    $parPhone = $row['PARENT_PHONENUM'];
    $parJob = $row['PARENT_JOB'];
    $parSalary = $row['PARENT_MONTHLY_INCOME'];
}



if (isset($_POST['submit'])) {

    $studname = $_POST['studentName'];
    $studGender = $_POST['gender'];
    $studLevel = $_POST['level'];
    $studDOB = $_POST['dob'];
    $studPOB = $_POST['placeOfBirth'];
    $studReligion = $_POST['religion'];
    $studRace = $_POST['race'];
    $studNationality = $_POST['nationality'];
    $studAddress = $_POST['address'];
    $studDisease = $_POST['disease'];
    $studDisable = $_POST['disability'];
    $studStatus = $_POST['status'];

    echo $oldparIC;
    $parIC = $_POST['parentIC'];
    $parName = $_POST['parentName'];
    $parGender = $_POST['parentGender'];
    $parPhone = $_POST['parentPhone'];
    $parJob = $_POST['parentJob'];
    $parSalary = $_POST['parentIncome'];


    // Modify $studLevel based on student level to be insert into db
    if ($studLevel == "Form 4") {
        $studLevel = "4";
    } elseif ($studLevel == "Form 1") {
        $studLevel = "1";
    }

    mysqli_query($con, "UPDATE `parent` SET `PARENT_ID`='$parIC',`PARENT_NAME`='$parName', `PARENT_GENDER`='$parGender', 
        `PARENT_PHONENUM`='$parPhone', `PARENT_JOB`='$parJob', `PARENT_MONTHLY_INCOME`='$parSalary' 
        WHERE `PARENT_ID`='$oldparIC'") or die("Error Occurred parent " . mysqli_error($con));

    mysqli_query($con, "UPDATE `student` SET `STUDENT_NAME`='$studname', `STUDENT_GENDER`='$studGender', 
        `STUDENT_LEVEL`='$studLevel', `STUDENT_DOB`='$studDOB', `STUDENT_POB`='$studPOB', 
        `STUDENT_RELIGION`='$studReligion', `STUDENT_RACE`='$studRace', `STUDENT_NATIONALITY`='$studNationality',
        `STUDENT_ADDRESS`='$studAddress', `STUDENT_DISEASE`='$studDisease', 
        `STUDENT_DISABILITY`='$studDisable', `STUDENT_STATUS`='$studStatus' 
        WHERE `STUDENT_ID`='$id'") or die("Error Occurred student " . mysqli_error($con));

    // Redirect to student_home.php after processing the form data
    header("Location: studentList.php");
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
        <div class='btn'><a class='btn btn-back' href='studentList.php'>Go Back</a></div>
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