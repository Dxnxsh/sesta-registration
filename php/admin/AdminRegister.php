<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

$selectadmin = "SELECT * FROM admin";
$queryadmin = mysqli_query($con, $selectClassCategory);
?>

<?php include "../header/adminHeader.php" ?>
<!doctype html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<link rel="stylesheet" type="text/css" href="../style/SRegis.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />

	<title>Registration Form</title>
</head>

<body>
    <div class="container">
        <form action="<?php echo $editFormAction; ?>" name="teacherRegister" method="POST" id="teacherRegister">
            <h1><img src="../image/admin.png" alt="Search Icon" width="50" height="45"> Admin Registration</h1>
            <div class="container2">
                <div style="display: block;">
					<h2>Admin Information:</h2><b>
					<p>
						<label for="studentName">Admin ID :</label>
						<input type="text" id="id" name="id" required>
					</p>
					<p>
						<label for="studentName">Username :</label>
						<input type="text" id="name" name="name" required>
					</p>
							<label for="pwd">Password :</label>
							  <input type="text" id="pwd" name="pwd" required></b>
					
				</div>
			</div>
            <input type="hidden" name="MM_insert" value="teacherRegister">
            <div class="button-container">
            <button type="reset" class="btn btn-reset">Reset</button>
            <button type="submit" name="submit" class="btn btn-save">Save</button>
        	</div>
			</form>
		
	</div>
</body>
</html>