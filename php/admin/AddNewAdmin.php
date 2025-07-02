<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

?>
<?php include "../header/adminHeader.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Insert New Class Record</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="../../css/button.css">
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../../image/admin.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: grid;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }
    </style>
</head>

<body>


    <div class="container">
        <h2>Register New Admin</h2>
        <form action="" name="adminform" method="POST" id="adminform">
            <p>
                <label for="id">Admin ID:</label>
                <input type="text" name="adminId" id="adminId" placeholder="Enter ID" required>

                <label for="name">Username:</label>
                <input type="text" name="uname" id="uname" placeholder="Enter admin Name" required>

                <label for="name">Fullname:</label>
                <input type="text" name="fname" id="fname" placeholder="Enter admin Name" required>

                <label for="passwd">Contact Number:</label>
                <input type="text" name="phone" id="phone" required>

                <label for="passwd">Password:</label>
                <input type="text" name="pwd" id="pwd" required>

                <label for="passwd">Re-Enter Password:</label>
                <input type="text" name="pwd2" id="pwd2" required>



            </p>
            <div class="buttons">
                <a href="adminList.php"><input class="back-button" type="button" value="Back"></a>
                <button type="reset">Reset</button>
                <div class="spacer"></div>
                <button type="button" id="save" name="submit" value="adminform">Insert</button>
            </div>
        </form>

    </div>

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('adminform');
        const saveButton = document.getElementById('save');

        saveButton.addEventListener('click', function (event) {
            event.preventDefault();

            // Gather form data
            const formData = new FormData(form);

            // Check if passwords match
            const password = formData.get('pwd');
            const confirmPassword = formData.get('pwd2');

            if (password !== confirmPassword) {
                Swal.fire({
                    title: 'Password Mismatch',
                    text: 'The entered passwords do not match. Please re-enter your passwords.',
                    icon: 'error',
                    confirmButtonColor: '#FF0004',
                });
                return;
            }

            // Submit form data using AJAX
            fetch('insert_admin.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server
                if (data.success) {
                    Swal.fire({
                        title: 'Admin Registered',
                        text: 'The new Admin has been added successfully.',
                        icon: 'success',
                        confirmButtonColor: '#4caf50',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'adminList.php';
                        }
                    });
                } else {
                    // Handle different error messages
                    let errorTitle = 'Registration Failed';
                    let errorText = 'An error occurred while registering the admin.';
                    
                    if (data.message) {
                        if (data.message === 'Admin ID already exists') {
                            errorTitle = 'Admin ID Exists';
                            errorText = 'The Admin ID you entered already exists. Please choose a different ID.';
                        } else if (data.message === 'Database error occurred') {
                            errorTitle = 'Database Error';
                            errorText = 'A database error occurred. Please try again later.';
                        } else {
                            errorText = data.message;
                        }
                    }
                    
                    Swal.fire({
                        title: errorTitle,
                        text: errorText,
                        icon: 'error',
                        confirmButtonColor: '#FF0004',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>

</body>

</html>