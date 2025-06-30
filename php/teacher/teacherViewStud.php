<?php
session_start();
include("../config.php");

if (!isset($_SESSION['validTC'])) {
  header("Location: ../login-logout/login.php");
  exit();
}

if (isset($_GET['id'])) {
  $stud_id = $_GET['id'];
  $teacherId = $_SESSION['validTC'];

  // Modified query to ensure teacher can only view students from their assigned classes
  $selectClassStudent = "SELECT * FROM student s
    INNER JOIN class c ON s.CLASS_CODE = c.CLASS_CODE
    WHERE s.STUDENT_ID = '$stud_id' AND c.TEACHER_ID = '$teacherId'";
  $queryClassStudent = mysqli_query($con, $selectClassStudent);

  if (!$queryClassStudent) {
    die('Error: ' . mysqli_error($con));
  }

  // Check if any student data was found
  $num_rows = mysqli_num_rows($queryClassStudent);
  if ($num_rows == 0) {
    header("Location: studentList.php");
    exit();
  }

  // Fetch and process the results
  while ($row = mysqli_fetch_assoc($queryClassStudent)) {
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
  }
}
?>

<?php include "../header/teacherHeader.php" ?>

<!doctype html>
<html>

<head>
  <meta charset="utf-8">
  <title>View Student</title>
  <link href="../../css/AVS.css" rel="stylesheet" type="text/css">
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
  <script>
    function goBack() {
      window.location.href = 'TeacherClass.php';
    }
  </script>
</head>

<body style="background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
  <div class="container">
    <h2><i class="bx bx-book"></i> VIEW STUDENT</h2>
    <form id="form1" name="form1" method="post" class="item-mid">
      <div class="table-wrapper">
        <table width="50%" border="1" cellspacing="5" class="fl-table">
          <tbody>
            <tr>
              <th>Name</th>
              <td><?php echo (!empty($studname) ? $studname : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>ID</th>
              <td><?php echo (!empty($stud_id) ? $stud_id : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Gender</th>
              <td><?php echo (!empty($gender) ? $gender : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Class</th>
              <td><?php echo (!empty($cCode) && !empty($cName) ? $cCode . ' - ' . $cName : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Study Level</th>
              <td><?php echo (!empty($lvl) ? $lvl : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Place of Birth</th>
              <td><?php echo (!empty($pob) ? $pob : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Date of Birth</th>
              <td><?php echo (!empty($dob) ? $dob : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>E-mail</th>
              <td><?php echo (!empty($email) ? $email : 'Not available'); ?></td>
            </tr>
            <tr>
              <th>Address</th>
              <td>
                <?php echo (!empty($address) ? nl2br(htmlspecialchars($address)) : 'Not available'); ?>
              </td>
            </tr>
          </tbody>
        </table>
      </div>
      <br>
      <a class='back-button' href='studentList.php'>Go Back</a>
    </form>
  </div>
</body>

</html>

<?php include "../header/footer.php" ?>