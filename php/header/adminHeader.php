<?php
include(__DIR__ . '/../config.php');

// Sanitize and validate session ID
$id = SecuritySanitizer::sanitize($_SESSION['adminID'] ?? '', 'id', 'ADMIN_ID');

if (empty($id)) {
    SecuritySanitizer::logSecurityEvent('admin_header_invalid_session', [
        'session_id' => session_id(),
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    header("Location: ../login-logout/login.php");
    exit();
}

// Use prepared statement for secure query
$stmt = $con->prepare("SELECT ADMIN_USERNAME FROM admin WHERE ADMIN_ID = ?");
$stmt->bind_param("s", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($row = $result->fetch_assoc()) {
    $res_Name = SecuritySanitizer::sanitize($row['ADMIN_USERNAME'], 'username');
} else {
    SecuritySanitizer::logSecurityEvent('admin_header_user_not_found', [
        'admin_id' => $id,
        'ip' => $_SERVER['REMOTE_ADDR'] ?? 'unknown'
    ]);
    $res_Name = 'Unknown Admin';
}

$stmt->close();

function getBasePath()
{
  // Determine if we're on localhost or production - secure host validation
  $host = SecuritySanitizer::sanitize($_SERVER['HTTP_HOST'] ?? 'localhost', 'name');
  $isLocalhost = ($host === 'localhost' || 
                  strpos($host, '127.0.0.1') === 0 || 
                  strpos($host, 'localhost:') === 0);

  if ($isLocalhost) {
    // On localhost, we're in a subdirectory
    return '/sesta-registration/';
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
              <a href="<?php echo getBasePath(); ?>php/admin/Admin_home.php" class="nav-link">
                <i class='bx bxs-home icon'></i>
                <span class="link">Home</span>
              </a>
            </li>            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/adminClass.php" class="nav-link">
                <i class='bx bxs-chalkboard icon'></i>
                <span class="link">Class</span>
              </a>
            </li>
            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/adminList.php" class="nav-link">
                <i class='bx bxs-user-circle icon'></i>
                <span class="link">Admin</span>
              </a>
            </li>

            <li class="list">
              <a href="<?php echo getBasePath(); ?>php/admin/TeacherList.php" class="nav-link">
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