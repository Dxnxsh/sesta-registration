<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <script src="https://unpkg.com/typed.js@2.0.16/dist/typed.umd.js"></script>
    <title>Login</title>
</head>

<body style="background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script type="module" src="..\..\chatbox\index-Dsumbowl.js"></script>
    <link rel="stylesheet" href="..\..\chatbox\index-vXR3yhj7.css">
    <div style="position: fixed; bottom: 16px; right: 70px; z-index: 50;">
        <script src="https://static.elfsight.com/platform/platform.js" async></script>
        <div class="elfsight-app-34c1fc02-7809-4a7b-b810-871487813e1f" data-elfsight-app-lazy></div>
    </div>
    <div id="root"></div>
    <div class="wrapper">
        <div class="container">

            <!-- Admin Login Form -->
            <div id="admin-form" class="box form-box" style="display: none;">
                <header>Login Admin</header>
                <form action="loginVerify.php" method="post">
                <input type="hidden" name="role" value="admin">
                    <div class="field input">
                        <label for="admin-username">Username</label>
                        <input type="text" name="username" id="admin-username" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="admin-password">Password</label>
                        <input type="password" name="password" id="admin-password" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="LOGIN">
                    </div>
                </form>
                <button class="home-sci" onclick="showForm('student')">STUDENT</button>
                <p>&nbsp;</p>
                <button class="home-sci" onclick="showForm('teacher')">TEACHER</button>
            </div>

            <!-- Teacher Login Form -->
            <div id="teacher-form" class="box form-box" style="display: none;">
                <header>Login Teacher</header>
                <form action="loginVerify.php" method="post">
                <input type="hidden" name="role" value="teacher">
                    <div class="field input">
                        <label for="teacher-username">Username</label>
                        <input type="text" name="username" id="teacher-username" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="teacher-password">Password</label>
                        <input type="password" name="password" id="teacher-password" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="captcha_input">Enter CAPTCHA</label>
                        <img src="captcha.php" alt="CAPTCHA Image" style="margin-bottom: 10px;">
                        <input type="text" name="captcha_input" id="captcha_input" autocomplete="off" required>
                    </div>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="LOGIN">
                    </div>
                    <div class="links">
                        Don't have account? <a href="verifyTeacher.php">Sign Up Now</a>
                    </div>
                </form>
                <button class="home-sci" onclick="showForm('student')">STUDENT</button>
                <p>&nbsp;</p>
                <button class="home-sci" onclick="showForm('admin')">ADMIN</button>
            </div>

            <!-- Student Login Form -->
            <div id="student-form" class="box form-box">
                <header>Login Student</header>
                <form action="loginVerify.php" method="post">
                    <input type="hidden" name="role" value="student">
                    <div class="field input">
                        <label for="student-no_ic">IC Number</label>
                        <input type="text" name="username" id="student-no_ic" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                    </div>
                    <div class="field input">
                        <label for="student-password">Password</label>
                        <input type="password" name="password" id="student-password" autocomplete="off" required>
                    </div>
                    <div class="field input">
                        <label for="captcha_input">Enter CAPTCHA</label>
                        <img src="captcha.php" alt="CAPTCHA Image" style="margin-bottom: 10px;">
                        <input type="text" name="captcha_input" id="captcha_input" autocomplete="off" required>
                    </div>
                    <div class="links">
                        <a href="ForgotPS.php">Forgot Password?</a>
                    </div>
                    <div class="field">
                        <input type="submit" class="btn" name="submit" value="LOGIN">
                    </div>
                    <p>&nbsp;</p>
                </form>
                <button class="home-sci" onclick="showForm('teacher')">TEACHER</button>
                <p>&nbsp;</p>
                <button class="home-sci" onclick="showForm('admin')">ADMIN</button>
                <p>&nbsp;</p>
                <div class="links">
                    Don't have account? <a href=" StudentNewAccount.php">Sign Up Now</a>
                </div>
            </div>
        </div>
    </div>

    <div class="home-content">
        <h3>HELLO, WELCOME TO</h3>
        <h1>SEKOLAH MENENGAH SAINS TAPAH</h1>
        <h3>SCHOOL REGISTRATION SYSTEM</h3>
        <h3><span class="text"></span></h3>
    </div>

    <video autoplay loop muted play-inline class="background-clip">
        <source src="../../image/bg1.mp4" type="video/mp4">
    </video>

    <script>
        function showForm(role) {
            document.getElementById('admin-form').style.display = 'none';
            document.getElementById('teacher-form').style.display = 'none';
            document.getElementById('student-form').style.display = 'none';

            if (role === 'admin') {
                document.getElementById('admin-form').style.display = 'block';
            } else if (role === 'teacher') {
                document.getElementById('teacher-form').style.display = 'block';
            } else if (role === 'student') {
                document.getElementById('student-form').style.display = 'block';
            }
        }
    </script>
</body>

</html>