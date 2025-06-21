<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Add debug output at the very top
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    error_log("POST request received");
    error_log("POST data: " . print_r($_POST, true));
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


if (isset($_POST['update_teacher']) && isset($_POST['confirmed']) && $_POST['confirmed'] === '1') {
    error_log("Form submitted - update_teacher parameter received and confirmed");
    
    // Sanitize and validate input data
    $teachname = mysqli_real_escape_string($con, trim($_POST['teacherName']));
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $Status = mysqli_real_escape_string($con, $_POST['status']);
    $id = mysqli_real_escape_string($con, $id);
    
    error_log("Data received: Name=$teachname, ID=$id");

    // Validate required fields
    if (empty($teachname) || empty($gender) || empty($dob) || empty($phone) || empty($email) || empty($Status)) {
        error_log("Validation failed - missing required fields");
        echo "<script>
            Swal.fire({
                icon: 'error',
                title: 'Validation Error',
                text: 'Please fill in all required fields.',
                confirmButtonText: 'OK'
            });
        </script>";
    } else {
        $updateQuery = "UPDATE `teacher` SET 
            `TEACHER_NAME`='$teachname', 
            `TEACHER_GENDER`='$gender',
            `TEACHER_DOB`='$dob', 
            `TEACHER_ADDRESS`='$address', 
            `TEACHER_PHONENUM`='$phone', 
            `TEACHER_EMAIL`='$email',
            `TEACHER_STATUS`='$Status' 
            WHERE `TEACHER_ID`='$id'";
        
        error_log("Executing query: $updateQuery");
        $result = mysqli_query($con, $updateQuery);

        if ($result) {
            $affected_rows = mysqli_affected_rows($con);
            error_log("Query executed successfully. Affected rows: $affected_rows");
            
            if ($affected_rows > 0) {
                error_log("Update successful - redirecting to TeacherList.php");
                // Try both approaches
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: 'Teacher information updated successfully.',
                            confirmButtonText: 'OK'
                        }).then(() => {
                            window.location.href = 'TeacherList.php';
                        });
                    });
                </script>";
                // Backup redirect
                echo "<meta http-equiv='refresh' content='2;url=TeacherList.php'>";
            } else {
                error_log("No rows affected - no changes made");
                echo "<script>
                    document.addEventListener('DOMContentLoaded', function() {
                        Swal.fire({
                            icon: 'info',
                            title: 'No Changes',
                            text: 'No changes were made to the teacher information.',
                            confirmButtonText: 'OK'
                        });
                    });
                </script>";
            }
        } else {
            $error = mysqli_error($con);
            error_log("MySQL Error in adminUpdateTeacher.php: $error");

            echo "<script>
                document.addEventListener('DOMContentLoaded', function() {
                    Swal.fire({
                        icon: 'error',
                        title: 'Update Failed',
                        text: 'An error occurred while updating teacher information. Please try again.',
                        confirmButtonText: 'OK'
                    });
                });
            </script>";
        }
    }
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
        <div class='btn'><a class='btn btn-back' href='TeacherList.php'>Go Back</a></div>
        <form name="teacherRegister" method="post" id="teacherRegister" onsubmit="return confirmUpdate()">
            <input type="hidden" id="confirmed" name="confirmed" value="0">
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
                <button type="submit" name="update_teacher" class="btn btn-admin">Save</button>
            </div>
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
            <script>
                function confirmUpdate() {
                    // Check if already confirmed
                    if (document.getElementById('confirmed').value === '1') {
                        return true; // Allow form submission
                    }
                    
                    // Show confirmation dialog
                    Swal.fire({
                        title: 'Confirm Update',
                        text: 'Are you sure you want to update this teacher\'s information?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: '#3085d6',
                        cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, update it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            // Set confirmed flag and submit
                            document.getElementById('confirmed').value = '1';
                            document.getElementById('teacherRegister').submit();
                        }
                    });
                    return false; // Prevent initial submission
                }
            </script>
        </form>
    </div>
</body>

</html>