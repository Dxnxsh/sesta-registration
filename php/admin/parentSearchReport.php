<?php
session_start();
include "../header/adminHeader.php";
require_once('../config.php');

// Check admin session
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize admin session ID
$adminId = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');
if (!$adminId) {
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in parentSearchReport.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}

// Handle search with proper sanitization
$searchTerm = '';
$totalRows_rsSearchParent = 0;
$rsSearchParent = null;

if (isset($_POST['searchBox'])) {
    $searchTerm = trim($_POST['searchBox']);
    $searchTerm = SecuritySanitizer::sanitize($searchTerm, 'id', 'PARENT_ID');
    
    if ($searchTerm) {
        // Use prepared statement for search
        $stmt = $con->prepare("SELECT * FROM parent WHERE PARENT_ID LIKE ?");
        $searchPattern = $searchTerm . "%";
        $stmt->bind_param("s", $searchPattern);
        $stmt->execute();
        $rsSearchParent = $stmt->get_result();
        $totalRows_rsSearchParent = $rsSearchParent->num_rows;
        SecuritySanitizer::logSecurityEvent("Admin $adminId searched for parent: $searchTerm", 'INFO');
    } else {
        SecuritySanitizer::logSecurityEvent("Invalid parent search term by admin $adminId", 'MEDIUM');
        // Set empty result for invalid search
        $rsSearchParent = $con->query("SELECT * FROM parent WHERE 1=0");
        $totalRows_rsSearchParent = 0;
    }
} else {
    // Default: show all parents
    $rsSearchParent = $con->query("SELECT * FROM parent");
    $totalRows_rsSearchParent = $rsSearchParent->num_rows;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
    <meta charset="utf-8">
    <title>Parent Report</title>
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
            background-image: linear-gradient(to right, #3a7bd5 0%, #3a6073  51%, #3a7bd5  100%);
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
            width: 60%;
            box-sizing: border-box;
        }

        input[type="submit"] {
            padding: 8px;
            background-color: #1484BD ;
            color: white;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            width: 30%;
        }

        input[type="submit"]:hover {
            background-color: #1C48C7 ;
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
            background-color: #1484BD ;
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
      background-color: #1484BD ;
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
</head>

<body>
    <div class="container">
		<form id="form1" name="form1" method="post">
			<h2>
            <i class="fas fa-chart-bar"></i> FULL REPORT
        </h2>
        <h2 class="h2-ct">
             PARENT
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
                <input name="searchBox" type="text" id="searchBox" placeholder="Search Parent ID" required>
                <input name="submit" type="submit" id="submit" formaction="parentSearchReport.php" value="Search">
            </p>
        </form>

      <form id="form3" name="form3" method="post">
        <p><b>Total Record Found: </b><?php echo htmlspecialchars($totalRows_rsSearchParent, ENT_QUOTES, 'UTF-8') ?> </p>
        <table>
          <tr>
            <th>ID</th>
            <th>NAME</th>
            <th>GENDER</th>
            <th>PHONE NO</th>
            <th>JOB</th>
            <th>MONTHLY INCOME</th>
          </tr>
          <?php 
          if ($rsSearchParent && $totalRows_rsSearchParent > 0) {
              while ($row_rsSearchParent = $rsSearchParent->fetch_assoc()) {
                  // Sanitize all outputs for XSS protection
                  $parentId = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_ID'], 'id');
                  $parentName = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_NAME'], 'name');
                  $parentGender = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_GENDER'], 'gender');
                  $parentPhone = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_PHONENUM'], 'phone');
                  $parentJob = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_JOB'], 'job');
                  $parentIncome = SecuritySanitizer::sanitize($row_rsSearchParent['PARENT_MONTHLY_INCOME'], 'income');
                  
                  echo "<tr class='tr-hover'>
                      <td>" . htmlspecialchars($parentId, ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($parentName, ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($parentGender, ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($parentPhone, ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($parentJob, ENT_QUOTES, 'UTF-8') . "</td>
                      <td>" . htmlspecialchars($parentIncome, ENT_QUOTES, 'UTF-8') . "</td>
                  </tr>";
              }
          } else {
              echo "<tr><td colspan='6'>No parent records found.</td></tr>";
          }
          ?>
        </table>
        <p>&nbsp;</p>
      </form>
       <div class="button-container">
  <button type="button" class="proceed-payment-button" onclick="window.location.href = 'parentFullReport.php';">
    <i class="fas fa-arrow-left"></i> All Parent
  </button>
  </div>
    </div>
</body>
</html>
<?php
mysqli_free_result($rsSearchParent);
?>
