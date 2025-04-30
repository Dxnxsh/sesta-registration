<!-- error_page.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <title>Error</title>
    <!-- Include necessary CSS and SweetAlert library -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
</head>
<body style= "background-image: url(../image/admin.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script>
        // Use SweetAlert2 to show an error message
                // Use SweetAlert2 to show an error message
        Swal.fire({
            icon: "error",
            title: "Oops...",
            text: "Wrong Username or Password!",
        }).then(function() {
            // Redirect to the login page
            window.location.href = '../loginadmin.php';
        });
    </script>
</body>
</html>