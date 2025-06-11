<?php
if (!function_exists('getBasePath')) {
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
}
?>
<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="<?php echo getBasePath(); ?>css/footerStyle.css" rel="stylesheet" />

</head>
<body>
  <div class="wrapper">

      

    <div class="push"></div>
  </div>
  <footer class="footer">
	  &copy;<span id="year"> </span><span> Titan Company. All rights reserved.</span>
  </footer>
</body>
</html>

<script>
	let year = document.getElementById("year");

    document.addEventListener("DOMContentLoaded", function () {
      year.innerText = new Date().getFullYear();
    });
</script>