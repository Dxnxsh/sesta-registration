<?php  
include(__DIR__ . '/../config.php');
$id = $_SESSION['adminID'];
$query = mysqli_query($con, "SELECT*FROM admin WHERE ADMIN_ID=$id");
while ($result = mysqli_fetch_assoc($query)) {
  $res_Name = $result['ADMIN_USERNAME'];
}?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="http://localhost/Sesta-registration/php/header/headerStyle.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
  <header>
    <nav>
      <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <img src="http://localhost/Sesta-registration/image/icon/logoSESTA2.png" width="200">

      </div>
      <div class="user">
        <i class='bx bx-user-circle user-icon'></i>
        <div class="user-name">
          <?php echo $res_Name ?>
        </div>
      </div>

      <div class="sidebar">
        <div class="logo">
          <div class="sideLogo">
            <i class="bx bx-menu menu-icon"></i>
            <img src="http://localhost/Sesta-registration/image/icon/logoSESTA2.png" width="200">

          </div>
        </div>

        <div class="sidebar-content">
          <ul class="lists">
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/admin_home.php" class="nav-link">
                <i class='bx bxs-home icon'></i>
                <span class="link">Home</span>
              </a>
            </li>
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/adminClass.php" class="nav-link">
                <i class='bx bxs-chalkboard icon'></i>
                <span class="link">Class</span>
              </a>
            </li>
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/adminList.php" class="nav-link">
                <i class='bx bxs-user-circle icon'></i>
                <span class="link">Admin</span>
              </a>
            </li>

            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/teacherList.php" class="nav-link">
                <i class='bx bxs-book-reader icon'></i>
                <span class="link">Teacher</span>
              </a>
            </li>

            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/studentlist.php" class="nav-link">
                <i class='bx bxs-graduation icon'></i>
                <span class="link">Student</span>
              </a>
            </li>
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/AdminBilling.php" class="nav-link">
                <i class='bx bxs-dollar-circle icon'></i>
                <span class="link">Billing</span>
              </a>
            </li>
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/AdminbackupSummary.php" class="nav-link">
                <i class='bx bxs-pie-chart-alt-2 icon'></i>
                <span class="link">System Summary</span>
              </a>
            </li>
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/admin/studentFullReport.php" class="nav-link">
                <i class='bx bxs-report icon'></i>
                <span class="link">Full Report</span>
              </a>
            </li>

          </ul>

          <div class="bottom-cotent">
            <li class="list">
              <a href="http://localhost/Sesta-registration/php/login-logout/logoutAdmin.php" class="nav-link">
                <i class="bx bx-log-out icon"></i>
                <span class="link">Logout</span>
              </a>
            </li>
          </div>
        </div>
      </div>
    </nav>

    <section class="overlay"></section>

    <script>
      const navBar = document.querySelector("nav"),
        menuBtns = document.querySelectorAll(".menu-icon"),
        overlay = document.querySelector(".overlay");

      menuBtns.forEach((menuBtn) => {
        menuBtn.addEventListener("click", () => {
          navBar.classList.toggle("open");
        });
      });

      overlay.addEventListener("click", () => {
        navBar.classList.remove("open");
      });
    </script>
  </header>
    </body>
    </html>