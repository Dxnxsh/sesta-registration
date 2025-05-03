<?php 
	session_start();
   include("../config.php");
   if(!isset($_SESSION['adminID'])){
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
    <title>Register Teacher</title>
</head>
<body style= "background-image: url(../../image/bg5.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%"> 
      <div class="container-sign">
        <div class="box form-box">
        <?php 
         include("../config.php");
         
         $id = $_SESSION['adminID'] ;
         $query = mysqli_query($con,"SELECT*FROM admin WHERE ADMIN_ID=$id");
       
         while($result = mysqli_fetch_assoc($query)){
             $res_id = $result['ADMIN_ID'];
         }

         if(isset($_POST['submit'])){
            $TeachID = $_POST['TeacherID'];

         //verifying the unique Teacher iD

         $verify_query = mysqli_query($con,"SELECT TEACHER_ID FROM teacher WHERE TEACHER_ID='$TeachID'");

         if(mysqli_num_rows($verify_query) !=0 ){
            echo "<div class='message'>
                      <p>Teacher already registered!</p>
                  </div> <br>";
            echo "<a href='javascript:self.history.back()'><button class='btn'>Go Back</button>";
         }
         else{
            mysqli_query($con, "INSERT INTO `teacher` (`TEACHER_ID`, `ADMIN_ID`) VALUES ('$TeachID', '$res_id')") or die("Error Occurred student " . mysqli_error($con));

            header("Location: noti/noti_AddTeach.php");
            exit();
         }
        }
        ?>
            <header>Add New Teacher</header>
            <form action="" method="post">
            <input class="button" type="submit" name="submit2" id="submit2" formaction="teacherlist.php"
                    value="Back">
                <div class="field input">
                    <label for="IC">New Teacher Ic</label>
                    <input type="text" name="TeacherID" id="TeacherID" maxlength="12" autocomplete="off">
                
                </div>
                

                <div class="field">
                    
                    <input type="submit" class="btn" name="submit" value="Register" required>
                </div>
            </form>
        </div>
      </div>
</body>
</html>
<?php include "../header/footer.php" ?>