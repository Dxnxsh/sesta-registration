<?php
session_start();

include("..\config.php");
if (!isset($_SESSION['valid'])) {
    header("Location: login.php");
}
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <title>Student Card</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --blue-rgb: 33 150 243;
            --background-rgb: 15 15 15;
        }

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            height: 100vh;
            display: grid;
            place-items: center;
            /*overflow: hidden;*/
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url("../../image/student_bg.png") no-repeat center center fixed;
            background-size: cover;
        }

        header {
            position: absolute;
            top: 0;
            width: 100%;
            /* You can use a specific width like 80% or 1200px */
            margin: 0 auto;
            /* Center the header horizontally */
            z-index: 1000;
            /* Adjust the value based on your needs */
        }

        .container {
            width: 1000px;
            height: 700px;
            background: linear-gradient(90deg, rgba(var(--background-rgb), 0.8) 0%, rgba(50, 50, 50, 1) 100%),
                url("../../image/student_bg.png") no-repeat center center fixed;
            background-size: cover;
            border: 2px solid #fff;
            border-radius: 20px;
            overflow: hidden;
            position: relative;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            margin: 20px;
        }

        .container-text {
            font: 700 3em/1 'Poppins', sans-serif;
            font-size: 24px;
            font-weight: bold;
            color: #fff;
            position: absolute;
            top: -50px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .title {
            font: 700 4em/1 'Poppins', sans-serif;
            font-size: 36px;
            font-weight: bold;
            color: #fff;
            position: absolute;
            top: 10px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 2;
        }

        .card-container {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            perspective: 1000px;
            z-index: 1;
        }

        .card {
            width: 360px;
            height: 510px;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: transform 1s;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            border: 5px solid #fff;
            border-radius: 15px;
            position: relative;
        }

        .card:hover {
            transform: rotateY(-180deg);
        }

        .card-inner {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 1s;
            background: linear-gradient(90deg, rgba(140, 82, 255, 0.8) 35%, rgba(92, 225, 230, 0.8) 100%);
            border: 2px solid #fff;
            border-radius: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            /* Decorative lines */
            position: relative;
        }

        .card-inner::before,
        .card-inner::after {
            content: '';
            position: absolute;
            width: 80%;
            height: 2px;
            background-color: #fff;
        }

        .card-inner::before {
            top: 20%;
        }

        .card-inner::after {
            top: 90%;
        }

        .logo {
            position: absolute;
            top: 10px;
            left: 10px;
            width: calc(100% - 20px);
            height: auto;
            border-radius: 10px;
            z-index: 1;
        }

        .middle-container {
            text-align: center;
            color: #fff;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            z-index: 1;
        }

        .text {
            font-size: 18px;
            color: #fff;
            white-space: nowrap;
        }
    </style>
</head>
<?php include("../config.php");

$id = $_SESSION['valid'];
$query = mysqli_query($con, "SELECT*FROM student WHERE STUDENT_ID=$id");
$queryClass  = mysqli_query($con, "SELECT student.*, class.* FROM student INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE
            WHERE student.STUDENT_ID = $id");

// Initialize $res_Class with a default value
$res_Class = '';
while ($result = mysqli_fetch_assoc($query)) {
    $res_IC = $result['STUDENT_ID'];
    $res_Name = $result['STUDENT_NAME'];
    $res_DOB = $result['STUDENT_DOB'];
}

while ($result = mysqli_fetch_assoc($queryClass)) {
    $res_Class = $result['CLASS_NAME'];
}

if ($res_Class == '') {
    $res_Class = 'not assigned';
}

?>

<body>
    <header>
        <?php include "../header/studentHeader.php" ?>
    </header>



    <div class="container">
        <div class="container-text">STUDENT CARD</div>
        <div class="title">STUDENT CARD</div>

        <div class="card-container">
            <div class="card">
                <div class="card-inner">
                    <div class="logo">
                        <img src="../../image/icon/logoSESTA2.png" alt="Logo"
                            style="width: 100%; height: auto; border-radius: 10px;">
                    </div>
                    <div class="middle-container">
                        <div class="text"><?php echo $res_Name ?></div>
                        <div class="text"><?php echo $res_IC ?></div>
                        <div class="text">Class <?php echo $res_Class ?></div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <svg id="h1" viewBox="0 0 1000 1000"
        style="position: absolute; height: 100%; width: 100%; top: 50%; left: 50%; transform: translate(-50%, -50%); cursor: pointer;">
        <defs>
            <filter id='blur' color-interpolation-filters="sRGB">
                <feDropShadow dx="5" dy="5" stdDeviation="5" flood-opacity="0.5" />
            </filter>
        </defs>
    </svg>
    <footer><?php include "../header/footer.php" ?></footer>
</body>


</html>