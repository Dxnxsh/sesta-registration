<?php 
	session_start();
   if(!isset($_SESSION['validTC'])){
    header("Location: ../login-logout/login.php");
   }
?>

<?php include "../header/teacherHeader.php"; ?>
<?php 
    include("../config.php");

   // Handle search form submission
   $sql = "";  // Initialize $sql

if (isset($_GET['submit'])) {
    $searchOption = isset($_GET['searchOption']) ? $_GET['searchOption'] : '';
    $searchTerm = isset($_GET['searchBox']) ? $_GET['searchBox'] : '';
    
    if (!empty($searchTerm)) {
        if ($searchOption == 'name') {
            $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID WHERE student.STUDENT_NAME LIKE '%$searchTerm%' AND STUDENT_NAME IS NOT NULL";
        } elseif ($searchOption == 'ic') {
            $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID WHERE student.STUDENT_ID LIKE '%$searchTerm%' AND STUDENT_NAME IS NOT NULL";
        } else {
            // Default query without search
            $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID WHERE student.STUDENT_ID AND STUDENT_NAME IS NOT NULL";
        }
    } else {
        // If search box is empty, retrieve all data
        $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID WHERE student.STUDENT_ID AND STUDENT_NAME IS NOT NULL";
    }
    
    
} else {
    // Default query without search
    $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID WHERE student.STUDENT_ID AND STUDENT_NAME IS NOT NULL";
}


$result = $con->query($sql);

// Check if the query was successful
if (!$result) {
    die("Error in SQL query: " . $con->error);
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

$queryParent = mysqli_query($con, "SELECT payment.*, student.* FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");

while ($resultStud = mysqli_fetch_assoc($queryParent)) {
    $studentInfo[$resultStud['STUDENT_ID']] = $resultStud['STUDENT_NAME'];
}
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
            <input name="submit" type="submit" id="submit" formaction="teacherbilling.php" value="Search">
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
                            $file_path = "../image/" . $row['PAYMENT_RECEIPT'];
                            $res_paymentType = $row['PAYMENT_TYPE'];
							$res_paymentAmount = $row['PAYMENT_AMOUNT'];
							$res_paymentID = $row['PAYMENT_ID'];
							$res_paymentStatus = $row['PAYMENT_STATUS'];
							$res_StudId = $row['STUDENT_ID'];

							if (array_key_exists($res_StudId, $studentInfo)) {
           					 	$res_StudName = $studentInfo[$res_StudId];
        					} else {
            					$res_StudName = "Unknown"; // You can set a default name if needed
        					}
        
                            // Check if the file path is not empty and the file exists
                            if (!empty($row['PAYMENT_RECEIPT']) && file_exists($file_path)) {
                            ?>
                            <tr>
                                <td><?php echo $res_paymentID ?></td>
                                <td><?php echo $res_StudName ?></td>
	       						<td><?php echo $res_StudId ?></td>
								<td><?php echo $res_paymentType ?></td>
	        					<td>RM <?php echo $res_paymentAmount ?></td>
								<td style="text-align: center; color: <?php 
          							if ($res_paymentStatus == 'UNPAID') {
             							echo 'red';
          							} elseif ($res_paymentStatus == 'PENDING') {
             							echo 'orange';
          							} else {
             							echo 'green';
          							}
            						?>;"><B><?php echo $res_paymentStatus ?></B></td>
                                </tr>
                            <?php
                            } else {
                             ?>
                            <tr>
                                <td><?php echo $res_paymentID ?></td>
                                <td><?php echo $res_StudName ?></td>
	       						<td><?php echo $res_StudId ?></td>
								<td><?php echo $res_paymentType ?></td>
	        					<td>RM <?php echo $res_paymentAmount ?></td>
								<td style="text-align: center; color: <?php 
          							if ($res_paymentStatus == 'UNPAID') {
             							echo 'red';
          							} elseif ($res_paymentStatus == 'PENDING') {
             							echo 'orange';
          							} else {
             							echo 'green';
          							}
            						?>;"><B><?php echo $res_paymentStatus ?></B></td>
                            </tr>
                <?php
                }
                }
                } else {
                ?>
                <tr>
                    <td colspan="2">No files uploaded yet.</td>
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