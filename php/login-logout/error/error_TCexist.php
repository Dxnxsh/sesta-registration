<!-- error_page.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error</title>
    <!-- Include necessary CSS and SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body style= "background-image: url(../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script>
        // Use SweetAlert2 to show an error message
                // Use SweetAlert2 to show an error message
        Swal.fire({
            icon: "error",
            title: "Teacher Already Registered",
            text: "If you forgot your password, please contact admin",
        }).then(function() {
            // Redirect to the login page
            window.location.href = '../verifyTeacher.php';
        });
    </script>
</body>
</html>