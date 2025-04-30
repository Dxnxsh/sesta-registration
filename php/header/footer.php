<!doctype html>
<html>
<head>
<meta charset="utf-8">
<link href="http://localhost/Sesta-registration/php/header/footerStyle.css"  rel="stylesheet" />

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