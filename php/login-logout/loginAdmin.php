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
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <title>Login Admin</title>
</head>
<body style="background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <?php
    // Include the function definition
    function showWhatsappWidget()
    {
        ?>
        <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
        <div class="elfsight-app-752aad43-5ef5-4b28-9ef5-3f858f07f183" data-elfsight-app-lazy></div>
        <?php
    }

    // Call the function to display the WhatsApp widget
    showWhatsappWidget();
    ?>
<div class="wrapper">
      <div class="container">
        <div class="box form-box">
            <?php 
               session_start();
             
              include("../config.php");
              if(isset($_POST['submit'])){
                $username = mysqli_real_escape_string($con,$_POST['username']);
                $password = mysqli_real_escape_string($con,$_POST['password']);

                $result = mysqli_query($con,"SELECT * FROM admin WHERE ADMIN_USERNAME='$username' AND ADMIN_PWD='$password' ") or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['adminID'] = $row['ADMIN_ID'];
                }else{
                    
                      // Redirect to the error page
                      header("Location: error/error_admin.php");
                      exit();
                }
                if(isset($_SESSION['adminID'])){
                    header("Location: ../admin/admin_home.php");
                }
              }else{

            
            ?>
            <header>Login Admin</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" autocomplete="off" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="LOGIN" required>
                </div>
            </form>
            <a href="loginStudent.php" class="home-sci">STUDENT</a>
            <p>&nbsp;</p>
            <a href="LoginTeacher.php" class="home-sci">TEACHER</a>
            
        </div>
        <?php } ?>
      </div>
      <div class="home-content-A">
                <h3>HELLO, WELCOME TO</h3>
                <h1>SEKOLAH MENENGAH SAINS TAPAH</h1>
                <h3>SCHOOL REGISTRATION SYSTEM </h3>
                <h3><span class="text"></span></h3>
</div>
      <video autoplay loop muted play-inline class="background-clip">
                <source src="../../image/bg1.mp4" type="video/mp4">
            </video>
            
            <script src="../../js/intro.js"></script>
</body>
</html>