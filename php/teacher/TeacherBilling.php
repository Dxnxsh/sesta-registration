<?php 
session_start();
include("../config.php");

if(!isset($_SESSION['validTC'])){
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize teacher session ID
$teacherId = SecuritySanitizer::sanitize($_SESSION['validTC'], 'id', 'TEACHER_ID');
if (!$teacherId) {
    SecuritySanitizer::logSecurityEvent('Invalid teacher session ID in TeacherBilling.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}
?>

<?php include "../header/teacherHeader.php"; ?>
<?php 
// Handle search form submission with proper sanitization
$result = null;

if (isset($_GET['submit'])) {
    $searchOption = isset($_GET['searchOption']) ? SecuritySanitizer::sanitize($_GET['searchOption'], 'name') : '';
    $searchTerm = isset($_GET['searchBox']) ? trim($_GET['searchBox']) : '';
    
    // Validate search option
    $validOptions = ['name', 'ic'];
    if (!in_array($searchOption, $validOptions)) {
        $searchOption = 'name'; // Default to name search
    }
    
    if (!empty($searchTerm)) {
        if ($searchOption == 'name') {
            $searchTerm = SecuritySanitizer::sanitize($searchTerm, 'name');
            if ($searchTerm) {
                $stmt = $con->prepare("SELECT * FROM payment 
                                     INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                                     INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                                     WHERE class.TEACHER_ID = ? AND student.STUDENT_NAME LIKE ? AND STUDENT_NAME IS NOT NULL");
                $searchPattern = "%" . $searchTerm . "%";
                $stmt->bind_param("ss", $teacherId, $searchPattern);
                SecuritySanitizer::logSecurityEvent("Teacher $teacherId searched for student by name: $searchTerm", 'INFO');
            }
        } elseif ($searchOption == 'ic') {
            $searchTerm = SecuritySanitizer::sanitize($searchTerm, 'id', 'STUDENT_ID');
            if ($searchTerm) {
                $stmt = $con->prepare("SELECT * FROM payment 
                                     INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                                     INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                                     WHERE class.TEACHER_ID = ? AND student.STUDENT_ID LIKE ? AND STUDENT_NAME IS NOT NULL");
                $searchPattern = "%" . $searchTerm . "%";
                $stmt->bind_param("ss", $teacherId, $searchPattern);
                SecuritySanitizer::logSecurityEvent("Teacher $teacherId searched for student by ID: $searchTerm", 'INFO');
            }
        }
        
        if (isset($stmt) && $stmt) {
            $stmt->execute();
            $result = $stmt->get_result();
            $stmt->close();
        } else {
            SecuritySanitizer::logSecurityEvent("Invalid search term by teacher $teacherId", 'MEDIUM');
            $result = $con->query("SELECT * FROM payment WHERE 1=0"); // Empty result
        }
    } else {
        // If search box is empty, retrieve all data for teacher's classes
        $stmt = $con->prepare("SELECT * FROM payment 
                             INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                             INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                             WHERE class.TEACHER_ID = ? AND STUDENT_NAME IS NOT NULL");
        $stmt->bind_param("s", $teacherId);
        $stmt->execute();
        $result = $stmt->get_result();
        $stmt->close();
    }
} else {
    // Default query without search but filtered by teacher's classes
    $stmt = $con->prepare("SELECT * FROM payment 
                         INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                         INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                         WHERE class.TEACHER_ID = ? AND STUDENT_NAME IS NOT NULL");
    $stmt->bind_param("s", $teacherId);
    $stmt->execute();
    $result = $stmt->get_result();
    $stmt->close();
}

// Check if the query was successful
if (!$result) {
    SecuritySanitizer::logSecurityEvent("Database query failed in TeacherBilling.php for teacher $teacherId", 'HIGH');
    die("Error in database query");
}

?>
<!doctype html>

<html>
<head>
<meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../../css/AB.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
	<script src="https://code.jquery.com/jquery-3.7.0.js"></script>
	<script src="https://code.jquery.com/jquery-migrate-3.4.1.js"></script> 
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
  
<title>Billings</title>
<style>
     h2 {
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: linear-gradient(to right, #DCE35B 0%, #45B649  51%, #DCE35B  100%);
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      font-size: 50px;
    }
    i {
      margin-right: 10px;
    }
    
    </style>
<script>
	
</script>
</head>

<body style= "background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">

<?php include("../config.php");

// Get student information using prepared statement
$stmt = $con->prepare("SELECT payment.*, student.* FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");
$stmt->execute();
$queryParent = $stmt->get_result();

$studentInfo = [];
while ($resultStud = $queryParent->fetch_assoc()) {
    $studentInfo[$resultStud['STUDENT_ID']] = SecuritySanitizer::sanitize($resultStud['STUDENT_NAME'], 'name');
}
$stmt->close();
?>
	<div class="container">
         <h1>View Billing</h1>
         <form action="" method="get">
        <p>
            <select name="searchOption" id="searchOption">
                <option value="name">Search by Name</option>
                <option value="ic">Search by IC</option>
            </select>
            <input name="searchBox" type="text" id="searchBox" placeholder="Enter search term">
            <input name="submit" type="submit" id="submit" formaction="TeacherBilling.php" value="Search">
        </p>
    </form>
		
	</div>

	<div class="container2">
	  <table width="90%" border="1" cellspacing="5">
	    <tbody>
			<tr class="tr-color">
	        <td width="13%">PAYMENT ID</td>
	        <td width="19%">STUDENT NAME</td>
	        <td width="15%">STUDENT IC</td>
			<td width="17%">PAYMENT TYPE</td>
	        <td width="10%">PAYMENT AMOUNT</td>
	        <td width="10%">PAYMENT STATUS</td>
          </tr>
			<?php
                    // Display the uploaded files and download links
                    if ($result->num_rows > 0) {
                         while ($row = $result->fetch_assoc()) {
                            // Sanitize all outputs for XSS protection
                            $res_paymentID = SecuritySanitizer::sanitize($row['PAYMENT_ID'], 'id');
                            $res_paymentType = SecuritySanitizer::sanitize($row['PAYMENT_TYPE'], 'enum');
                            $res_paymentAmount = SecuritySanitizer::sanitize($row['PAYMENT_AMOUNT'], 'decimal');
                            $res_paymentStatus = SecuritySanitizer::sanitize($row['PAYMENT_STATUS'], 'enum');
                            $res_StudId = SecuritySanitizer::sanitize($row['STUDENT_ID'], 'id');
                            $paymentReceipt = SecuritySanitizer::sanitize($row['PAYMENT_RECEIPT'], 'file_path');
                            
                            $file_path = "../image/" . $paymentReceipt;

							if (array_key_exists($res_StudId, $studentInfo)) {
           					 	$res_StudName = $studentInfo[$res_StudId];
        					} else {
            					$res_StudName = "Unknown"; // You can set a default name if needed
        					}
        
                            // Check if the file path is not empty and the file exists
                            if (!empty($paymentReceipt) && file_exists($file_path)) {
                            ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?php echo htmlspecialchars($res_StudName, ENT_QUOTES, 'UTF-8') ?></td>
	       						<td><?php echo htmlspecialchars($res_StudId, ENT_QUOTES, 'UTF-8') ?></td>
								<td><?php echo htmlspecialchars($res_paymentType, ENT_QUOTES, 'UTF-8') ?></td>
	        					<td>RM <?php echo htmlspecialchars($res_paymentAmount, ENT_QUOTES, 'UTF-8') ?></td>
								<td style="text-align: center; color: <?php 
          							if ($res_paymentStatus == 'UNPAID') {
             							echo 'red';
          							} elseif ($res_paymentStatus == 'PENDING') {
             							echo 'orange';
          							} else {
             							echo 'green';
          							}
            						?>;"><B><?php echo htmlspecialchars($res_paymentStatus, ENT_QUOTES, 'UTF-8') ?></B></td>
                                </tr>
                            <?php
                            } else {
                             ?>
                            <tr>
                                <td><?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?></td>
                                <td><?php echo htmlspecialchars($res_StudName, ENT_QUOTES, 'UTF-8') ?></td>
	       						<td><?php echo htmlspecialchars($res_StudId, ENT_QUOTES, 'UTF-8') ?></td>
								<td><?php echo htmlspecialchars($res_paymentType, ENT_QUOTES, 'UTF-8') ?></td>
	        					<td>RM <?php echo htmlspecialchars($res_paymentAmount, ENT_QUOTES, 'UTF-8') ?></td>
								<td style="text-align: center; color: <?php 
          							if ($res_paymentStatus == 'UNPAID') {
             							echo 'red';
          							} elseif ($res_paymentStatus == 'PENDING') {
             							echo 'orange';
          							} else {
             							echo 'green';
          							}
            						?>;"><B><?php echo htmlspecialchars($res_paymentStatus, ENT_QUOTES, 'UTF-8') ?></B></td>
                            </tr>
                <?php
                }
                }
                } else {
                ?>
                <tr>
                    <td colspan="6">No records found..</td>
                </tr>
                <?php
                }
                ?>
        </tbody>
      </table>
		
	</div>
</body>
</html>
<?php include "../header/footer.php" ?>
<?php
$con->close();
?>