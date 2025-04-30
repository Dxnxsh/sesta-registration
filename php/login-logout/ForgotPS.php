<?php
include_once '../../PHPMailer/PHPMailerAutoload.php';

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
                        $email = $_POST["email"];
                        $email = filter_var($email, FILTER_SANITIZE_EMAIL);
                        $email = filter_var($email, FILTER_VALIDATE_EMAIL);
                        if (!$email) {
                            $error .="Invalid email address";
                        } else {
                            $sel_query = "SELECT * FROM `student` WHERE STUDENT_EMAIL='" . $email . "'";
                            $results = mysqli_query($con, $sel_query);
                            $row = mysqli_num_rows($results);
                            if ($row == "") {
                                $error .= "User Not Found";
                            }
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
                            // Insert Temp Table
                            mysqli_query($con, "INSERT INTO `password_reset_temp` (`email`, `key`, `expDate`) VALUES ('" . $email . "', '" . $key . "', '" . $expDate . "');");


                            $output.='<p>Please click on the following link to reset your password.</p>';
                            //replace the site url

                            // Get the base URL dynamically
$base_url = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://" . $_SERVER['HTTP_HOST'];

// Use the base URL to create the absolute path
$output .= '<p><a href="' . $base_url . '/Sesta-registration/php/login-logout/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset" target="_blank">' . $base_url . '/Sesta-registration/php/login-logout/reset-password.php?key=' . $key . '&email=' . $email . '&action=reset</a></p>'; $body = $output;
                            $subject = "Password Recovery";

                            $email_to = $email;


                            //autoload the PHPMailer
                            include_once '../../PHPMailer/PHPMailerAutoload.php';
                            $mail = new PHPMailer();
                            $mail->CharSet = 'UTF-8';
                            $mail->IsSMTP();
                        
                            $mail->IsHTML(true);
                            $mail->From = "support@rathorji.in";
                            $mail->FromName = "SESTA TAPAH";

                            $mail->Subject = $subject;
                            $mail->Body = $body;
                            $mail->AddAddress($email_to);
                            if (!$mail->Send()) {
                                echo "Mailer Error: " . $mail->ErrorInfo;
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
                        
                        <a href="LoginStudent.php" class="btn btn-secondary">Back to Login</a>
                        
					  </form>
					</div>


                </div>
                <div class="col-md-4"></div>
            </div>
        </div>
    </body>
</html>