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
            
            // Use prepared statement to get admin info
            $query = "SELECT ADMIN_ID FROM admin WHERE ADMIN_ID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $admin = mysqli_fetch_assoc($result);
            $res_id = $admin['ADMIN_ID'] ?? null;
            mysqli_stmt_close($stmt);

            if (isset($_POST['submit'])) {
                try {
                    // Sanitize and validate Teacher ID
                    $TeachID = SecuritySanitizer::sanitizeForDB($_POST['TeacherID'] ?? '', 'id', 'TEACHER_ID');

                    if (empty($TeachID)) {
                        throw new InvalidArgumentException("Teacher ID is required");
                    }

                    // Check for malicious input
                    if (detectMaliciousInput($TeachID)) {
                        SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                            'field' => 'teacher_creation',
                            'admin_id' => $id
                        ]);
                        throw new InvalidArgumentException("Invalid input detected");
                    }

                } catch (InvalidArgumentException $e) {
                    echo "<script>
                        document.addEventListener('DOMContentLoaded', function() {
                            Swal.fire({
                                icon: 'error',
                                title: 'Invalid Input',
                                text: '" . htmlspecialchars($e->getMessage()) . "',
                                confirmButtonText: 'OK'
                            });
                        });
                    </script>";
                    $TeachID = null;
                }

                if ($TeachID) {
                    // Check if teacher ID already exists using prepared statement
                    $verify_query = "SELECT TEACHER_ID FROM teacher WHERE TEACHER_ID = ?";
                    $stmt = mysqli_prepare($con, $verify_query);
                    mysqli_stmt_bind_param($stmt, "s", $TeachID);
                    mysqli_stmt_execute($stmt);
                    $verify_result = mysqli_stmt_get_result($stmt);
                    
                    if (mysqli_num_rows($verify_result) != 0) {
                        mysqli_stmt_close($stmt);
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
                        mysqli_stmt_close($stmt);
                        
                        // Insert new teacher using prepared statement
                        $insert_query = "INSERT INTO teacher (TEACHER_ID, ADMIN_ID) VALUES (?, ?)";
                        $stmt = mysqli_prepare($con, $insert_query);
                        
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "ss", $TeachID, $res_id);
                            
                            if (mysqli_stmt_execute($stmt)) {
                                SecuritySanitizer::logSecurityEvent('teacher_created', [
                                    'teacher_id' => $TeachID,
                                    'created_by' => $id
                                ]);
                                header("Location: noti/noti_AddTeach.php");
                                exit();
                            } else {
                                SecuritySanitizer::logSecurityEvent('teacher_creation_failed', [
                                    'teacher_id' => $TeachID,
                                    'admin_id' => $id,
                                    'error' => mysqli_error($con)
                                ]);
                                echo "<script>
                                    document.addEventListener('DOMContentLoaded', function() {
                                        Swal.fire({
                                            icon: 'error',
                                            title: 'Database Error',
                                            text: 'Error occurred during registration.',
                                            confirmButtonText: 'OK'
                                        });
                                    });
                                </script>";
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            echo "<script>
                                document.addEventListener('DOMContentLoaded', function() {
                                    Swal.fire({
                                        icon: 'error',
                                        title: 'Database Error',
                                        text: 'Database preparation error.',
                                        confirmButtonText: 'OK'
                                    });
                                });
                            </script>";
                        }
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