<?php
    $servername = "127.0.0.1:3306";
    $username = "root";
    $password = "";
    $db = "sesta_registration";

    $con = mysqli_connect($servername, $username, $password, $db);

    if (!$con) {
        die("Connection failed: " . mysqli_connect_error());
    }

    
    ?>

