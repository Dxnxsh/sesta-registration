<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <title>Register Teacher</title>
</head>
<body style= "background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%"> 
      <div class="container-sign">
        <div class="box form-box">

        <?php 
         session_start();
         include("../config.php");
         
         $id = $_SESSION['validTC'];
         $query = mysqli_query($con,"SELECT*FROM teacher WHERE TEACHER_ID=$id");
       
         while($result = mysqli_fetch_assoc($query)){
             $res_id = $result['TEACHER_ID'];
         }

         if(isset($_POST['submit'])){
            $username = $_POST['username'];
            $password = $_POST['password'];

            mysqli_query($con,"UPDATE `teacher` SET `TEACHER_USERNAME`='$username', `TEACHER_PWD`='$password' WHERE `TEACHER_ID`='$id'") or die("Error Occurred student ". mysqli_error($con));
            
            
            header("Location: error/noti_successTCacc.php");
              exit();
         }
         
        ?>

            <header>Teacher Account Registration</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter username here" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter password here" autocomplete="off" required>
                </div>

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
            </form>
        </div>
      </div>
</body>
</html>