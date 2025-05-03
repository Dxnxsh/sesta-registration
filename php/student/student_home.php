
<?php 
   session_start();

   include("../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
   }
?>
<?php include "../header/studentHeader.php" ?>
<!doctype html>

<html>
<head>
<meta charset="utf-8">
<title>Student Homepage</title>
	
	<link rel="stylesheet" href="../../css/student_homeStyle.css"/>
</head>

<body>
<?php include("../config.php"); 
            
            $id = $_SESSION['valid'];
            $query = mysqli_query($con,"SELECT*FROM student WHERE STUDENT_ID=$id");
            $queryClass  = mysqli_query($con,"SELECT student.*, class.* FROM student INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE
            WHERE student.STUDENT_ID = $id");

            // Initialize $res_Class with a default value
    		$res_Class = '';
            while($result = mysqli_fetch_assoc($query)){
                $res_IC = $result['STUDENT_ID'];
				$res_Name = $result['STUDENT_NAME'];
                $res_DOB = $result['STUDENT_DOB'];
				
            }

			while($result = mysqli_fetch_assoc($queryClass)){
                $res_Class = $result['CLASS_NAME'];
				
            }

			if($res_Class==''){
				$res_Class='not assigned';
			}

			
          
 ?>

<div class="header">
      <h1 style="color: #FFFFFF">Student Profile</h1>
	</div>
	<div class="box">
		<div class="container">
			<table width="736" border="0">
			  <tbody >
				<tr>
				<td class="line" style="color: black">STUDENT IC</td>
			  <td class="line" style="color: #727268"><?php echo $res_IC ?></td>
			</tr>
				<tr>
				  <td colspan="2"><p>&nbsp;</p></td>
				</tr>
				<tr>
				<td class="line" style="color: black">NAME</td>
			  <td class="line" style="color: #727268"><?php echo $res_Name ?></td>
			</tr>
				<tr>
				  <td colspan="2"><p>&nbsp;</p></td>
				</tr>
				<tr>
				<td class="line" style="color: black">DATE OF BIRTH</td>
			  <td class="line" style="color: #727268"><?php echo $res_DOB ?></td>
		</tr>
				<tr>
				  <td colspan="2"><p>&nbsp;</p></td>
				</tr>
				<tr>
				<td class="line" style="color: black">CLASS</td>
			  <td class="line" style="color: #727268"><?php echo $res_Class ?></td>
			</tr>
			</table>
		</div>
		<div class="button">
			<ul>
				<li>
				<a href="../../html/subject.html">
						<img src="../../image/icon/subjectOutline.png" alt="Subject Outline">
				</a>
				</li>
				<li>
				<a href="billing/billing.php">
						<img src="../../image/icon/studentBill.png" alt="Billing">
			</a>
				</li>
				<li>
				<a href="studentCard.php">
						<img src="../../image/icon/studentCard.png" alt="Student Card">
			</a>
				</li>
			</ul>
		</div>
	</div>
</body>
</html>
<?php include "../header/footer.php" ?>

