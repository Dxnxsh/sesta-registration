<?php 
   session_start();
?>
<!-- error_page.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Registration success</title>
    <!-- Include necessary CSS and SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body style= "background-image: url(../../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script>
        // Use SweetAlert2 to show an error message
                // Use SweetAlert2 to show an error message
        Swal.fire({
            icon: "success",
            title: "Registration Successful!",
            text: "Welcome to SESTA!",
        }).then(function() {
            // Redirect to the login page
            window.location.href = '../Teacher_home.php';
        });
    </script>
</body>
</html>