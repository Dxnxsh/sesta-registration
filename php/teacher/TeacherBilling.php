<?php
session_start();
include("../config.php");
if (!isset($_SESSION['validTC'])) {
    header("Location: ../login-logout/login.php");
}

include "../header/teacherHeader.php";

$teacherId = $_SESSION['validTC'];
$sql = "";

if (isset($_GET['submit'])) {
    $searchOption = isset($_GET['searchOption']) ? $_GET['searchOption'] : '';
    $searchTerm = isset($_GET['searchBox']) ? $_GET['searchBox'] : '';

    if (!empty($searchTerm)) {
        if ($searchOption == 'name') {
            $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE WHERE class.TEACHER_ID = '$teacherId' AND student.STUDENT_NAME LIKE '%$searchTerm%' AND STUDENT_NAME IS NOT NULL";
        } elseif ($searchOption == 'ic') {
            $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE WHERE class.TEACHER_ID = '$teacherId' AND student.STUDENT_ID LIKE '%$searchTerm%' AND STUDENT_NAME IS NOT NULL";
        }
    }
}

if (empty($sql)) {
    $sql = "SELECT * FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE WHERE class.TEACHER_ID = '$teacherId' AND STUDENT_NAME IS NOT NULL";
}

$result = $con->query($sql);
if (!$result) {
    die("Error in SQL query: " . $con->error);
}

$queryParent = mysqli_query($con, "SELECT payment.*, student.* FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");
$studentInfo = [];
while ($resultStud = mysqli_fetch_assoc($queryParent)) {
    $studentInfo[$resultStud['STUDENT_ID']] = $resultStud['STUDENT_NAME'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Billing</title>
    <style>
        body {
            background-image: url("../../image/teacher.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 100px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h1 {
            font-size: 40px;
            text-align: center;
            color: black;
        }

        form {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 20px;
        }

        select, input[type=text], input[type=submit] {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }

        input[type=submit] {
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }

        input[type=submit]:hover {
            background-color: #45a049;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
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
    </style>
</head>
<body>
    <div class="container">
        <h1>View Billing</h1>
        <form method="get">
            <select name="searchOption">
                <option value="name">Search by Name</option>
                <option value="ic">Search by IC</option>
            </select>
            <input type="text" name="searchBox" placeholder="Enter search term">
            <input type="submit" name="submit" value="Search">
        </form>

        <table>
            <thead>
                <tr>
                    <th>PAYMENT ID</th>
                    <th>STUDENT NAME</th>
                    <th>STUDENT IC</th>
                    <th>PAYMENT TYPE</th>
                    <th>AMOUNT</th>
                    <th>STATUS</th>
                </tr>
            </thead>
            <tbody>
                <?php
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $file_path = "../image/" . $row['PAYMENT_RECEIPT'];
                        $res_paymentType = $row['PAYMENT_TYPE'];
                        $res_paymentAmount = $row['PAYMENT_AMOUNT'];
                        $res_paymentID = $row['PAYMENT_ID'];
                        $res_paymentStatus = $row['PAYMENT_STATUS'];
                        $res_StudId = $row['STUDENT_ID'];

                        $res_StudName = $studentInfo[$res_StudId] ?? "Unknown";

                        echo "<tr>",
                             "<td>$res_paymentID</td>",
                             "<td>$res_StudName</td>",
                             "<td>$res_StudId</td>",
                             "<td>$res_paymentType</td>",
                             "<td>RM $res_paymentAmount</td>",
                             "<td style='color: " . ($res_paymentStatus == 'UNPAID' ? 'red' : ($res_paymentStatus == 'PENDING' ? 'orange' : 'green')) . "'><b>$res_paymentStatus</b></td>",
                             "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='6'>No records found.</td></tr>";
                }
                ?>
            </tbody>
        </table>
    </div>
</body>
</html>
<?php include "../header/footer.php"; ?>
<?php $con->close(); ?>
