<?php
session_start();

include("../config.php");

if (!isset($_SESSION['valid'])) {
    header("Location: ../login-logout/loginStudent.php");
    exit();
}

$id = $_SESSION['valid'];

$yearPrefix =  substr($id, 0, 2);

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

$query = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID=$id");

while ($result = mysqli_fetch_assoc($query)) {
    $res_id = $result['STUDENT_ID'];
}

if(isset($_POST['submit'])){

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
	
   mysqli_query($con, "INSERT INTO `parent`(`PARENT_ID`, `PARENT_NAME`, `PARENT_GENDER`, `PARENT_PHONENUM`,
			   `PARENT_JOB`, `PARENT_MONTHLY_INCOME`, STUDENT_ID) VALUES('$parIC', '$parName', '$parGender', '$parPhone', '$parJob', '$parSalary', '$id' )") or die("Error Occurred student". mysqli_error($con));
  
			  
  
	mysqli_query($con,"UPDATE `student` SET `STUDENT_NAME`='$studname', `STUDENT_GENDER`='$studGender', `STUDENT_LEVEL`='$studLevel',
	`STUDENT_DOB`='$studDOB', `STUDENT_POB`='$studPOB', `STUDENT_RELIGION`='$studReligion', `STUDENT_RACE`='$studRace', `STUDENT_NATIONALITY`='$studNationality',
	`STUDENT_ADDRESS`='$studAddress', `STUDENT_DISEASE`='$studDisease', `STUDENT_DISABILITY`='$studDisable', `STUDENT_STATUS`='$studStatus', `PARENT_ID`='$parIC' WHERE `STUDENT_ID`='$res_id'") or die("Error Occurred student ". mysqli_error($con));

// Redirect to student_home.php after processing the form data
header("Location: student_home.php");
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
	<script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
	<title>Student Registration</title>
	

</head>

<body>
    <div class="container">
        <form name="studentRegister" method="post" id="studentRegister">
            <h1><img src="../../image/icon/student.png" alt="Search Icon" width="50" height="45" class="img-icon"> Student Registration</h1>
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
							  <input type="date" id="dob" name="dob" value="<?= date('Y-m-d', strtotime($dobpredict)); ?>" required>
						  </p>
						  <p><br>
						    <label for="placeOfBirth">Place of Birth :</label>
						    <input type="text" id="placeOfBirth" name="placeOfBirth" required>
						    
						    <label for="religion"><br>
						      Religion :</label>
			      </p>		<select name="religion" required>
							 <option value="ISLAM" selected>ISLAM</option>
							 <option value="BUDDHA" >BUDDHA</option>
							 <option value="HINDU" >HINDU</option>
							 <option value="CHRISTIAN" >CHRISTIAN</option>
							 <option value="ATHEIST" >ATHEIST</option>
							 </select>
						  <p>

							<label for="race">Race :</label>
							<select name="race" required>
							 <option value="MALAY" selected>MALAY</option>
							 <option value="CHINESE" >CHINESE</option>
							 <option value="INDIAN" >INDIAN</option>
							 <option value="OTHERS" >OTHERS</option>
							 </select>

							<label for="nationality"><br>Nationality :</label>
					</p>	 <select name="nationality" required>
							 <option value="MALAYSIAN" selected>MALAYSIAN</option>
							 <option value="FOREIGNER" >FOREIGNER</option>
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
							<input type="text" id="disability" name="disability" placeholder="Enter if there is a diability">
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
							  <input type="text" id="parentIC" name="parentIC" maxlength="12" pattern="\d{12}" required placeholder=" XXXXXXXXXXXX (12 digits)">
							</p>
							<p>

							  <label for="parent1Phone">Phone No. :</label>
							  <input type="text" id="parentPhone" name="parentPhone" maxlength="11" required placeholder="01XXXXXXXX">

						  </p>
							<p>
							  <label for="parent1Job">Job :</label>
							  <input type="text" id="parentJob" name="parentJob">

							  <label for="parent1Income"><br>
							  Monthly Income :</label>
							  <input type="int" id="parentIncome" name="parentIncome" pattern="\d+" placeholder="RMXXXX.XX">
								
							
						  </b></p>
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