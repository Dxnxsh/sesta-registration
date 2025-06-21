<?php
include_once '../config/email_config.php';

?>
<html>
    <head>
        <title>Password Recovery using PHP and MySQL</title>
         <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
         <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
		<link rel="stylesheet" href="../../css/forgot_style.css" />
    </head>
<body style= "background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-4"></div>
                <div class="col-md-4">
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 
</br> 

                    <?php
                    $error = ""; // Initialize $error as an empty string
                    include('../config.php');
                    
                    if (isset($_POST["email"]) && (!empty($_POST["email"]))) {
                        
                        try {
                            // Sanitize and validate email
                            $email = SecuritySanitizer::sanitizeForDB($_POST["email"], 'email', 'STUDENT_EMAIL');
                            
                            if (empty($email)) {
                                $error .= "Invalid email address";
                            } else {
                                // Check for malicious input
                                if (detectMaliciousInput($email)) {
                                    SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                                        'field' => 'forgot_password',
                                        'email' => $email
                                    ]);
                                    $error .= "Invalid input detected";
                                } else {
                                    // Use prepared statement to check if user exists
                                    $sel_query = "SELECT STUDENT_EMAIL FROM student WHERE STUDENT_EMAIL = ?";
                                    $stmt = mysqli_prepare($con, $sel_query);
                                    
                                    if ($stmt) {
                                        mysqli_stmt_bind_param($stmt, "s", $email);
                                        mysqli_stmt_execute($stmt);
                                        $results = mysqli_stmt_get_result($stmt);
                                        $row = mysqli_num_rows($results);
                                        mysqli_stmt_close($stmt);
                                        
                                        if ($row == 0) {
                                            $error .= "User Not Found";
                                        }
                                    } else {
                                        $error .= "Database error occurred";
                                    }
                                }
                            }
                            
                        } catch (InvalidArgumentException $e) {
                            $error .= "Invalid email format: " . htmlspecialchars($e->getMessage());
                        }
                        
                        if ($error != "") {
                            echo $error;
                        } else {
                            $output = '';

                            $expFormat = mktime(date("H"), date("i"), date("s"), date("m"), date("d") + 1, date("Y"));
                            $expDate = date("Y-m-d H:i:s", $expFormat);
                            $key = md5(time());
                            $addKey = substr(md5(uniqid(rand(), 1)), 3, 10);
                            $key = $key . $addKey;
                            
                            // Insert into temp table using prepared statement
                            $insert_query = "INSERT INTO password_reset_temp (email, `key`, expDate) VALUES (?, ?, ?)";
                            $stmt = mysqli_prepare($con, $insert_query);
                            
                            if ($stmt) {
                                mysqli_stmt_bind_param($stmt, "sss", $email, $key, $expDate);
                                
                                if (mysqli_stmt_execute($stmt)) {
                                    SecuritySanitizer::logSecurityEvent('password_reset_requested', [
                                        'email' => $email
                                    ]);
                                    
                                    $output.='<p>Please click on the following link to reset your password.</p>';
                                    
                                    // Get the base URL dynamically
                                    $base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];
                                    
                                    // Use the base URL to create the absolute path (with proper sanitization)
                                    $safe_key = htmlspecialchars($key, ENT_QUOTES, 'UTF-8');
                                    $safe_email = htmlspecialchars($email, ENT_QUOTES, 'UTF-8');
                                    $output .= '<p><a href="' . $base_url . '/php/login-logout/reset-password.php?key=' . $safe_key . '&email=' . $safe_email . '&action=reset" target="_blank">' . $base_url . '/sesta-registration/php/login-logout/reset-password.php?key=' . $safe_key . '&email=' . $safe_email . '&action=reset</a></p>';
                                    
                                } else {
                                    $error .= "Error generating reset link. Please try again.";
                                }
                                mysqli_stmt_close($stmt);
                            } else {
                                $error .= "Database error occurred";
                            }
                            
                            if ($error != "") {
                                echo $error;
                            }
                        }
                        
                        if (isset($output) && !empty($output)) {
                            $body = $output;
                            $subject = "Password Recovery";
                            $email_to = $email;

                            // Use the new PHPMailer integration
                            include_once '../config/email_config.php';
                            
                            $result = EmailConfig::sendPasswordRecoveryEmail($email_to, $subject, $body);
                            
                            if (!$result['success']) {
                                echo "Mailer Error: " . $result['message'];
                            } else {
                                 ?>
    <script>
        // Use SweetAlert2 to show an error message
        Swal.fire({
            icon: 'success',
            title: 'Yeaaay!',
            text: 'An email has been sent',
        })
    </script>
    <?php
                            }
                        }
                    }
                    ?>
                   <div class="form-container">
					  <div class="logo-container">
						Forgot Password
					  </div>

					  <form class="form" method="post" action="" name="reset">
						<div class="form-group">
						  <label for="email">Email</label>
						  <input type="email" name="email" placeholder="username@email.com" class="form-control">
						</div>

						<button class="btn btn-primary" type="submit" id="reset">Reset Password</button>
                        
                        <a href="login.php" class="btn btn-secondary">Back to Login</a>
                        
					  </form>
					</div>


                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </body>
</html>