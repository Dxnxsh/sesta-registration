<!DOCTYPE html>
<html lang="en">

<head>
    <title>Reset Password</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css">
    <style>
        body {
            background-image: url('../../image/bg11.jpeg');
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: 'Arial', sans-serif;
        }

        .container-fluid {
            margin-top: 50px;
        }

        .col-md-4 {
            text-align: center;
        }

        .error {
            color: red;
        }

        form {
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.2);
        }

        h2 {
            color: #333;
        }

        input[type="password"] {
            width: 100%;
            padding: 10px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        .btn-primary {
            background-color: #337ab7;
            color: #fff;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }

        .btn-primary:hover {
            background-color: #286090;
        }
    </style>
</head>

<body>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4"></div>
            <div class="col-md-4">
                <?php
                include('../config.php');

                $error = ""; // Initialize $error as an empty string
                
                if (isset($_GET["key"]) && isset($_GET["email"]) && isset($_GET["action"]) && ($_GET["action"] == "reset") && !isset($_POST["action"])) {
                    $key = $_GET["key"];
                    $email = $_GET["email"];
                    $curDate = date("Y-m-d H:i:s");
                    $query = mysqli_query($con, "SELECT * FROM `password_reset_temp` WHERE `key`='$key' and `email`='$email';");
                    $row = mysqli_num_rows($query);

                    if ($row == 0) {
                        $error .= '<h2 class="error">Invalid Link</h2>';
                    } else {
                        $row = mysqli_fetch_assoc($query);
                        $expDate = $row['expDate'];

                        if ($expDate >= $curDate) {
                            ?>

                            <form method="post" action="" name="update">
                                <h2>Reset Password</h2>
                                <input type="hidden" name="action" value="update" class="form-control" />
                                <div class="form-group">
                                    <label><strong>Enter New Password:</strong></label>
                                    <input type="password" name="pass1" class="form-control" />
                                </div>
                                <div class="form-group">
                                    <label><strong>Re-Enter New Password:</strong></label>
                                    <input type="password" name="pass2" class="form-control" />
                                </div>
                                <input type="hidden" name="email" value="<?php echo $email; ?>" />
                                <div class="form-group">
                                    <input type="submit" id="reset" value="Reset Password" class="btn btn-primary" />
                                </div>
                            </form>
                            <?php
                        } else {
                            $error .= "<h2>Link Expired</h2>";
                        }
                    }

                    if ($error != "") {
                        echo "<div class='error'>" . $error . "</div><br />";
                    }
                }

                if (isset($_POST["email"]) && isset($_POST["action"]) && ($_POST["action"] == "update")) {
                    $error = "";
                    $pass1 = $_POST["pass1"];
                    $pass2 = $_POST["pass2"];
                    $email = $_POST["email"];
                    $curDate = date("Y-m-d H:i:s");

                    if ($pass1 != $pass2) {
                        $error .= "<p class='error'>Passwords do not match. Both passwords should be the same.<br /><br /></p>";
                    }

                    if ($error != "") {
                        echo $error;
                    } else {
                        // Update the password in the database
                        mysqli_query($con, "UPDATE `student` SET `STUDENT_PWD`='$pass1' WHERE `STUDENT_EMAIL`='$email'");

                        // Remove the password reset entry
                        mysqli_query($con, "DELETE FROM `password_reset_temp` WHERE `email` = '$email'");

                        echo '<div class="error"><p>Congratulations! Your password has been updated successfully.</p>';
                        // Check if STUDENT_NAME is empty or null
                        $studentQuery = mysqli_query($con, "SELECT * FROM `student` WHERE `STUDENT_EMAIL`='$email'");
                        $studentRow = mysqli_fetch_assoc($studentQuery);

                        if (empty($studentRow['STUDENT_NAME']) || is_null($studentRow['STUDENT_NAME'])) {
                            header("Location: login.php"); // Redirect to login.php
                            exit();
                        } else {
                            echo "<a href='login.php'><button class='btn'>Go Back</button>";
                        }
                    }
                }
                ?>
            </div>
            <div class="col-md-4"></div>
        </div>
    </div>

</body>

</html>