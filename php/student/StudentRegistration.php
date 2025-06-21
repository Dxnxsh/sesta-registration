<?php
session_start();

include("../config.php");

if (!isset($_SESSION['valid'])) {
	header("Location: ../login-logout/login.php");
	exit();
}

$id = SecuritySanitizer::sanitize($_SESSION['valid'], 'id', 'STUDENT_ID');

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

$query = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID='$id'");

while ($result = mysqli_fetch_assoc($query)) {
	$res_id = $result['STUDENT_ID'];
}

if (isset($_POST['submit'])) {
    
    try {
        // Sanitize and validate all student data
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
        $studReligion = SecuritySanitizer::sanitizeForDB($_POST['religion'] ?? '', 'religion', 'STUDENT_RELIGION');
        $studRace = SecuritySanitizer::sanitizeForDB($_POST['race'] ?? '', 'race', 'STUDENT_RACE');

        // Sanitize parent data
        $parIC = SecuritySanitizer::sanitizeForDB($_POST['parentIC'] ?? '', 'id', 'PARENT_ID');
        $parName = SecuritySanitizer::sanitizeForDB($_POST['parentName'] ?? '', 'name', 'PARENT_NAME');
        $parGender = SecuritySanitizer::sanitizeForDB($_POST['parentGender'] ?? '', 'enum', 'PARENT_GENDER');
        $parPhone = SecuritySanitizer::sanitizeForDB($_POST['parentPhone'] ?? '', 'phone', 'PARENT_PHONENUM');
        $parJob = SecuritySanitizer::sanitizeForDB($_POST['parentJob'] ?? '', 'job', 'PARENT_JOB');
        $parSalary = SecuritySanitizer::sanitizeForDB($_POST['parentIncome'] ?? '', 'decimal', 'PARENT_MONTHLY_INCOME');

        // Validate required fields
        $requiredFields = [$studname, $studGender, $studLevel, $studDOB, $parIC, $parName];
        foreach ($requiredFields as $field) {
            if (empty($field)) {
                throw new InvalidArgumentException("Required fields cannot be empty");
            }
        }

        // Check for malicious input
        $allInputs = [$studname, $studGender, $studLevel, $studDOB, $studPOB, $studReligion, 
                     $studRace, $studNationality, $studAddress, $studDisease, $studDisable, 
                     $studStatus, $parIC, $parName, $parGender, $parPhone, $parJob, $parSalary];
        
        foreach ($allInputs as $input) {
            if ($input && detectMaliciousInput($input)) {
                SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                    'field' => 'student_registration',
                    'student_id' => $id
                ]);
                throw new InvalidArgumentException("Invalid input detected");
            }
        }

    } catch (InvalidArgumentException $e) {
        $registration_error = $e->getMessage();
    }

    if (!isset($registration_error)) {
        // Modify $studLevel based on student level to be insert into db
        if ($studLevel == "Form 4") {
            $studLevel = "4";
        } elseif ($studLevel == "Form 1") {
            $studLevel = "1";
        }

        // Check if parent ID already exists using prepared statement
        $parentCheckQuery = "SELECT PARENT_ID FROM parent WHERE PARENT_ID = ?";
        $stmt = mysqli_prepare($con, $parentCheckQuery);
        mysqli_stmt_bind_param($stmt, "s", $parIC);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if (mysqli_num_rows($result) == 0) {
            // If parent ID does not exist, insert the parent data
            $insertParentQuery = "INSERT INTO parent (PARENT_ID, PARENT_NAME, PARENT_GENDER, PARENT_PHONENUM, PARENT_JOB, PARENT_MONTHLY_INCOME) VALUES (?, ?, ?, ?, ?, ?)";
            $stmt2 = mysqli_prepare($con, $insertParentQuery);
            mysqli_stmt_bind_param($stmt2, "sssssd", $parIC, $parName, $parGender, $parPhone, $parJob, $parSalary);
            
            if (!mysqli_stmt_execute($stmt2)) {
                $registration_error = "Error inserting parent data: " . mysqli_error($con);
            }
            mysqli_stmt_close($stmt2);
        }
        mysqli_stmt_close($stmt);

        // Update student record using prepared statement
        if (!isset($registration_error)) {
            $updateStudentQuery = "UPDATE student SET STUDENT_NAME=?, STUDENT_GENDER=?, STUDENT_LEVEL=?, STUDENT_DOB=?, STUDENT_POB=?, STUDENT_RELIGION=?, STUDENT_RACE=?, STUDENT_NATIONALITY=?, STUDENT_ADDRESS=?, STUDENT_DISEASE=?, STUDENT_DISABILITY=?, STUDENT_STATUS=?, PARENT_ID=? WHERE STUDENT_ID=?";
            $stmt3 = mysqli_prepare($con, $updateStudentQuery);
            mysqli_stmt_bind_param($stmt3, "ssssssssssssss", $studname, $studGender, $studLevel, $studDOB, $studPOB, $studReligion, $studRace, $studNationality, $studAddress, $studDisease, $studDisable, $studStatus, $parIC, $res_id);
            
            if (mysqli_stmt_execute($stmt3)) {
                $registration_success = true;
                SecuritySanitizer::logSecurityEvent('student_registration_completed', [
                    'student_id' => $res_id
                ]);
            } else {
                $registration_error = "Error updating student data: " . mysqli_error($con);
                SecuritySanitizer::logSecurityEvent('student_registration_failed', [
                    'student_id' => $res_id,
                    'error' => mysqli_error($con)
                ]);
            }
            mysqli_stmt_close($stmt3);
        }
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<title>Student Registration</title>


</head>

<body>
	<div class="container">
		<form name="studentRegister" method="post" id="studentRegister">
			<h1><img src="../../image/icon/student.png" alt="Search Icon" width="50" height="45" class="img-icon">
				Student Registration</h1>
			<div class="container2">
				<div style="display: block;">
					<h2>Student Information :</h2>
					<p><b>

							<label>Education Level : </label></b>
						<input type="radio" id="form1" name="level" value="Form 1" required>
						<label for="Form1">Form 1</label>
						<input type="radio" id="form4" name="level" value="Form 4" required>
						<label for="Form4">Form 4</label>
						<br><br><b>
							<label for="studentName">Student Name :</label>
							<input type="text" id="studentName" name="studentName" required></b>
					</p>
					<p><b>
							<label>Gender : </label>
							<input type="radio" id="male" name="gender" value="Male" required></b>
						<label for="male">Male</label>
						<input type="radio" id="female" name="gender" value="Female" required>
						<label for="female">Female</label>
						<br>
						<br><b>
							<label>Status : </label>
							<input type="radio" id="status" name="status" value="Single" required></b>
						<label for="stat">Single</label>
						<input type="radio" id="statusM" name="status" value="Married" required>
						<label for="stat2">Married</label>
					</p>
					<p><b>
							<label for="dob">
								<br>
								Date of Birth : </label>
							<input type="date" id="dob" name="dob" value="<?= date('Y-m-d', strtotime($dobpredict)); ?>"
								required>
					</p>
					<p><br>
						<label for="placeOfBirth">Place of Birth :</label>
						<input type="text" id="placeOfBirth" name="placeOfBirth" required>

						<label for="religion"><br>
							Religion :</label>
					</p> <select name="religion" required>
						<option value="ISLAM" selected>ISLAM</option>
						<option value="BUDDHA">BUDDHA</option>
						<option value="HINDU">HINDU</option>
						<option value="CHRISTIAN">CHRISTIAN</option>
						<option value="ATHEIST">ATHEIST</option>
					</select>
					<p>

						<label for="race">Race :</label>
						<select name="race" required>
							<option value="MALAY" selected>MALAY</option>
							<option value="CHINESE">CHINESE</option>
							<option value="INDIAN">INDIAN</option>
							<option value="OTHERS">OTHERS</option>
						</select>

						<label for="nationality"><br>Nationality :</label>
					</p> <select name="nationality" required>
						<option value="MALAYSIAN" selected>MALAYSIAN</option>
						<option value="FOREIGNER">FOREIGNER</option>
					</select>

					<p>

						<label for="address">Address :</label>
						<textarea id="address" name="address" required></textarea>

						<label><br>
							Disease :</label>
						<input type="text" id="disease" name="disease" placeholder="Enter if there is a disease">
					</p>
					<p>

						<label>Disability :</label>
						<input type="text" id="disability" name="disability"
							placeholder="Enter if there is a diability">
					</p>

				</div>
			</div>
			<div class="container2">
				<div style="display: block;">
					<h2>Father/Mother/Guardian Information :</h2>

					<p>
						<label for="parentName">Name :</label>
						<input type="text" id="parentName" name="parentName" required>

						<label><br>
							<br>
							Gender :</label>
						<input type="radio" id="parentMale" name="parentGender" value="Male" required></b>
						<label for="parent1Male">Male</label>
						<input type="radio" id="parentFemale" name="parentGender" value="Female" required>
						<label for="parentFemale">Female</label>

					</p>
					<p>&nbsp;</p>
					<p><b>
							<label for="parent1IC">No.KP (IC Number) :</label>
							<input type="text" id="parentIC" name="parentIC" maxlength="12" pattern="\d{12}" required
								placeholder=" XXXXXXXXXXXX (12 digits)">
					</p>
					<p>

						<label for="parent1Phone">Phone No. :</label>
						<input type="text" id="parentPhone" name="parentPhone" maxlength="11" required
							placeholder="01XXXXXXXX">

					</p>
					<p>
						<label for="parent1Job">Job :</label>
						<input type="text" id="parentJob" name="parentJob">

						<label for="parent1Income"><br>
							Monthly Income :</label>
						<input type="int" id="parentIncome" name="parentIncome" pattern="\d+" placeholder="RMXXXX.XX">


						</b>
					</p>
				</div>
			</div>
			<div class="button-container">
				<button type="reset" class="btn btn-reset">Reset</button>
				<button type="submit" name="submit" class="btn btn-save">Save</button>
			</div>
		</form>

	</div>

	<?php if (isset($registration_success) && $registration_success): ?>
		<script>
			Swal.fire({
				title: 'Registration Successful!',
				text: 'Student registration has been completed successfully.',
				icon: 'success',
				confirmButtonText: 'OK',
				confirmButtonColor: '#28a745'
			}).then((result) => {
				if (result.isConfirmed) {
					window.location.href = 'student_home.php';
				}
			});
		</script>
	<?php endif; ?>

</body>

</html>