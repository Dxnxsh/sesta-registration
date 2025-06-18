<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
}
?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/sweetalert2.min.css">
    <title>Register Teacher</title>
</head>

<body style="background-image: url(../../image/bg5.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <div class="container-sign">
        <div class="box form-box">
            <?php
            include("../config.php");

            $id = $_SESSION['adminID'];
            $query = mysqli_query($con, "SELECT*FROM admin WHERE ADMIN_ID=$id");

            while ($result = mysqli_fetch_assoc($query)) {
                $res_id = $result['ADMIN_ID'];
            }

            if (isset($_POST['submit'])) {
                $TeachID = $_POST['TeacherID'];

                //verifying the unique Teacher iD

                $verify_query = mysqli_query($con, "SELECT TEACHER_ID FROM teacher WHERE TEACHER_ID='$TeachID'");

                if (mysqli_num_rows($verify_query) != 0) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Registration Failed',
                                text: 'Teacher already registered!',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>";
                } else {
                    $insert_query = mysqli_query($con, "INSERT INTO `teacher` (`TEACHER_ID`, `ADMIN_ID`) VALUES ('$TeachID', '$res_id')");

                    if ($insert_query) {
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Success!',
                                    text: 'Teacher registered successfully!',
                                    confirmButtonText: 'OK'
                                }).then((result) => {
                                    if (result.isConfirmed) {
                                        window.location.href = 'noti/noti_AddTeach.php';
                                    }
                                });
                            });
                        </script>";
                    } else {
                        echo "<script>
                            document.addEventListener('DOMContentLoaded', function() {
                                Swal.fire({
                                    icon: 'error',
                                    title: 'Database Error',
                                    text: 'Error occurred: " . mysqli_error($con) . "',
                                    confirmButtonText: 'OK'
                                });
                            });
                        </script>";
                    }
                }
            }
            ?>
            <header>Add New Teacher</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="IC">New Teacher Ic</label>
                    <input type="text" name="TeacherID" id="TeacherID" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                </div>
                <div class="field">
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
            </form>
            <button class="btn" style="background-color: #007BFF; margin-top: 0px;" onclick="window.location.href='TeacherList.php'">Back</button>
        </div>
    </div>
    <script src="../../js/sweetalert2.all.min.js"></script>
</body>

</html>
<?php include "../header/footer.php" ?>