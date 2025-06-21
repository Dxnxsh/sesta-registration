<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('../../image/bg11.jpeg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: 'Arial', sans-serif;
        }

        .container-fluid {
            margin-top: 50px;
        }

        .col-md-4 {
            text-align: center;
        }

        .error {
            color: red;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #337ab7;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #286090;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <?php
                include('../config.php');

                $error = ""; // Initialize $error as an empty string
                
                if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"] == "reset") && !isset($_POST["action"])) {
                    $key = $_GET["key"];
                    $email = $_GET["email"];
                    $curDate = date("Y-m-d H:i:s");
                    $query = mysqli_query($con, "SELECT * FROM `password_reset_temp` WHERE `key`='$key' and `email`='$email';");
                    $row = mysqli_num_rows($query);

                    if ($row == 0) {
                        $error .= '<h2 class="error">Invalid Link</h2>';
                    } else {
                        $row = mysqli_fetch_assoc($query);
                        $expDate = $row['expDate'];

                        if ($expDate >= $curDate) {
                            ?>

                            <form method="post" action="" name="update">
                                <h2>Reset Password</h2>
                                <input type="hidden" name="action" value="update" class="form-control" />
                                <div class="form-group">
                                    <label><strong>Enter New Password:</strong></label>
                                    <input type="password" name="pass1" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><strong>Re-Enter New Password:</strong></label>
                                    <input type="password" name="pass2" class="form-control" />
                                </div>
                                <input type="hidden" name="email" value="<?php echo $email; ?>" />
                                <div class="form-group">
                                    <input type="submit" id="reset" value="Reset Password" class="btn btn-primary" />
                                </div>
                            </form>
                            <?php
                        } else {
                            $error .= "<h2>Link Expired</h2>";
                        }
                    }

                    if ($error != "") {
                        echo "<div class='error'>" . $error . "</div><br />";
                    }
                }

                if (isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"] == "update")) {
                    
                    $error = "";
                    
                    try {
                        // Sanitize and validate input data
                        $pass1 = SecuritySanitizer::sanitizeForDB($_POST["pass1"] ?? '', 'password', 'STUDENT_PWD');
                        $pass2 = SecuritySanitizer::sanitizeForDB($_POST["pass2"] ?? '', 'password', 'STUDENT_PWD');
                        $email = SecuritySanitizer::sanitizeForDB($_POST["email"] ?? '', 'email', 'STUDENT_EMAIL');

                        // Validate required fields
                        if (empty($pass1) || empty($pass2) || empty($email)) {
                            $error .= "<p class='error'>All fields are required.<br /><br /></p>";
                        }

                        // Check for malicious input
                        if (!$error && (detectMaliciousInput($pass1) || detectMaliciousInput($pass2) || detectMaliciousInput($email))) {
                            SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                                'field' => 'password_reset',
                                'email' => $email
                            ]);
                            $error .= "<p class='error'>Invalid input detected.<br /><br /></p>";
                        }

                    } catch (InvalidArgumentException $e) {
                        $error .= "<p class='error'>Invalid input format: " . htmlspecialchars($e->getMessage()) . "<br /><br /></p>";
                    }

                    $curDate = date("Y-m-d H:i:s");

                    if ($pass1 != $pass2) {
                        $error .= "<p class='error'>Passwords do not match. Both passwords should be the same.<br /><br /></p>";
                    }

                    if ($error != "") {
                        echo $error;
                    } else {
                        // Update the password in the database using prepared statement
                        $updateQuery = "UPDATE student SET STUDENT_PWD = ? WHERE STUDENT_EMAIL = ?";
                        $stmt = mysqli_prepare($con, $updateQuery);
                        
                        if ($stmt) {
                            mysqli_stmt_bind_param($stmt, "ss", $pass1, $email);
                            
                            if (mysqli_stmt_execute($stmt)) {
                                // Remove the password reset entry using prepared statement
                                $deleteQuery = "DELETE FROM password_reset_temp WHERE email = ?";
                                $stmt2 = mysqli_prepare($con, $deleteQuery);
                                
                                if ($stmt2) {
                                    mysqli_stmt_bind_param($stmt2, "s", $email);
                                    mysqli_stmt_execute($stmt2);
                                    mysqli_stmt_close($stmt2);
                                }

                                SecuritySanitizer::logSecurityEvent('password_reset_completed', [
                                    'email' => $email
                                ]);

                                echo '<div class="error"><p>Congratulations! Your password has been updated successfully.</p>';
                                
                                // Check if STUDENT_NAME is empty or null using prepared statement
                                $studentQuery = "SELECT STUDENT_NAME FROM student WHERE STUDENT_EMAIL = ?";
                                $stmt3 = mysqli_prepare($con, $studentQuery);
                                
                                if ($stmt3) {
                                    mysqli_stmt_bind_param($stmt3, "s", $email);
                                    mysqli_stmt_execute($stmt3);
                                    $result = mysqli_stmt_get_result($stmt3);
                                    $studentRow = mysqli_fetch_assoc($result);
                                    mysqli_stmt_close($stmt3);

                                    if (empty($studentRow['STUDENT_NAME']) || is_null($studentRow['STUDENT_NAME'])) {
                                        echo "<a href='../student/StudentRegistration.php'><button class='btn'>Proceed to Registration</button>";
                                    } else {
                                        echo "<a href='login.php'><button class='btn'>Login Here</button>";
                                    }
                                    echo '</div>'; // Close the 'error' div
                                } else {
                                    echo "<p class='error'>Database error retrieving student information.</p>";
                                }
                            } else {
                                SecuritySanitizer::logSecurityEvent('password_reset_failed', [
                                    'email' => $email,
                                    'error' => mysqli_error($con)
                                ]);
                                echo "<p class='error'>Error updating password. Please try again.</p>";
                            }
                            mysqli_stmt_close($stmt);
                        } else {
                            echo "<p class='error'>Database preparation error. Please try again.</p>";
                        }
                    }
                }
                ?>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>

</body>

</html>