<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
}
?>
<?php include "../header/adminHeader.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css"
        integrity="sha512-...." crossorigin="anonymous" />
    <title>System Summary</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../../image/bg5.jpeg");
            background-repeat: no-repeat;
            background-size: cover;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 50%;
            margin-top: 30px;
            margin-left: 320px;
            text-align: center;
            background-color: rgba(255, 255, 255, 0.8);
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        table {
            border-collapse: separate;
            border-spacing: 10px;
            /* Adjust the spacing between cells */
            width: 100%;
        }

        th,
        td {
            padding: 15px;
            text-align: left;
            color: #333;
            border-radius: 0;
            /* Remove border-radius for squared corners */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            /* Add shadow to each cell */
        }

        th {
            width: 50%;
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background-color: #E51061;
            color: #fff;
        }

        .th-ct {
            font-weight: bold;
            font-size: 16px;
            text-transform: uppercase;
            letter-spacing: 0.03em;
            background-color: black;
            color: #fff;
        }

        td {
            background-color: #ffffff;
        }

        /* Add border to each table row */
        tr {
            border: 2px solid #ddd;
            border-radius: 10px;
        }

        /* Add shadow to each table row */
        tr {
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        /* Remove border and shadow for specific cells */
        th:nth-child(1),
        th:nth-child(2),
        td:nth-child(1),
        td:nth-child(2) {
            border-right: 1px solid #ddd;
        }

        .additional-info {
            display: none;
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            box-sizing: border-box;
            padding: 10px;
        }

        tr:hover+.additional-info {
            display: table-row;
        }

        h2 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(to right, #7b4397 0%, #dc2430 51%, #7b4397 100%);
            color: #fff;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        i {
            margin-right: 10px;
        }

        .button-container {
            display: flex;
            justify-content: center;
            /* Center the buttons horizontally */
            align-items: center;
            /* Center the buttons vertically */
        }

        /* Button styling */
        .proceed-payment-button {
            all: unset;
            width: 100px;
            height: 30px;
            font-size: 16px;
            color: #f0f0f0;
            cursor: pointer;
            z-index: 1;
            padding: 10px 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            white-space: nowrap;
            user-select: none;
            -webkit-user-select: none;
            touch-action: manipulation;
            position: relative;
            margin: 20px;
            /* Center the button horizontally within its container */
        }

        .proceed-payment-button::after,
        .proceed-payment-button::before {
            content: '';
            position: absolute;
            bottom: 0;
            right: 0;
            z-index: -99999;
            transition: all .4s;
        }

        .proceed-payment-button::before {
            transform: translate(0%, 0%);
            width: 100%;
            height: 100%;
            background: #233858;
            border-radius: 10px;
            background-image: linear-gradient(to right, #7b4397 0%, #dc2430 51%, #7b4397 100%);
        }

        .proceed-payment-button::after {
            transform: translate(10px, 10px);
            width: 35px;
            height: 35px;
            background: #ffffff15;
            backdrop-filter: blur(5px);
            -webkit-backdrop-filter: blur(5px);
            border-radius: 50px;
        }

        .proceed-payment-button:hover::before {
            transform: translate(5%, 20%);
            width: 110%;
            height: 110%;
        }

        .proceed-payment-button:hover::after {
            border-radius: 10px;
            transform: translate(0, 0);
            width: 100%;
            height: 100%;
        }

        .proceed-payment-button:active::after {
            transition: 0s;
            transform: translate(0, 5%);
        }
    </style>
</head>

<body>
    <div class="container">
        <?php
       include("../config.php");
        $query = mysqli_query($con, "SELECT * FROM student");
        $query2 = mysqli_query($con, "SELECT * FROM teacher");
        $query3 = mysqli_query($con, "SELECT * FROM class");
        $query4 = mysqli_query($con, "SELECT * FROM payment");

        if ($query) {
            $rowCount = mysqli_num_rows($query);
        } else {
            echo "Error in the query: " . mysqli_error($con);
        }

        if ($query2) {
            $rowCount2 = mysqli_num_rows($query2);
        } else {
            echo "Error in the query: " . mysqli_error($con);
        }

        if ($query3) {
            $rowCount3 = mysqli_num_rows($query3);
        } else {
            echo "Error in the query: " . mysqli_error($con);
        }


        $rowCount4 = 0;
        $rowCount5 = 0;
        while ($result = mysqli_fetch_assoc($query2)) {
            $res_gender = $result['TEACHER_GENDER'];

            if ($res_gender == "Male") {
                $rowCount4++;
            }
            if ($res_gender == "Female") {
                $rowCount5++;
            }
        }

        // Counting the number of students in level 1 and level 4
        $rowCount6 = 0;
        $rowCount7 = 0;
        while ($result = mysqli_fetch_assoc($query)) {
            $res_level = $result['STUDENT_LEVEL'];

            if ($res_level == "1") {
                $rowCount6++;
            }
            if ($res_level == "4") {
                $rowCount7++;
            }
        }

        $totalAmount8 = 0;
        $totalAmount9 = 0;

        while ($result = mysqli_fetch_assoc($query4)) {
            $res_level = $result['PAYMENT_STATUS'];
            $res_payment_amount = $result['PAYMENT_AMOUNT'];

            if ($res_level == "UNPAID") {
                $totalAmount8 += $res_payment_amount;  // Add payment amount for UNPAID
            }

            if ($res_level == "COMPLETED") {
                $totalAmount9 += $res_payment_amount;  // Add payment amount for COMPLETED
            }
        }
        ?>
        <div class="logo">
            <img src="..\..\image\sesta.png" alt="Logo">
        </div>
        <h2>
            <i class="fas fa-chart-pie"></i> SYSTEM SUMMARY
        </h2>
        <table>
            <tr>
                <th>Total Student</th>
                <td><b>
                        <?php echo $rowCount ?> Student</td>
            </tr>
            <tr>
                <th class="th-ct">From 1</th>
                <td><b>
                        <?php echo $rowCount6 ?> Student</td>
            </tr>
            <tr>
                <th class="th-ct">Form 4</th>
                <td><b>
                        <?php echo $rowCount7 ?> Student</td>
            </tr>
            <tr>
                <th>Total Teacher</th>
                <td><b>
                        <?php echo $rowCount2 ?> Teacher</td>
            </tr>
            <tr>
                <th class="th-ct"> Male</th>
                <td><b>
                        <?php echo $rowCount4 ?> Teacher</td>
            </tr>
            <tr>
                <th class="th-ct">Female</th>
                <td><b>
                        <?php echo $rowCount5 ?> Teacher</td>
            </tr>
            <tr>
                <th>Total Class</th>
                <td><b>
                        <?php echo $rowCount3 ?> Class</td>
            </tr>
            <tr>
                <th>Total Paid Payment</th>
                <td><b>RM
                        <?php echo $totalAmount9 ?>
                </td>
            </tr>
            <tr>
                <th>Total Unpaid Payment</th>
                <td><b>RM
                        <?php echo $totalAmount8 ?>
                </td>
            </tr>
        </table>
        <div class="button-container">
            <button type="button" class="proceed-payment-button" onclick="window.location.href = 'admin_home.php';">
                <i class="fas fa-arrow-left"></i> Back
            </button>
            <a href="pdf_summary.php?&ACTION=VIEW" class="proceed-payment-button" target="_blank"><i
                    class="fa fa-file-pdf-o"></i> 🖨 Print</a>
        </div>
    </div>
</body>
<?php include "../header/footer.php" ?>
</html>