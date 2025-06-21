<?php
    $servername = "mysql-9b0e5b0-mysql-d231.c.aivencloud.com:17433";
    $username = "avnadmin";
    $password = "AVNS_I5PTmFPn-dMZg0KHVOz";
    $db = "sesta_registration";

    $con = mysqli_connect($servername, $username, $password, $db);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    // Include sanitization library for security
    require_once __DIR__ . '/config/sanitization.php';
    
    // Initialize SecuritySanitizer with database connection
    SecuritySanitizer::init($con);
    
    // Start session securely
    if (session_status() === PHP_SESSION_NONE) {
        // Set secure session parameters
        ini_set('session.cookie_httponly', 1);
        ini_set('session.cookie_secure', 1);
        ini_set('session.use_strict_mode', 1);
        session_start();
    }
    
    ?>

