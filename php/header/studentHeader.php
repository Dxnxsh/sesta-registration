<?php

include(__DIR__ . '/../config.php');

$id = $_SESSION['valid'];
$query = mysqli_query($con, "SELECT*FROM student WHERE STUDENT_ID=$id");

while ($result = mysqli_fetch_assoc($query)) {
  $res_Name = $result['STUDENT_NAME'];
}

function loadAsset($type, $path) {
  $base_url = "http://localhost/Sesta-registration";
  if ($type === 'css') {
      echo '<link href="' . $base_url . $path . '" rel="stylesheet">';
  } elseif ($type === 'js') {
      echo '<script src="' . $base_url . $path . '"></script>';
  }
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta http-equiv="X-UA-Compatible" content="IE=edge" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <!-- CSS -->
  <link href="http://localhost/Sesta-registration/php/header/headerStyle.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
  <header>
    <nav>
      <div class="logo">
        <i class="bx bx-menu menu-icon"></i>
        <img src="http://localhost/Sesta-registration/image/icon/logoSESTA2.png" width="200">
        <div class="user">
          <i class='bx bx-user-circle user-icon'></i>
          <div class="user-name"><?php echo $res_Name ?></div>
        </div>

        <div class="sidebar">
          <div class="logo">
            <div class="sideLogo">
              <i class="bx bx-menu menu-icon"></i>
              <img src="http://localhost/Sesta-registration/image/icon/logoSESTA2.png" width="200">
            </div>

            <div class="sidebar-content">
              <ul class="lists">
                <li class="list">
                  <a href="http://localhost/Sesta-registration/php/student/student_home.php" class="nav-link">
                    <i class='bx bxs-home icon'></i>
                    <span class="link">Home</span>
                  </a>
                </li>
                <li class="list">
                  <a href="http://localhost/Sesta-registration/html/subject.html" class="nav-link">
                    <i class='bx bxs-book icon'></i>
                    <span class="link">Subject Outline</span>
                  </a>
                </li>
                <li class="list">
                  <a href="http://localhost/Sesta-registration/php/student/billing/billing.php" class="nav-link">
                    <i class='bx bxs-dollar-circle icon'></i>
                    <span class="link">Billing</span>
                  </a>
                </li>
                <li class="list">
                  <a href="http://localhost/Sesta-registration/php/student/studentCard.php" class="nav-link">
                    <i class='bx bxs-id-card icon'></i>
                    <span class="link">Student Card</span>
                  </a>
                </li>
              </ul>

              <div class="bottom-cotent">
                <li class="list">
                  <a href="http://localhost/Sesta-registration/php/login-logout/logout.php" class="nav-link"> <i class="bx bx-log-out icon"></i>
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
  <?php
  loadAsset('css', '/chatbox/index-vXR3yhj7.css');
  loadAsset('js', '/chatbox/index-Dsumbowl.js');
  ?>
</body>

</html>