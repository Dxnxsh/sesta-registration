<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

$id = $_GET['id'];
$yearPrefix = substr($id, 0, 2);

// Adjust the year
if ($yearPrefix >= 30 && $yearPrefix <= 99) {
    $year = "19" . $yearPrefix;
} else {
    $year = "20" . $yearPrefix;
}

$monthPrefix = substr($id, 2, 2);
$dayPrefix = substr($id, 4, 2);

// Create predicted DOB
$dobpredict = "$year-$monthPrefix-$dayPrefix";

$selectTeachClass = "SELECT * FROM teacher s
LEFT JOIN class c ON s.TEACHER_ID = c.TEACHER_ID
WHERE s.TEACHER_ID = '$id'";
$queryTeachClass = mysqli_query($con, $selectTeachClass);

if (!$queryTeachClass) {
    die('Error in SQL query: ' . mysqli_error($con));
}

while ($row = mysqli_fetch_assoc($queryTeachClass)) {
    $names = $row['TEACHER_NAME'];
    $teachgender = $row['TEACHER_GENDER'];
    $teachDOB = $row['TEACHER_DOB'];
    $teachAddress = $row['TEACHER_ADDRESS'];
    $teachStat = $row['TEACHER_STATUS'];
    $teachPhone = $row['TEACHER_PHONENUM'];
    $teachEm = $row['TEACHER_EMAIL'];
}

$selectClassTeacher = "SELECT CLASS_CODE, CLASS_NAME FROM class c
WHERE NOT EXISTS (SELECT 1 FROM teacher WHERE c.TEACHER_ID = teacher.TEACHER_ID)";
$queryClassTeacher = mysqli_query($con, $selectClassTeacher);

if (isset($_POST['confirmed']) && $_POST['confirmed'] === '1') {
    $teachname = mysqli_real_escape_string($con, trim($_POST['teacherName']));
    $gender = mysqli_real_escape_string($con, $_POST['gender']);
    $dob = mysqli_real_escape_string($con, $_POST['dob']);
    $address = mysqli_real_escape_string($con, trim($_POST['address']));
    $phone = mysqli_real_escape_string($con, trim($_POST['phone']));
    $email = mysqli_real_escape_string($con, trim($_POST['email']));
    $Status = mysqli_real_escape_string($con, $_POST['status']);
    $id = mysqli_real_escape_string($con, $id);

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
    <link rel="stylesheet" href="../../css/button.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Update Teacher details</title>

    <style>
        /* Radio button styling to keep labels and buttons on same line */
        input[type="radio"] {
            width: auto !important;
            height: auto !important;
            margin-right: 8px;
            vertical-align: middle;
        }

        input[type="radio"] + label {
            display: inline !important;
            margin-right: 20px;
            font-weight: normal;
            vertical-align: middle;
            margin-bottom: 0;
        }

        .radio-group {
            display: flex;
            align-items: center;
            margin: 10px 0;
            flex-wrap: wrap;
        }

        .radio-group > b {
            margin-right: 15px;
        }
    </style>

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
        <form name="teacherRegister" method="post" id="teacherRegister" onsubmit="return confirmUpdate()">
            <input type="hidden" id="confirmed" name="confirmed" value="0">
            <h1>Update Teacher Details</h1>
            <div class="container2">
                <div style="display: block;">
                    <h2>Teacher Information :</h2>

                    <p><b>
                        <label for="teacherName">Teacher Name :</label>
                        <input type="text" id="teacherName" value="<?php echo $names ?>" name="teacherName" required>
                    </b></p>

                    <div class="radio-group">
                        <b><label>Gender : </label></b>
                        <div class="radio-item">
                            <input type="radio" id="male" name="gender" value="Male" <?php echo ($teachgender == 'Male') ? 'checked' : ''; ?> required>
                            <label for="male">Male</label>
                        </div>
                        <div class="radio-item">
                            <input type="radio" id="female" name="gender" value="Female" <?php echo ($teachgender == 'Female') ? 'checked' : ''; ?> required>
                            <label for="female">Female</label>
                        </div>
                    </div>

                    <div class="radio-group">
                        <b><label>Status : </label></b>
                        <input type="radio" id="status" name="status" value="Single" <?php echo ($teachStat == 'Single') ? 'checked' : ''; ?> required>
                        <label for="status">Single</label>
                        <input type="radio" id="statusM" name="status" value="Married" <?php echo ($teachStat == 'Married') ? 'checked' : ''; ?> required>
                        <label for="statusM">Married</label>
                    </div>

                    <b><label for="dob">Date of Birth :</label></b>
                    <input type="date" id="dob" name="dob" value="<?= date('Y-m-d', strtotime($dobpredict)); ?>" required style="margin-left:0;"><br><br>

                    <b><label for="address">Address :</label></b>
                    <textarea id="address" name="address"><?php echo isset($teachAddress) ? $teachAddress : ''; ?></textarea><br>

                    <b><label for="phone">Phone No. :</label></b>
                    <input type="text" id="phone" name="phone" maxlength="12" required value="<?php echo $teachPhone ?>"><br>

                    <b><label for="Email">Email :</label></b>
                    <input type="text" id="email" name="email" value="<?php echo $teachEm ?>" required>
                </div>
            </div>

            <div class="button-container">
                <a class="btn btn-back" href="TeacherList.php">Back</a>
                <button type="submit" name="update_teacher" class="btn btn-admin">Save</button>
            </div>
        </form>
    </div>
</body>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
<script>
    function confirmUpdate() {
        if (document.getElementById('confirmed').value === '1') {
            return true;
        }

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
                document.getElementById('confirmed').value = '1';
                document.getElementById('teacherRegister').submit();
            }
        });

        return false;
    }
</script>
</html>
<?php include "../header/footer.php" ?>