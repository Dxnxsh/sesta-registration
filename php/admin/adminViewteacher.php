<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
  SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Admin view teacher access without valid session');
  header("Location: ../login-logout/login.php");
  exit();
}

// Initialize variables
$studname = $id = $cCode = $cName = $gender = $dob = $pob = $email = $address = '';

if (isset($_GET['id'])) {
  $stud_id = SecuritySanitizer::sanitize($_GET['id'], 'id', 'TEACHER_ID');
  
  // Use prepared statement to prevent SQL injection
  $selectClassStudent = "SELECT * FROM teacher s
  LEFT JOIN class c ON s.TEACHER_ID = c.TEACHER_ID
  WHERE s.TEACHER_ID = ?";
  
  $stmt = mysqli_prepare($con, $selectClassStudent);
  if (!$stmt) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare teacher view query: ' . mysqli_error($con));
    die('Database error occurred');
  }
  
  mysqli_stmt_bind_param($stmt, "s", $stud_id);
  mysqli_stmt_execute($stmt);
  $queryClassStudent = mysqli_stmt_get_result($stmt);

  if (!$queryClassStudent) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Teacher view query failed: ' . mysqli_error($con));
    die('Error: ' . mysqli_error($con));
  }

  // Fetch and process the results
  if ($row = mysqli_fetch_assoc($queryClassStudent)) {
    // Process each row of data
    // $row contains the combined data from both "teacher" and "class" tables
    $studname = $row['TEACHER_NAME'];
    $id = $row['TEACHER_ID'];
    $cCode = $row['CLASS_CODE'];
    $cName = $row['CLASS_NAME'];
    $gender = $row['TEACHER_GENDER'];
    $dob = $row['TEACHER_DOB'];
    $pob = $row['TEACHER_PHONENUM'];
    $email = $row['TEACHER_EMAIL'];
    $address = $row['TEACHER_ADDRESS'];
  } else {
    SecuritySanitizer::logSecurityEvent('invalid_access', 'Teacher ID not found: ' . $stud_id);
  }
  
  mysqli_stmt_close($stmt);
} else {
  SecuritySanitizer::logSecurityEvent('invalid_access', 'Admin view teacher accessed without teacher ID');
}
?>
<?php include "../header/adminHeader.php" ?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>VIEW TEACHER</title>
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
    <h2><i class="bx bx-book"></i> VIEW TEACHER</h2>
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
              <th> Phone Number </th>
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
      <a class='back-button' href='TeacherList.php'>Go Back</a>
  </form>
</body>

</html>
<?php include "../header/footer.php" ?>