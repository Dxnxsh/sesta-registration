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
<body style= "background-image: url(../../../image/admin.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script>
        // Use SweetAlert2 to show an error message
                // Use SweetAlert2 to show an error message
        Swal.fire({
            icon: "success",
            title: "Add Teacher Successful!",
            text: "Make sure to assign the teacher into class",
        }).then(function() {
            // Redirect to the login page
            window.location.href = '../teacherList.php';
        });
    </script>
</body>
</html>