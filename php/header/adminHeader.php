<?php
include(__DIR__ . '/../config.php');
$id = $_SESSION['adminID'];
$query = mysqli_query($con, "SELECT*FROM admin WHERE ADMIN_ID=$id");
while ($result = mysqli_fetch_assoc($query)) {
  $res_Name = $result['ADMIN_USERNAME'];
}

function getBasePath()
{
  // Determine if we're on localhost or production
  $isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

  if ($isLocalhost) {
    // On localhost, we're in a subdirectory
    return '/Sesta-registration/';
  } else {
    // On production, we're at the root
    return '/';
  }
}

function loadAsset($type, $path)
{
  $basePath = getBasePath();
  if ($type === 'css') {
    echo '<link href="' . $basePath . $path . '" rel="stylesheet">';
  } elseif ($type === 'js') {
    echo '<script src="' . $basePath . $path . '"></script>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <link href="<?php echo getBasePath(); ?>php/header/headerStyle.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
  <header>
    <nav>
      <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <img src="<?php echo getBasePath(); ?>image/icon/logoSESTA2.png" width="200">

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
            <img src="<?php echo getBasePath(); ?>image/icon/logoSESTA2.png" width="200">

          </div>
        </div>

        <div class="sidebar-content">
          <ul class="lists">
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/admin_home.php" class="nav-link">
                <i class='bx bxs-home icon'></i>
                <span class="link">Home</span>
              </a>
            </li>            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/adminclass.php" class="nav-link">
                <i class='bx bxs-chalkboard icon'></i>
                <span class="link">Class</span>
              </a>
            </li>
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/AdminList.php" class="nav-link">
                <i class='bx bxs-user-circle icon'></i>
                <span class="link">Admin</span>
              </a>
            </li>

            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/teacherList.php" class="nav-link">
                <i class='bx bxs-book-reader icon'></i>
                <span class="link">Teacher</span>
              </a>
            </li>

            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/StudentList.php" class="nav-link">
                <i class='bx bxs-graduation icon'></i>
                <span class="link">Student</span>
              </a>
            </li>
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/AdminBilling.php" class="nav-link">
                <i class='bx bxs-dollar-circle icon'></i>
                <span class="link">Billing</span>
              </a>
            </li>
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/AdminbackupSummary.php" class="nav-link">
                <i class='bx bxs-pie-chart-alt-2 icon'></i>
                <span class="link">System Summary</span>
              </a>
            </li>
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/studentFullReport.php" class="nav-link">
                <i class='bx bxs-report icon'></i>
                <span class="link">Full Report</span>
              </a>
            </li>

          </ul>

          <div class="bottom-cotent">
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/login-logout/logout.php" class="nav-link">
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
  <div style="position: fixed; bottom: 16px; right: 70px; z-index: 50;">
    <script src="https://static.elfsight.com/platform/platform.js" async></script>
    <div class="elfsight-app-34c1fc02-7809-4a7b-b810-871487813e1f" data-elfsight-app-lazy></div>
  </div>
  <div id="root"></div>
  <link href="<?php echo getBasePath(); ?>chatbox/index-vXR3yhj7.css" rel="stylesheet">
  <script type="module" src="<?php echo getBasePath(); ?>chatbox/index-Dsumbowl.js"></script>
</body>

</html>