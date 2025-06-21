<?php 
session_start();
    session_destroy();

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <title>Verification Teacher</title>
</head>
<body style= "background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
      <div class="container-sign">
        <div class="box form-box">

        <?php 
         session_start();

         include("../config.php");
         
         if(isset($_POST['submit'])){
            $no_ic = trim($_POST['no_ic']);
            
            // Sanitize and validate IC number
            $no_ic = SecuritySanitizer::sanitize($no_ic, 'id', 'TEACHER_ID');
            
            if (!$no_ic) {
                SecuritySanitizer::logSecurityEvent("Invalid IC number attempted in teacher verification: " . $_POST['no_ic'], 'MEDIUM');
                header("Location: error/error_VerifyTc.php");
                exit();
            }
            
            // Use prepared statement to check teacher existence
            $stmt = $con->prepare("SELECT * FROM teacher WHERE TEACHER_ID = ?");
            $stmt->bind_param("s", $no_ic);
            $stmt->execute();
            $result = $stmt->get_result();
            $row = $result->fetch_assoc();
            $stmt->close();

            if($row && !empty($row)){
                $_SESSION['validTC'] = $row['TEACHER_ID'];
                
                // Check if teacher already has username using prepared statement
                $stmt2 = $con->prepare("SELECT TEACHER_USERNAME FROM teacher WHERE TEACHER_ID = ?");
                $stmt2->bind_param("s", $no_ic);
                $stmt2->execute();
                $result2 = $stmt2->get_result();
                $row2 = $result2->fetch_assoc();
                $stmt2->close();
                
                if (!empty($row2['TEACHER_USERNAME'])) {
                    // User is already registered
                    SecuritySanitizer::logSecurityEvent("Teacher verification attempt for already registered teacher: $no_ic", 'INFO');
                    header("Location: error/error_TCexist.php");
                    exit();
                }
                
                // Teacher exists but not yet registered
                SecuritySanitizer::logSecurityEvent("Teacher verification successful for: $no_ic", 'INFO');
                header("Location: error/noti_successTC.php");
                exit();
            } else {
                // Teacher not found
                SecuritySanitizer::logSecurityEvent("Teacher verification failed - teacher not found: $no_ic", 'MEDIUM');
                header("Location: error/error_VerifyTc.php");
                exit();
            }

         }else{
         
        ?>

            <header>VERIFICATION</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="no_ic">IC Number</label>
                    <input type="text" name="no_ic" id="no_ic" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Verify" required>
                </div>
                <div class="links">
                    Already a member? <a href="login.php">Sign In</a>
                </div>
            </form>
        </div>
        <?php } ?>
      </div>
</body>
</html>
<?php include "../header/footer.php" ?>