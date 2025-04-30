<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <title>Register Student</title>
</head>

<body
    style="background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <div class="container-sign">
        <div class="box form-box">

            <?php

            include("../config.php");
            if (isset($_POST['submit'])) {
                $ic = $_POST['ic'];
                $email = $_POST['email'];
                $pwd = $_POST['pwd'];
                $pwd2 = $_POST['pwd2'];

                //verifying the ic and password
            
                $verify_query = mysqli_query($con, "SELECT STUDENT_ID FROM student WHERE student_id='$ic'");

                if ($pwd != $pwd2) {
                    // Redirect to the error page
                    header("Location: error/error_page1.php");
                    exit();
                } else if (mysqli_num_rows($verify_query) != 0) {
                    // Redirect to the error page
                    header("Location: error/error_page2.php");
                    exit();
                } else {

                    mysqli_query($con, "INSERT INTO `student`(`STUDENT_ID`,`STUDENT_EMAIL`, `STUDENT_PWD`) VALUES('$ic','$email', '$pwd')") or die("Error Occurred");
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('200', 'SCHOOL FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('210', 'DORMITORY FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('100', 'PIBG FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));


                    header("Location: loginStudentb.php");
                    exit();
                }
            }


            ?>

            <header>Sign Up Student</header>
            <form action="" method="post">
                <div class="field input">
                    <label for="ic">IC</label>
                    <input type="text" name="ic" id="ic" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="pwd">Password</label>
                    <input type="password" name="pwd" id="pwd" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password2">Re-enter Password</label>
                    <input type="password" name="pwd2" id="pwd2" autocomplete="off" required>
                </div>

                <input type="submit" class="btn" name="submit" value="Register" required>
                <div class="links">
                    Already a member? <a href="LoginStudent.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
</body>

</html>