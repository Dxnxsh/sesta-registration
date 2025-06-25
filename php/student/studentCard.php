<?php
session_start();

include("../config.php");
if (!isset($_SESSION['valid'])) {
    header("Location: ../login-logout/login.php");
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
        }

        body {
            padding: 0;
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background: url("../../image/student_bg.png") no-repeat center center fixed;
            background-size: cover;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
            position: relative;
        }

        header {
            position: relative;
            z-index: 10;
        }

        .main-content {
            flex: 1;
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
            z-index: 1;
        }

        .title-text {
            margin-top: 30px;
            font-size: 28px;
            font-weight: bold;
            color: white;
        }

        .container {
            width: 1000px;
            height: 700px;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
            position: relative;
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .card-container {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            perspective: 1000px;
        }

        .card {
            width: 360px;
            height: 510px;
            cursor: pointer;
            transform-style: preserve-3d;
            transition: transform 1s;
            border-radius: 15px;
            position: relative;
        }

        .card:hover {
            transform: rotateY(180deg);
        }

        .card-inner {
            width: 100%;
            height: 100%;
            position: relative;
            transform-style: preserve-3d;
            transition: transform 1s;
            border-radius: 15px;
        }

        .card-face {
            position: absolute;
            width: 100%;
            height: 100%;
            backface-visibility: hidden;
            border-radius: 15px;
            padding: 20px;
            color: #000;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
        }

        .card-front {
            background: url('../../image/studentCardFront.png') no-repeat center center;
            background-size: cover;
        }

        .card-back {
            background: url('../../image/studentCardBack.png') no-repeat center center;
            background-size: cover;
            transform: rotateY(180deg);
            justify-content: flex-start;
            padding-top: 100px;
            align-items: flex-start;
        }

        .student-photo {
            width: 120px;
            height: 160px;
            object-fit: cover;
            border-radius: 10px;
            border: 2px solid white;
            position: absolute;
            top: 120px;
        }

        .front-bottom {
            position: absolute;
            top: 60%;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .text {
            margin: 2px 20px;
            text-align: left;
            color: black;
        }

        .text.front-name {
            font-size: 16px;
            font-weight: bold;
            text-align: center;
        }

        .text.back-detail {
            font-size: 8px;
            margin: 2px 20px;
        }

        .text b {
            font-weight: bold;
        }
    </style>
</head>

<?php 
    include("../config.php");

    $id = $_SESSION['valid'];
    $query = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID = $id");

    while ($result = mysqli_fetch_assoc($query)) {
        $res_IC = $result['STUDENT_ID'];
        $res_Name = $result['STUDENT_NAME'];
        $res_DOB = $result['STUDENT_DOB'];
        $res_Email = $result['STUDENT_EMAIL'];
        $res_Gender = $result['STUDENT_GENDER'];
        $res_Religion = $result['STUDENT_RELIGION'];
        $res_Race = $result['STUDENT_RACE'];
        $res_Nationality = $result['STUDENT_NATIONALITY'];
        $res_Face = $result['STUDENT_FACE'];
    }
?>

<body>
    <header>
        <?php include "../header/studentHeader.php" ?>
    </header>

    <div class="main-content">
        <div class="title-text">Student Card</div>
        <div class="container">
            <div class="card-container">
                <div class="card">
                    <div class="card-inner">
                        <!-- Front Side -->
                        <div class="card-face card-front">
                            <img src="../../image/student_face/<?php echo $res_Face; ?>" alt="Student Face" class="student-photo">
                            <div class="front-bottom">
                                <div class="text front-name"><?php echo $res_Name; ?></div>
                                <div style="margin-top: 10px;">
                                    <img alt="Barcode" src="https://barcode.tec-it.com/barcode.ashx?data=<?php echo urlencode($res_IC); ?>&translate-esc=on" style="width: 180px; height: 50px;">
                                </div>
                            </div>
                        </div>

                        <!-- Back Side -->
                        <div class="card-face card-back">
                            <div class="text back-detail"><b>Name:</b> <?php echo $res_Name; ?></div>
                            <div class="text back-detail"><b>ID:</b> <?php echo $res_IC; ?></div>
                            <div class="text back-detail"><b>Gender:</b> <?php echo $res_Gender; ?></div>
                            <div class="text back-detail"><b>DOB:</b> <?php echo $res_DOB; ?></div>
                            <div class="text back-detail"><b>Email:</b> <?php echo $res_Email; ?></div>
                            <div class="text back-detail"><b>Religion:</b> <?php echo $res_Religion; ?></div>
                            <div class="text back-detail"><b>Race:</b> <?php echo $res_Race; ?></div>
                            <div class="text back-detail"><b>Nationality:</b> <?php echo $res_Nationality; ?></div>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <?php include "../header/footer.php" ?>
    </footer>
</body>
</html>