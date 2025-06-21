<?php
if (!function_exists('getBasePath')) {
  function getBasePath()
  {
    // Determine if we're on localhost or production - secure host validation
    $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
    // Basic sanitization for host header to prevent header injection
    $host = filter_var($host, FILTER_SANITIZE_STRING);
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