<?php 
	session_start();
   include("../config.php");
   if(!isset($_SESSION['validTC'])){
    header("Location: ../login-logout/LoginTeacher.php");
   }
?>

<?php include "../header/teacherHeader.php" ?>
<!doctype html>

<html>
<head>
<meta charset="utf-8">
<title>Teacher Homepage</title>
	
<link rel="stylesheet" href="../../css/teacher_homeStyle.css"/>
</head>

<body>
   <?php $result = mysqli_query($con, "SELECT * FROM teacher WHERE TEACHER_ID='{$_SESSION['validTC']}'") or die("Select Error");
         $row_rsTeach = mysqli_fetch_assoc($result);
		 ?>


	<div class="header">
      <h1 style="color: #FFFFFF">Teacher Profile</h1>
	</div>
	<div class="box">
	<div class="container">
		<table width="736" border="0">
			<tr>
			<td class="line" style="color: black">TEACHER IC</td>
		 	<td class="line" style="color: #727268"><?php echo $row_rsTeach['TEACHER_ID']; ?></td>
			</tr>
			<tr>
			  <td colspan="2"><p>&nbsp;</p></td>
			</tr>
			<tr>
			  <td class="line" style="color: black">	NAME</td>
			  <td class="line" style="color: #727268"> <?php echo $row_rsTeach['TEACHER_NAME']; ?></td>
			</tr>
			<tr>
			  <td colspan="2"><p>&nbsp;</p></td>
			</tr>
			<tr>
			  <td class="line" style="color: black">DATE OF BIRTH</td>
			  <td class="line" style="color: #727268"><?php echo $row_rsTeach['TEACHER_DOB']; ?></td>
			</tr>
			<tr>
			  <td colspan="2"><p>&nbsp;</p></td>
			</tr>
			<tr>
			  <td class="line" style="color: black">EMAIL</td>
			  <td class="line" style="color: #727268"><?php echo $row_rsTeach['TEACHER_EMAIL']; ?></td>
			</tr>
			<tr>
			  <td colspan="2">&nbsp;</td>
			</tr>
			<tr>
			  <td class="line" style="color: black"><p>PHONE NO.</p></td>
			  <td class="line" style="color: #727268"><?php echo $row_rsTeach['TEACHER_PHONENUM']; ?></td>
			</tr>
		</table>
	</div>
	<div class="button">
	<ul>
				<li>
				<a href="TeacherClass.php">
						<img src="../../image/icon/AssignedClass.png" alt="Assigned Class">
					  </a>
				</li>
                <div class="buttonMid">
                    <li>
                        <a href="studentList.php">
						<img src="../../image/icon/updateStudent.png" alt="View Student">
                        </a>
                    </li>  
                </div>
				<li>
					<a href="teacherbilling.php">
						<img src="../../image/icon/studentBill.png" alt="Student Bill">
					  </a>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>
<?php include "../header/footer.php" ?>