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
            $no_ic = mysqli_real_escape_string($con,$_POST['no_ic']);
                $result = mysqli_query($con,"SELECT * FROM teacher WHERE TEACHER_ID='$no_ic'") or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['validTC'] = $row['TEACHER_ID'];
                }

                $verify_query2 = mysqli_query($con, "SELECT TEACHER_USERNAME FROM teacher WHERE TEACHER_ID='$no_ic'");
                $row2 = mysqli_fetch_assoc($verify_query2);
                
                if (!empty($row2['TEACHER_USERNAME'])) {
                    // User is already registered
                    header("Location: error/error_TCexist.php");
                    exit();
                }

            
         //verifying the unique ic
         $verify_query = mysqli_query($con,"SELECT TEACHER_ID FROM teacher WHERE TEACHER_ID='$no_ic'");

         if(mysqli_num_rows($verify_query) !=0 ){

            header("Location: error/noti_successTC.php");
            exit();
         }
         else{
            // Redirect to the error page
            header("Location: error/error_verifyTC.php");
            exit();
         }

         }else{
         
        ?>

            <header>VERIFICATION</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="no_ic">IC Number</label>
                    <input type="text" name="no_ic" id="no_ic" autocomplete="off" required>
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