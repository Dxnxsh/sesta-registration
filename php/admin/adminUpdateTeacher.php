<?php
session_start();
include("../config.php");

if (!isset($_SESSION['validAD'])) {
    SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Admin update teacher access without valid session');
    header("Location: ../login-logout/login.php");
    exit();
}

if (!isset($_SESSION['adminID'])) {
    SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Admin update teacher access without valid session');
    header("Location: ../login-logout/login.php");
    exit();
}

if (!isset($_GET['id'])) {
    SecuritySanitizer::logSecurityEvent('invalid_access', 'Admin update teacher accessed without teacher ID');
    header("Location: TeacherList.php");
    exit();
}

$id = SecuritySanitizer::sanitize($_GET['id'], 'id');
if (empty($id)) {
    SecuritySanitizer::logSecurityEvent('invalid_input', 'Invalid teacher ID for update: ' . $_GET['id']);
    header("Location: TeacherList.php");
    exit();
}

// Initialize variables to prevent undefined variable warnings
$names = $teachgender = $teachDOB = $teachAddress = $teachStat = $teachPhone = $teachEm = '';

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
WHERE s.TEACHER_ID = ?";

$stmt = mysqli_prepare($con, $selectTeachClass);
if (!$stmt) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare teacher update query: ' . mysqli_error($con));
    die('Database error occurred');
}

mysqli_stmt_bind_param($stmt, "s", $id);
mysqli_stmt_execute($stmt);
$queryTeachClass = mysqli_stmt_get_result($stmt);

// Check for errors during the query execution
if (!$queryTeachClass) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Teacher update query failed: ' . mysqli_error($con));
    die('Error in SQL query: ' . mysqli_error($con));
}

// Fetch and process the results
if ($row = mysqli_fetch_assoc($queryTeachClass)) {
    // Process each row of data
    // $row contains the combined data from both "teacher" and "class" tables
    $names = $row['TEACHER_NAME'];
    $teachgender = $row['TEACHER_GENDER'];
    $teachDOB = $row['TEACHER_DOB'];
    $teachAddress = $row['TEACHER_ADDRESS'];
    $teachStat = $row['TEACHER_STATUS'];
    $teachPhone = $row['TEACHER_PHONENUM'];
    $teachEm = $row['TEACHER_EMAIL'];
} else {
    SecuritySanitizer::logSecurityEvent('invalid_access', 'Teacher ID not found for update: ' . $id);
    header("Location: TeacherList.php");
    exit();
}

mysqli_stmt_close($stmt);

// Fetch the list of class who are not assigned to any teacher
$selectClassTeacher = "SELECT CLASS_CODE, CLASS_NAME FROM class c
                      WHERE NOT EXISTS (SELECT 1 FROM teacher WHERE c.TEACHER_ID = teacher.TEACHER_ID)";
$queryClassTeacher = mysqli_query($con, $selectClassTeacher);


if (isset($_POST['confirmed']) && $_POST['confirmed'] === '1') {
    // Sanitize and validate input data
    $teachname = mysqli_real_escape_string($con, trim($_POST['teacherName']));
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $Status = mysqli_real_escape_string($con, $_POST['status']);
    $id = mysqli_real_escape_string($con, $id);

    // Validate required fields
    if (empty($teachname) || empty($gender) || empty($dob) || empty($phone) || empty($email) || empty($Status)) {
        $_SESSION['message'] = array('type' => 'error', 'text' => 'Please fill in all required fields.');
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
        
        $result = mysqli_query($con, $updateQuery);

        if ($result) {
            $affected_rows = mysqli_affected_rows($con);
            
            if ($affected_rows > 0) {
                $_SESSION['message'] = array(
                    'type' => 'success', 
                    'text' => 'Teacher information updated successfully.',
                    'redirect' => 'TeacherList.php'
                );
            } else {
                $_SESSION['message'] = array('type' => 'info', 'text' => 'No changes were made to the teacher information.');
            }
        } else {
            $_SESSION['message'] = array('type' => 'error', 'text' => 'An error occurred while updating teacher information. Please try again.');
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
    <?php if (isset($_SESSION['message'])): ?>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            Swal.fire({
                icon: '<?php echo $_SESSION['message']['type']; ?>',
                title: '<?php echo ucfirst($_SESSION['message']['type']); ?>',
                text: '<?php echo $_SESSION['message']['text']; ?>',
                confirmButtonText: 'OK'
            }).then((result) => {
                <?php if (isset($_SESSION['message']['redirect'])): ?>
                if (result.isConfirmed) {
                    window.location.href = '<?php echo $_SESSION['message']['redirect']; ?>';
                }
                <?php endif; ?>
            });
        });
    </script>
    <?php unset($_SESSION['message']); endif; ?>
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