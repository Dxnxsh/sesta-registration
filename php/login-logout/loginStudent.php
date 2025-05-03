<?php
session_start();

// Ensure CAPTCHA text is generated and stored in the session
if (!isset($_SESSION['captcha_text'])) {
    $_SESSION['captcha_text'] = strtoupper(substr(md5(uniqid(mt_rand(), true)), 0, 6)); // Example CAPTCHA generation
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Login Student</title>

</head>

<body
    style="background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <?php
    // Include the function definition
    function showWhatsappWidget()
    {
    ?>
        <script src="https://static.elfsight.com/platform/platform.js" data-use-service-core defer></script>
        <div class="elfsight-app-752aad43-5ef5-4b28-9ef5-3f858f07f183" data-elfsight-app-lazy style="right: 50px;"></div>
    <?php
    }
    ?>
    <script type="module" src="..\..\chatbox\index-Dsumbowl.js"></script>
    <link rel="stylesheet" href="..\..\chatbox\index-vXR3yhj7.css">
    <div class="wrapper">
        <div class="container">
            <div class="box form-box">
                <?php

                include("../config.php");
                if (isset($_POST['submit'])) {
                    $userCaptcha = strtoupper(trim($_POST['captcha_input']));
                    $serverCaptcha = $_SESSION['captcha_text'];

                    if ($userCaptcha === $serverCaptcha) {
                        // ✅ CAPTCHA matched, now check login
                        $no_ic = mysqli_real_escape_string($con, $_POST['no_ic']);
                        $password = mysqli_real_escape_string($con, $_POST['password']);

                        $result = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID='$no_ic' AND STUDENT_PWD='$password' ") or die("Select Error");
                        $row = mysqli_fetch_assoc($result);

                        if (is_array($row) && !empty($row)) {
                            $_SESSION['valid'] = $row['STUDENT_ID'];
                        } else {
                            // Redirect to the error page
                            header("Location: error/error_page.php");
                            exit();
                        }

                        if (isset($_SESSION['valid'])) {
                            $studentId = $_SESSION['valid'];
                            $result = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID='$studentId'");
                            $row = mysqli_fetch_assoc($result);

                            if (isset($row['STUDENT_NAME']) && !empty($row['STUDENT_NAME'])) {
                                // Student has a name, redirect to student home page
                                header("Location: ../student/student_home.php");
                            } else {
                                // Student name is empty or null, redirect to registration page
                                header("Location: ../student/StudentRegistration.php");
                            }
                        }
                    } else {
                        // ❌ CAPTCHA wrong
                        header("Location: ../login-logout/error/error_captcha.php");
                    }
                } else {


                ?>
                    <header>Login Student</header>
                    <form action="" method="post">
                        <div class="field input">
                            <label for="no_ic">IC Number</label>
                            <input type="text" name="no_ic" id="no_ic" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                        </div>

                        <div class="field input">
                            <label for="password">Password</label>
                            <input type="password" name="password" id="password" autocomplete="off" required>
                        </div>

                        <!-- CAPTCHA Section -->
                        <div class="field input">
                            <label for="captcha_input">Enter CAPTCHA</label>
                            <img src="captcha.php" alt="CAPTCHA Image" style="margin-bottom: 10px;">
                            <input type="text" name="captcha_input" id="captcha_input" autocomplete="off" required>
                        </div>

                        <div class="links">
                            <a href="ForgotPS.php">Forgot Password?</a>
                        </div>

                        <div class="field">

                            <input type="submit" class="btn" name="submit" value="LOGIN" required>
                        </div>
                        <p>&nbsp;</p>
                    </form>
                    <a href="loginTeacher.php" class="home-sci">TEACHER</a>
                    <p>&nbsp;</p>
                    <a href="LoginAdmin.php" class="home-sci">ADMIN</a>
                    <p>&nbsp;</p>
                    <div class="links">
                        Don't have account? <a href=" StudentNewAccount.php">Sign Up Now</a>
                    </div>
            </div>
        <?php } ?>
        </div>
    </div>
    <div class="home-content">
        <h3>HELLO, WELCOME TO</h3>
        <h1>SEKOLAH MENENGAH SAINS TAPAH</h1>
        <h3>SCHOOL REGISTRATION SYSTEM </h3>
        <h3><span class="text"></span></h3>
    </div>
    <div style="position: fixed; bottom: 16px; right: 70px; z-index: 50;">
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-34c1fc02-7809-4a7b-b810-871487813e1f" data-elfsight-app-lazy></div>
    </div>
    <div id="root"></div>
    <video autoplay loop muted play-inline class="background-clip">
        <source src="../../image/bg1.mp4" type="video/mp4">
    </video>

    <script src="../../js/intro.js"></script>
</body>

</html>