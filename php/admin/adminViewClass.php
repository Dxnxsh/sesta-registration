<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
  SecuritySanitizer::logSecurityEvent('unauthorized_access', 'Admin view class access without valid session');
  header("Location: ../login-logout/login.php");
  exit();
}
?>
<?php if (isset($_GET['id'])) {
  $classCode = SecuritySanitizer::sanitize($_GET['id'], 'id', 'CLASS_CODE');
  
  if (empty($classCode)) {
    SecuritySanitizer::logSecurityEvent('invalid_input', 'Invalid class code in view: ' . $_GET['id']);
    header("Location: adminClass.php");
    exit();
  }
  
  $selectClassTeacher = "SELECT * FROM teacher t
  INNER JOIN class c ON t.TEACHER_ID = c.TEACHER_ID
  WHERE c.CLASS_CODE = ?";
  
  $stmt = mysqli_prepare($con, $selectClassTeacher);
  if (!$stmt) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare class view query: ' . mysqli_error($con));
    die('Database error occurred');
  }
  
  mysqli_stmt_bind_param($stmt, "s", $classCode);
  mysqli_stmt_execute($stmt);
  $queryClassTeacher = mysqli_stmt_get_result($stmt);

  if (!$queryClassTeacher) {
    SecuritySanitizer::logSecurityEvent('sql_error', 'Class view query failed: ' . mysqli_error($con));
    die('Error: ' . mysqli_error($con));
  }

  // Check if the query result is empty
  if (mysqli_num_rows($queryClassTeacher) > 0) {
    // Fetch and process the results
    while ($row = mysqli_fetch_assoc($queryClassTeacher)) {
      // Process each row of data
      // $row contains the combined data from both "teacher" and "class" tables
      $className = $row['CLASS_NAME'];
      $classlvl = $row['CLASS_LEVEL'];
      $blck = $row['CLASS_BLOCK'];
      $teachName = $row['TEACHER_NAME'];
      $teachid = $row['TEACHER_ID'];
      $teachphone = $row['TEACHER_PHONENUM'];
    }
  } else {
    // Show a popup using SweetAlert
    echo "<script>
            window.onload = function() {
              Swal.fire({
                title: 'Please assign a teacher to this class',
                icon: 'info',
                confirmButtonColor: '#007BFF',
              }).then((result) => {
                if (result.isConfirmed) {
                  window.location.href = 'adminClass.php';
                }
              });
            };
          </script>";

    // Display a message if the query result is empty
    $className = "Not Available";
    $classlvl = "Not Available";
    $blck = "Not Available";
    $teachName = "Not Available";
    $teachid = "Not Available";
    $teachphone = "Not Available";

  
  }
}
?>
<?php include "../header/adminHeader.php" ?>
<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>View Class Detail</title>
  <link href="../../css/AVS.css" rel="stylesheet" type="text/css">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.min.css">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.0.16/dist/sweetalert2.all.min.js"></script>

  <script>
    function goBack() {
        window.location.href = 'adminClass.php';
    }
  </script>


</head>

<body
  style="background-image: url(../../image/admin.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
  <div class="container">
    <h2><i class="bx bx-book"></i> VIEW CLASS DETAIL</h2>
    <form id="form1" name="form1" method="post" class="item-mid">
      <div class="table-wrapper">
        <table width="50%" border="1" cellspacing="5" class="fl-table">
          <tbody>
            <tr>
              <th>Class Code </th>
              <td>
                <?php echo htmlspecialchars($classCode ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Name </th>
              <td>
                <?php echo htmlspecialchars($className ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Level </th>
              <td>
                <?php echo htmlspecialchars($classlvl ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Block </th>
              <td>
                <?php echo htmlspecialchars($blck ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Teacher ID</th>
              <td>
                <?php echo htmlspecialchars($teachid ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Teacher Name</th>
              <td>
                <?php echo htmlspecialchars($teachName ?? '') ?>
              </td>
            </tr>
            <tr>
              <th> Teacher Contact</th>
              <td>
                <?php echo htmlspecialchars($teachphone ?? '') ?>
              </td>
            </tr>
          </tbody>
        </table>      
      </div>
      <br>
      <a class='back-button' href='adminClass.php'>Go Back</a>
  </form>
</body>

</html>
<?php include "../header/footer.php" ?>