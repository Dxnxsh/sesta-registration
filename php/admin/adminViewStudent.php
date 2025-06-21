<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
  SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Admin view student access without valid session');
  header("Location: ../login-logout/login.php");
  exit();
}

// Initialize variables
$studname = $id = $lvl = $cCode = $cName = $gender = $dob = $pob = $email = $address = '';

if (isset($_GET['id'])) {
  $stud_id = SecuritySanitizer::sanitize($_GET['id'], 'id', 'STUDENT_ID');
  
  if (empty($stud_id)) {
    SecuritySanitizer::logSecurityEvent('invalid_input', 'Invalid student ID in view: ' . $_GET['id']);
    header("Location: StudentList.php");
    exit();
  }
  
  $selectClassStudent = "SELECT * FROM student s
  INNER JOIN class c ON s.CLASS_CODE = c.CLASS_CODE
  WHERE s.STUDENT_ID = ?";
  
  $stmt = mysqli_prepare($con, $selectClassStudent);
  if (!$stmt) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare student view query: ' . mysqli_error($con));
    die('Database error occurred');
  }
  
  mysqli_stmt_bind_param($stmt, "s", $stud_id);
  mysqli_stmt_execute($stmt);
  $queryClassStudent = mysqli_stmt_get_result($stmt);

  if (!$queryClassStudent) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Student view query failed: ' . mysqli_error($con));
    die('Error: ' . mysqli_error($con));
  }

  // Fetch and process the results
  if ($row = mysqli_fetch_assoc($queryClassStudent)) {
    // Process each row of data
    // $row contains the combined data from both "student" and "class" tables
    $studname = $row['STUDENT_NAME'];
    $id = $row['STUDENT_ID'];
    $lvl = $row['STUDENT_LEVEL'];
    $cCode = $row['CLASS_CODE'];
    $cName = $row['CLASS_NAME'];
    $gender = $row['STUDENT_GENDER'];
    $dob = $row['STUDENT_DOB'];
    $pob = $row['STUDENT_POB'];
    $email = $row['STUDENT_EMAIL'];
    $address = $row['STUDENT_ADDRESS'];
  } else {
    SecuritySanitizer::logSecurityEvent('invalid_access', 'Student ID not found: ' . $stud_id);
  }
  
  mysqli_stmt_close($stmt);
} else {
  SecuritySanitizer::logSecurityEvent('invalid_access', 'Admin view student accessed without student ID');
}
?>
<?php include "../header/adminHeader.php" ?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>View Student</title>
  <link href="../../css/AVS.css" rel="stylesheet" type="text/css">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
  <script>
    function goBack() {
        window.location.href = 'adminClass.php';
    }
  </script>
</head>

<body
  style="background-image: url(../../image/admin.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
  <div class="container">
    <h2><i class="bx bx-book"></i> VIEW STUDENT</h2>
    <form id="form1" name="form1" method="post" class="item-mid">
      <div class="table-wrapper">
        <table width="50%" border="1" cellspacing="5" class="fl-table">
          <tbody>
          <tr>
              <th>Name </th>
              <td>
              <?php echo htmlspecialchars(!empty($studname) ? $studname : 'Not available'); ?>
              </td>
            </tr>  
          <tr>
              <th>ID</th>
              <td>
              <?php echo htmlspecialchars(!empty($stud_id) ? $stud_id : 'Not available'); ?>
              </td>
            </tr>  
          <tr>
              <th>Gender </th>
              <td>
              <?php echo htmlspecialchars(!empty($gender) ? $gender : 'Not available'); ?>
              </td>
            </tr>
            <tr>
              <th> Class </th>
              <td>
              <?php echo htmlspecialchars(!empty($cCode) && !empty($cName) ? $cCode . ' - ' . $cName : 'Not available'); ?>
      </td>
            </tr>
            <tr>
              <th> Study Level </th>
              <td>
              <?php echo htmlspecialchars(!empty($lvl) ? $lvl : 'Not available'); ?>
             </td>
            </tr>
            <tr>
              <th> Place of Birth </th>
              <td>
              <?php echo htmlspecialchars(!empty($pob) ? $pob : 'Not available'); ?>
</td>
            </tr>
            <tr>
              <th>Date of Birth</th>
              <td>
              <?php echo htmlspecialchars(!empty($dob) ? $dob : 'Not available'); ?>
              </td>
            </tr>
            <tr>
              <th>E-mail</th>
              <td>
              <?php echo htmlspecialchars(!empty($email) ? $email : 'Not available'); ?>
             </td>
            </tr>
            <tr>
              <th> Address</th>
              <td>
              <input type="text" id="address" name="address" value="<?php echo htmlspecialchars(!empty($address) ? $address : 'Not available'); ?>" disabled><br>
  </td>
            </tr>
          </tbody>
        </table>      
      </div>
      <br>
      <a class='back-button' href='StudentList.php'>Go Back</a>
  </form>
</body>

</html>
<?php include "../header/footer.php" ?>