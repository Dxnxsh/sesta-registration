<?php
    $servername = "mysql-9b0e5b0-mysql-d231.c.aivencloud.com:17433";
    $username = "avnadmin";
    $password = "AVNS_I5PTmFPn-dMZg0KHVOz";
    $db = "sesta_registration";

    $con = mysqli_connect($servername, $username, $password, $db);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    
    ?>

