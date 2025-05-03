<?php
session_start();

include("../config.php");

if (!isset($_SESSION['validTC'])) {
	header("Location: ../login-logout/loginb.php");
	exit();
}

$id = $_SESSION['validTC'];

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

$query = mysqli_query($con, "SELECT * FROM teacher WHERE TEACHER_ID=$id");

while ($result = mysqli_fetch_assoc($query)) {
	$res_id = $result['TEACHER_ID'];
}

if (isset($_POST['submit'])) {

	$teachname = $_POST['teachername'];
	$gender = $_POST['gender'];
	$dob = $_POST['dob'];
	$address = $_POST['address'];
	$phone = $_POST['phone'];
	$email = $_POST['email'];
	$Status = $_POST['status'];




	mysqli_query($con, "UPDATE `teacher` SET `TEACHER_NAME`='$teachname', `TEACHER_GENDER`='$gender',
	`TEACHER_DOB`='$dob', `TEACHER_ADDRESS`='$address', `TEACHER_PHONENUM`='$phone', `TEACHER_EMAIL`='$email',
	`TEACHER_STATUS`='$Status'WHERE `TEACHER_ID`='$res_id'") or die("Error Occurred student " . mysqli_error($con));



	header("Location: noti/noti_successTCReg.php");
	exit();

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
							<input type="text" id="teacherName" required>
					</p>
					<label>Gender : </label></b>
					<input type="radio" id="male" name="gender" required></b>
					<label for="male">Male</label>
					<input type="radio" id="female" name="gender"  required>
					<label for="female">Female</label><br>
					<br><b>
						<label>Status : </label>
						<input type="radio" id="status" name="status" required>
						<label for="stat">Single</label>
						<input type="radio" id="statusM" name="status" required>
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
						<input type="text" id="parentPhone" name="parentPhone" required>



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