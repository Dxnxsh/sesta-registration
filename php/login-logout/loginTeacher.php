<?php 

        session_start();
      session_destroy();
?>
<?php 
   session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <title>Login Teacher</title>
    
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
             
              include("../config.php");
              if(isset($_POST['submit'])){
                $username = mysqli_real_escape_string($con,$_POST['username']);
                $password = mysqli_real_escape_string($con,$_POST['password']);

                $result = mysqli_query($con,"SELECT * FROM teacher WHERE TEACHER_USERNAME='$username' AND TEACHER_PWD='$password' ") or die("Select Error");
                $row = mysqli_fetch_assoc($result);

                if(is_array($row) && !empty($row)){
                    $_SESSION['validTC'] = $row['TEACHER_ID']; // Add this line to store TEACHER_ID in the session
                }else{
                     // Redirect to the error page
                     header("Location: error/error_pageTC.php");
                     exit();
         
                }
                if(isset($_SESSION['validTC'])){
                    header("Location: ../teacher/teacher_home.php");
                }
              }else{

            
            ?>
            <header>Login Teacher</header>
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
                <div class="links">
                    Don't have account? <a href="verifyTeacher.php">Sign Up Now</a>
                </div>
            </form>
            <a href="loginStudent.php" class="home-sci">STUDENT</a>
            <p>&nbsp;</p>
            <a href="LoginAdmin.php" class="home-sci">ADMIN</a>
        </div>
        <?php } ?>
      </div>
      <div class="home-content-T">
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