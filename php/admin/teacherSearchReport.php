<?php
  session_start();
  include "../header/adminHeader.php" ?>
<?php require_once('../config.php'); 

if (!function_exists("GetSQLValueString")) {
function GetSQLValueString($theValue, $theType, $theDefinedValue = "", $theNotDefinedValue = "") 
{
  if (PHP_VERSION < 6) {
    $theValue = get_magic_quotes_gpc() ? stripslashes($theValue) : $theValue;
  }

  $theValue = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($theValue) : mysql_escape_string($theValue);

  switch ($theType) {
    case "text":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;    
    case "long":
    case "int":
      $theValue = ($theValue != "") ? intval($theValue) : "NULL";
      break;
    case "double":
      $theValue = ($theValue != "") ? doubleval($theValue) : "NULL";
      break;
    case "date":
      $theValue = ($theValue != "") ? "'" . $theValue . "'" : "NULL";
      break;
    case "defined":
      $theValue = ($theValue != "") ? $theDefinedValue : $theNotDefinedValue;
      break;
  }
  return $theValue;
}
}

$colname_rsSearchTeacher = "-1";
if (isset($_POST['searchBox'])) {
    $colname_rsSearchTeacher = $_POST['searchBox'];
}

$query_rsSearchTeacher = sprintf("SELECT * FROM teacher WHERE TEACHER_ID LIKE %s", GetSQLValueString($colname_rsSearchTeacher . "%", "text"));
$rsSearchTeacher = mysqli_query($con, $query_rsSearchTeacher) or die(mysqli_error($con));
$row_rsSearchTeacher = mysqli_fetch_assoc($rsSearchTeacher);
$totalRows_rsSearchTeacher = mysqli_num_rows($rsSearchTeacher);
?>
<!doctype html>
<html>
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
<meta charset="utf-8">
		<style>
		body {
            background-image: url("../../image/bg5.jpeg");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: Arial, sans-serif;
            margin: 0;
            padding-top: 90px;
            padding-left: 20px;
            padding-right: 20px;
        }

        .container {
            background-color: white;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin: auto;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(to right, #7b4397 0%, #dc2430  51%, #7b4397  100%);
            font-size: 40px;
            color: white;
            text-align: center;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

         .h2-ct {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(to right, #c21500 0%, #ffc500  51%, #c21500  100%);
            font-size: 30px;
            color: white;
            text-align: center;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }
         
        i {
            margin-right: 10px;
        }
        .button-container {
            display: flex;
            justify-content: left; /* Center the buttons horizontally */
            align-items: left; /* Center the buttons vertically */
        }

        .nav-links {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            width: 100%;
			padding: 10px;
            border: 1px solid #4CAF50;
            transition: background-color 0.3s;
			text-align: center;
        }

        .nav-links a.student {
            background-color: #4CAF50;
        }

        .nav-links a.parent {
            background-color: #008CBA;
        }

        .nav-links a.teacher {
            background-color: #FF6600;
        }

        .nav-links a.student:hover {
            background-color: #45a049;
        }
		.nav-links a.parent:hover {
            background-color: #0098E2;
        }
		.nav-links a.teacher:hover {
            background-color: #E05900;
        }

        #form2 {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-top: 20px;
        }

        input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 4px;
            width: 65%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 8px;
            background-color: #F57A14 ;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 30%;
        }

        input[type="submit"]:hover {
            background-color: #C7551C ;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
            background-color: #fff;
            box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
        }

        table, th, td {
            border: 1px solid #ccc;
        }

        th, td {
            padding: 12px;
            text-align: center;
        }

        th {
            background-color: #F57A14 ;
            color: white;
        }
		.tr-hover:hover {
    		background-color: #f5f5f5;
		}
     /* Button styling */
    .proceed-payment-button {
      all: unset;
      width: 100px;
      height: 30px;
      font-size: 16px;
      color: #f0f0f0;
      cursor: pointer;
      z-index: 1;
      padding: 10px 20px;
      display: flex;
      align-items: center;
      justify-content: center;
      white-space: nowrap;
      user-select: none;
      -webkit-user-select: none;
      touch-action: manipulation;
      position: relative;
      margin: 20px; /* Center the button horizontally within its container */
    }

    .proceed-payment-button::after,
    .proceed-payment-button::before {
      content: '';
      position: absolute;
      bottom: 0;
      right: 0;
      z-index: -99999;
      transition: all .4s;
    }

    .proceed-payment-button::before {
      transform: translate(0%, 0%);
      width: 100%;
      height: 100%;
      background: #233858;
      border-radius: 10px;
      background-color: #F57A14 ;
    }

    .proceed-payment-button::after {
      transform: translate(10px, 10px);
      width: 35px;
      height: 35px;
      background: #ffffff15;
      backdrop-filter: blur(5px);
      -webkit-backdrop-filter: blur(5px);
      border-radius: 50px;
    }

    .proceed-payment-button:hover::before {
      transform: translate(5%, 20%);
      width: 110%;
      height: 110%;
    }

    .proceed-payment-button:hover::after {
      border-radius: 10px;
      transform: translate(0, 0);
      width: 100%;
      height: 100%;
    }

    .proceed-payment-button:active::after {
      transition: 0s;
      transform: translate(0, 5%);
    }
	</style>
<title>Teacher Report</title>
</head>
<body>
	<div class="container">
		<form id="form1" name="form1" method="post">
				<h2>
            <i class="fas fa-chart-bar"></i> FULL REPORT
        </h2>
        <h2 class="h2-ct">
             TEACHER
        </h2>
			<div class="nav-links">
				<a href="studentFullReport.php" class="student"><b>STUDENT</b></a>
				<a href="parentFullReport.php" class="parent"><b>PARENT</b></a>
				<a href="teacherFullReport.php" class="teacher"><b>TEACHER</b></a>
			</div>
		</form>
	<form id="form2" name="form2" method="post">
	  <p style="font-size: 25px;"><b></b></p>
	  <p>
		<input name="searchBox" type="text" id="searchBox" placeholder="Search Teacher ID" required>
		<input name="submit" type="submit" id="submit" formaction="teacherSearchReport.php" value="Search">
	  </p>
	</form>
  <form id="form3" name="form3" method="post">
    <p><b>Total Record Found: </b><?php echo $totalRows_rsSearchTeacher ?> </p>
    <table>
      <tbody>
        <tr>
          <th>ID</th>
          <th>NAME</th>
          <th>GENDER</th>
          <th>DOB</th>
          <th>ADDRESS</th>
          <th>PHONE NO.</th>
          <th>EMAIL</th>
          <th>STATUS</th>
          <th>USERNAME</th>
          <th>PASSWORD</th>
        </tr>
		  <?php do { ?>
        <tr class="tr-hover">
          <td><?php echo $row_rsSearchTeacher['TEACHER_ID']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_NAME']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_GENDER']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_DOB']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_ADDRESS']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_PHONENUM']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_EMAIL']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_STATUS']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_USERNAME']; ?></td>
          <td><?php echo $row_rsSearchTeacher['TEACHER_PWD']; ?></td>
        </tr>
        <?php } while ($row_rsSearchTeacher = mysqli_fetch_assoc($rsSearchTeacher)); ?>
      </tbody>
    </table>
	  <p>&nbsp;    </p>
	   <div class="button-container">
  <button type="button" class="proceed-payment-button" onclick="window.location.href = 'teacherFullReport.php';">
    <i class="fas fa-arrow-left"></i> All Teacher
  </button>
  </div>
    </div>
</form>
  <p>&nbsp;</p>
</body>
</html>
<?php
mysqli_free_result($rsSearchTeacher);
?>
