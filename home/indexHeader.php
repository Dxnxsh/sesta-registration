<?php
function getBasePath()
{
  // Determine if we're on localhost or production
  $isLocalhost = ($_SERVER['HTTP_HOST'] === 'localhost' || strpos($_SERVER['HTTP_HOST'], '127.0.0.1') !== false);

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
  <link href="indexHeaderStyle.css" rel="stylesheet" />
  <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
</head>

<body>
  <header>
    <nav style="display: flex; align-items: center;">
      <div class="logo">
        <img src="<?php echo getBasePath(); ?>image/icon/logoSESTA2.png" width="200">
      </div>
      <div class="links" style="margin-left: 40px; display: flex; gap: 20px;">
        <div class="link"><a href="<?php echo getBasePath(); ?>home/index.php">HOME</a></div>
        <div class="link"><a href="<?php echo getBasePath(); ?>home/profile.php">PROFILE</a></div>
        <div class="link"><a href="<?php echo getBasePath(); ?>home/curriculum.php">CURRICULUM</a></div>
        <div class="link"><a href="<?php echo getBasePath(); ?>home/hem.php">HEM</a></div>
        <div class="link"><a href="<?php echo getBasePath(); ?>home/contactus.php">CONTACT US</a></div>
        <div class="link"><a href="<?php echo getBasePath(); ?>php/login-logout/login.php">LOGIN</a></div>
      </div>
    </nav>

    <section class="overlay"></section>
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