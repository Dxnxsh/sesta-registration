<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}


$id = $_GET['id'];
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

$selectTeachClass = "SELECT * FROM teacher s
LEFT JOIN class c ON s.TEACHER_ID = c.TEACHER_ID
WHERE s.TEACHER_ID = '$id'";
$queryTeachClass = mysqli_query($con, $selectTeachClass);

// Check for errors during the query execution
if (!$queryTeachClass) {
    die('Error in SQL query: ' . mysqli_error($con));
}
// Fetch and process the results
while ($row = mysqli_fetch_assoc($queryTeachClass)) {
    // Process each row of data
    // $row contains the combined data from both "student" and "class" tables
    $names = $row['TEACHER_NAME'];
    $teachgender = $row['TEACHER_GENDER'];
    $teachDOB = $row['TEACHER_DOB'];
    $teachAddress = $row['TEACHER_ADDRESS'];
    $teachStat = $row['TEACHER_STATUS'];
    $teachPhone = $row['TEACHER_PHONENUM'];
    $teachEm = $row['TEACHER_EMAIL'];
}

// Fetch the list of class who are not assigned to any teacher
$selectClassTeacher = "SELECT CLASS_CODE, CLASS_NAME FROM class c
                      WHERE NOT EXISTS (SELECT 1 FROM teacher WHERE c.TEACHER_ID = teacher.TEACHER_ID)";
$queryClassTeacher = mysqli_query($con, $selectClassTeacher);


if (isset($_POST['submit'])) {

    $teachname = $_POST['teacherName'];
    $gender = $_POST['gender'];
    $dob = $_POST['dob'];
    $address = $_POST['address'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $Status = $_POST['status'];


    mysqli_query($con, "UPDATE `teacher` SET `TEACHER_NAME`='$teachname', `TEACHER_GENDER`='$gender',
	`TEACHER_DOB`='$dob', `TEACHER_ADDRESS`='$address', `TEACHER_PHONENUM`='$phone', `TEACHER_EMAIL`='$email',
	`TEACHER_STATUS`='$Status'WHERE `TEACHER_ID`='$id'") or die("Error Occurred student " . mysqli_error($con));

   

    // Redirect to student_home.php after processing the form data
    header("Location: teacherList.php");
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
    <title>Update Teacher details</title>


</head>

<body>
    <div class="container">
        <div class='btn'><a class='btn btn-back' href='teacherList.php'>Go Back</a></div>
        
        <form name="teacherRegister" method="post" id="teacherRegister">
            <h1><img src="../../image/icon/teacher.png" alt="Search Icon" width="50" height="45" class="img-icon">
                Teacher Update Details</h1>
            <div class="container2">
                <div style="display: block;">
                    <h2>Teacher Information :</h2>
                    <p><b>
                            <label for="teacherName">Teacher Name :</label>
                            <input type="text" id="teacherName" value="<?php echo $names ?>" name="teacherName" required>
                    </p>
                    <label>Gender : </label></b>
                    <input type="radio" id="male" name="gender" value="Male" <?php echo ($teachgender == 'Male') ? 'checked' : ''; ?> required></b>
                    <label for="male">Male</label>
                    <input type="radio" id="female" name="gender" value="Female" <?php echo ($teachgender == 'Female') ? 'checked' : ''; ?> required>
                    <label for="female">Female</label><br>
                    <br><b>
                        <label>Status : </label>
                        <input type="radio" id="status" name="status" value="Single" <?php echo ($teachStat == 'Single') ? 'checked' : ''; ?> required>
                        <label for="stat">Single</label>
                        <input type="radio" id="statusM" name="status" value="Married" <?php echo ($teachStat == 'Married') ? 'checked' : ''; ?> required>
                        <label for="stat2">Married</label>
                        <label for="dob">
                            <br><br><b>
                                Date of Birth : </label>
                        <input type="date" id="dob" name="dob" value="<?= date('Y-m-d', strtotime($dobpredict)); ?>"
                            required><br><br>
                        <label for="address">Address :</label>
                        <textarea id="address"
                            name="address"><?php echo isset($teachAddress) ? $teachAddress : ''; ?></textarea>
                        <br>
                        <label for="phone">Phone No. :</label>
                        <input type="text" id="phone" name="phone" maxlength="12" required value="<?php echo $teachPhone ?>">

                        <label for="Email">Email :</label>
                        <input type="text" id="email" name="email" value="<?php echo $teachEm ?>" required></b>

                </div>
            </div>
            <div class="button-container">
                <button type="submit" name="submit" class="btn btn-admin">Save</button>
            </div>
        </form>

    </div>
</body>

</html>