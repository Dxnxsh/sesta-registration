<?php include 'indexHeader.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.7.1/css/all.min.css">
    <title>HOME</title>
    <link rel="stylesheet" href="indexStyle.css">
    <link rel="stylesheet" href="https://unpkg.com/aos@next/dist/aos.css" />
</head>
<body>
    <div class="welcome-row justify-content-center">
        <div class="welcome-header text-center">
            <h1 class="welcome-title">WELCOME TO<br>SESTA</h1>
        </div>
        <div class="welcome-links-container text-center mt-4">
            <p class="welcome-links">
                <span class="home-link" onclick="window.location.href='profile.php'">PROFILE</span>
                <span class="home-link" onclick="window.location.href='curriculum.php'">CURRICULUM</span>
                <span class="home-link" onclick="window.location.href='hem.php'">HEM</span>
                <span class="home-link" onclick="window.location.href='contactus.php'">CONTACT US</span>
            </p>
        </div>
        <div class="text-center mt-4">
            <a href="../php/login-logout/login.php" class="home-login-btn">LOG IN</a>
        </div>
    </div>
<?php include '../php/header/footer.php'; ?>
</body>
</html>