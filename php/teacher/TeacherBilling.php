<?php 
session_start();
if (!isset($_SESSION['validTC'])) {
    header("Location: ../login-logout/login.php");
    exit;
}

include "../header/teacherHeader.php"; 
include("../config.php");

$teacherId = $_SESSION['validTC'];
$sql = "";

// Handle search
if (isset($_GET['submit'])) {
    $searchOption = $_GET['searchOption'] ?? '';
    $searchTerm = $_GET['searchBox'] ?? '';

    if (!empty($searchTerm)) {
        if ($searchOption == 'name') {
            $sql = "SELECT * FROM payment 
                    INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                    INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                    WHERE class.TEACHER_ID = '$teacherId' 
                    AND student.STUDENT_NAME LIKE '%$searchTerm%' 
                    AND STUDENT_NAME IS NOT NULL";
        } elseif ($searchOption == 'ic') {
            $sql = "SELECT * FROM payment 
                    INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
                    INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
                    WHERE class.TEACHER_ID = '$teacherId' 
                    AND student.STUDENT_ID LIKE '%$searchTerm%' 
                    AND STUDENT_NAME IS NOT NULL";
        }
    }
}

// Default query if no search or empty
if (empty($sql)) {
    $sql = "SELECT * FROM payment 
            INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID 
            INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE 
            WHERE class.TEACHER_ID = '$teacherId' 
            AND STUDENT_NAME IS NOT NULL";
}

$result = $con->query($sql);
if (!$result) {
    die("Error in SQL query: " . $con->error);
}

// Get student name mappings
$queryParent = mysqli_query($con, "SELECT payment.*, student.* 
                                   FROM payment 
                                   INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");

$studentInfo = [];
while ($resultStud = mysqli_fetch_assoc($queryParent)) {
    $studentInfo[$resultStud['STUDENT_ID']] = $resultStud['STUDENT_NAME'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Billings</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Optional CSS libraries -->
    <link rel="stylesheet" href="../../css/AB.css">
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>

    <style>
        body {
            font-family: "Poppins", sans-serif;
            background-image: url("../../image/teacher.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 60px auto;
            background-color: #fff;
            padding: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 40px;
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        form {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        select, input[type="text"] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: aliceblue;
        }

        input[type="submit"], .reset-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 8px 16px;
            font-size: 15px;
            border-radius: 10px;
            cursor: pointer;
            text-decoration: none;
        }

        input[type="submit"]:hover,
        .reset-button:hover {
            background-color: #45a049;
        }

        .reset-button {
            background-color: #007BFF;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        th {
            background-color: #04AA6D;
            color: white;
        }

        tr:nth-child(even) {
            background-color: #f2f2f2;
        }

        td b {
            font-weight: bold;
        }
    </style>
</head>

<body>
<div class="container">
    <h1>View Billing</h1>
    <form action="TeacherBilling.php" method="get">
        <select name="searchOption" id="searchOption">
            <option value="name">Search by Name</option>
            <option value="ic">Search by IC</option>
        </select>
        <input name="searchBox" type="text" id="searchBox" placeholder="Enter search term">
        <input name="submit" type="submit" id="submit" value="Search">
        <a href="TeacherBilling.php" class="reset-button">Show All</a>
    </form>

    <table>
        <thead>
        <tr>
            <th>PAYMENT ID</th>
            <th>STUDENT NAME</th>
            <th>STUDENT IC</th>
            <th>PAYMENT TYPE</th>
            <th>PAYMENT AMOUNT</th>
            <th>PAYMENT STATUS</th>
        </tr>
        </thead>
        <tbody>
        <?php
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $res_paymentID = $row['PAYMENT_ID'];
                $res_StudId = $row['STUDENT_ID'];
                $res_StudName = $studentInfo[$res_StudId] ?? "Unknown";
                $res_paymentType = $row['PAYMENT_TYPE'];
                $res_paymentAmount = $row['PAYMENT_AMOUNT'];
                $res_paymentStatus = $row['PAYMENT_STATUS'];

                $color = match($res_paymentStatus) {
                    'UNPAID' => 'red',
                    'PENDING' => 'orange',
                    default => 'green'
                };

                echo "
                    <tr>
                        <td>$res_paymentID</td>
                        <td>$res_StudName</td>
                        <td>$res_StudId</td>
                        <td>$res_paymentType</td>
                        <td>RM $res_paymentAmount</td>
                        <td style='color:$color'><b>$res_paymentStatus</b></td>
                    </tr>
                ";
            }
        } else {
            echo "
                <tr>
                    <td colspan='6'>No records found.</td>
                </tr>
            ";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>

<?php 
include "../header/footer.php"; 
$con->close();
?>
