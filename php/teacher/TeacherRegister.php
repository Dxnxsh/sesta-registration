<?php
session_start();

include("../config.php");

if (!isset($_SESSION['validTC'])) {
	header("Location: ../login-logout/login.php");
	exit();
}

$id = SecuritySanitizer::sanitize($_SESSION['validTC'], 'id', 'TEACHER_ID');

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

$query = mysqli_query($con, "SELECT * FROM teacher WHERE TEACHER_ID='$id'");

while ($result = mysqli_fetch_assoc($query)) {
	$res_id = $result['TEACHER_ID'];
}

if (isset($_POST['submit'])) {

    try {
        // Sanitize and validate all teacher data
        $teachname = SecuritySanitizer::sanitizeForDB($_POST['teacherName'] ?? '', 'name', 'TEACHER_NAME');
        $gender = SecuritySanitizer::sanitizeForDB($_POST['gender'] ?? '', 'enum', 'TEACHER_GENDER');
        $dob = SecuritySanitizer::sanitizeForDB($_POST['dob'] ?? '', 'date', 'TEACHER_DOB');
        $address = SecuritySanitizer::sanitizeForDB($_POST['address'] ?? '', 'address', 'TEACHER_ADDRESS');
        $phone = SecuritySanitizer::sanitizeForDB($_POST['phone'] ?? '', 'phone', 'TEACHER_PHONENUM');
        $email = SecuritySanitizer::sanitizeForDB($_POST['email'] ?? '', 'email', 'TEACHER_EMAIL');
        $Status = SecuritySanitizer::sanitizeForDB($_POST['status'] ?? '', 'status', 'TEACHER_STATUS');

        // Validate required fields
        if (empty($teachname) || empty($gender) || empty($email)) {
            throw new InvalidArgumentException("Required fields cannot be empty");
        }

        // Check for malicious input
        $allInputs = [$teachname, $gender, $dob, $address, $phone, $email, $Status];
        foreach ($allInputs as $input) {
            if ($input && detectMaliciousInput($input)) {
                SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                    'field' => 'teacher_registration',
                    'teacher_id' => $res_id
                ]);
                throw new InvalidArgumentException("Invalid input detected");
            }
        }

        // Update teacher record using prepared statement
        $updateQuery = "UPDATE teacher SET TEACHER_NAME=?, TEACHER_GENDER=?, TEACHER_DOB=?, TEACHER_ADDRESS=?, TEACHER_PHONENUM=?, TEACHER_EMAIL=?, TEACHER_STATUS=? WHERE TEACHER_ID=?";
        $stmt = mysqli_prepare($con, $updateQuery);
        
        if (!$stmt) {
            throw new Exception("Database preparation failed");
        }
        
        mysqli_stmt_bind_param($stmt, "ssssssss", $teachname, $gender, $dob, $address, $phone, $email, $Status, $res_id);
        
        if (mysqli_stmt_execute($stmt)) {
            SecuritySanitizer::logSecurityEvent('teacher_registration_completed', [
                'teacher_id' => $res_id
            ]);
            mysqli_stmt_close($stmt);
            header("Location: noti/noti_successTCReg.php");
            exit();
        } else {
            SecuritySanitizer::logSecurityEvent('teacher_registration_failed', [
                'teacher_id' => $res_id,
                'error' => mysqli_error($con)
            ]);
            mysqli_stmt_close($stmt);
            throw new Exception("Database update failed");
        }
        
    } catch (InvalidArgumentException $e) {
        $registration_error = $e->getMessage();
    } catch (Exception $e) {
        $registration_error = "Registration failed: " . $e->getMessage();
    }
}


?>
<!doctype html>
<html>

<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../../css/SRegis.css" />
	<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />

	<title>Registration Form</title>
</head>

<body class="body-teacher">
	<div class="container">
		<form name="teacherRegister" method="post" id="teacherRegister">
			<h1><img src="../../image/icon/teacher.png" alt="Search Icon" width="60" height="50"> Teacher Registration
			</h1>
			<div class="container2">
				<div style="display: block;">
					<h2>Teacher Information :</h2>
					<p><b>
							<label for="teachername">Teacher Name :</label>
							<input type="text" id="teacherName" name="teacherName" required>
					</p>
					<label>Gender : </label></b>
					<input type="radio" id="male" name="gender" value="Male" required></b>
					<label for="male">Male</label>
					<input type="radio" id="female" name="gender" value="Female" required>
					<label for="female">Female</label><br>
					<br><b>
						<label>Status : </label>
						<input type="radio" id="status" name="status" value="Single" required>
						<label for="stat">Single</label>
						<input type="radio" id="statusM" name="status" value="Married" required>
						<label for="stat2">Married</label>
						<label for="dob">
							<br><br><b>
								Date of Birth : </label>
						<input type="date" id="dob" name="dob" value="<?= date('Y-m-d', strtotime($dobpredict)); ?>"
							required><br><br>
						<label for="address">Address :</label>
						<textarea id="address"
							name="address"> </textarea>
						<br>
						<label for="phone">Phone No. :</label>
						<input type="text" id="phone" name="phone" required>
						<label for="phone">Email :</label>
						<input type="email" id="email" name="email" required>



				</div>
			</div>
			<div class="button-container">
				<button type="reset" class="btn btn-reset">Reset</button>
				<button type="submit" name="submit" class="btn btn-save">Save</button>
			</div>
		</form>

	</div>
</body>

</html>