<?php
session_start();
include "../header/adminHeader.php";
require_once('../config.php');

if (!function_exists("GetSQLValueString")) {
    function GetSQLValueString($value, $type, $definedValue = "", $notDefinedValue = "") {
        if (PHP_VERSION < 6) {
            $value = get_magic_quotes_gpc() ? stripslashes($value) : $value;
        }

        $value = function_exists("mysql_real_escape_string") ? mysql_real_escape_string($value) : mysql_escape_string($value);

        switch ($type) {
            case "text":
                $value = ($value != "") ? "'" . $value . "'" : "NULL"; break;
            case "long":
            case "int":
                $value = ($value != "") ? intval($value) : "NULL"; break;
            case "double":
                $value = ($value != "") ? doubleval($value) : "NULL"; break;
            case "date":
                $value = ($value != "") ? "'" . $value . "'" : "NULL"; break;
            case "defined":
                $value = ($value != "") ? $definedValue : $notDefinedValue; break;
        }
        return $value;
    }
}

$searchTerm = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['submit']) && !empty($_POST['searchBox'])) {
        $searchTerm = trim($_POST['searchBox']);
        $query_rsTeacher = "SELECT * FROM teacher WHERE TEACHER_ID LIKE '%" . mysqli_real_escape_string($con, $searchTerm) . "%'";
    } else {
        $query_rsTeacher = "SELECT * FROM teacher"; // Show all
    }
} else {
    $query_rsTeacher = "SELECT * FROM teacher";
}

$rsTeacher = mysqli_query($con, $query_rsTeacher) or die(mysqli_error($con));
$row_rsTeacher = null;
$totalRows_rsTeacher = mysqli_num_rows($rsTeacher);
if ($totalRows_rsTeacher > 0) {
    $row_rsTeacher = mysqli_fetch_assoc($rsTeacher);
}
?>

<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Teacher Full Report</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
    <style>
        body {
            background-image: url("../../image/admin.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            font-family: "Poppins", sans-serif;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 96%; 
            margin: 30px auto;
            background-color: #fff;
            padding: 20px;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }


        h2 {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(to right, #7b4397 0%, #dc2430  51%, #7b4397  100%);
            font-size: 40px;
            color: white;
            text-align: center;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .h2-ct {
            display: flex;
            align-items: center;
            justify-content: center;
            background-image: linear-gradient(to right, #0f3443 0%, #34e89e  51%, #0f3443  100%);
            font-size: 30px;
            color: white;
            text-align: center;
            padding: 10px 20px;
            border-radius: 5px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .button-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .nav-links {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
        }

        .nav-links a {
            text-decoration: none;
            color: #fff;
            font-size: 18px;
            width: 100%;
            padding: 10px;
            transition: background-color 0.3s;
            text-align: center;
        }

        .nav-links a.student {
            background-color: #008CBA;
        }

        .nav-links a.parent {
            background-color:  #FF6600;
        }

        .nav-links a.teacher {
            background-color: #4CAF50;
        }

        .nav-links a.student:hover {
            background-color: #0098E2;
        }

        .nav-links a.parent:hover {
            background-color: #E05900;
        }

        .nav-links a.teacher:hover {
            background-color: #45a049;
        }

        .search-container {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: flex-end;
            margin-top: 20px;
        }
        #searchBox {
            width: fit-content;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: aliceblue;
        }
        #submit {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 7px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }
        #submit:hover {
            background-color: #388e3c;
        }
        .reset-button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 7px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            cursor: pointer;
            border-radius: 10px;
            margin-left: 5px;
        }
        .reset-button:hover {
            background-color: #388e3c;
        }
        .search-section, .search-form, .show-all-form, .show-all-button, .search-input, .search-button { display: none !important; }

        table {
            margin-top: 20px;
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            border: 1px solid grey;
        }

        th, td {
            padding: 4px;
            text-align: center;
            word-wrap: break-word;
            border: 1px solid grey; /* cell borders */
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-size: 10px;
        }

        .tr-hover:hover {
            background-color: #f5f5f5;
        }

        .proceed-payment-button {
            all: unset;
            width: 50px;
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
            background-color: #4CAF50;
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

        @media print {

            body {
                visibility: hidden; /* Hide everything */
            }

            .container, .container * {
                visibility: visible; /* Only show container */
                margin: 0;
                padding: 0;
            }

            .search-section * {
                display: none !important;
            }

            .button-container * {
                display: none !important;
            }

            
            .nav-links * {
                visibility: hidden;
            }

            .container {
                width: 100%;
                box-shadow: none; /* Optional: remove shadow */
                border: none; /* Optional: remove border */
            }
        }
    </style>
</head>

<body>
<div class="container">
    <form id="form1" name="form1" method="post">
        <h2>FULL REPORT</h2>
        <h2 class="h2-ct">TEACHER</h2>
        <div class="nav-links">
            <a href="studentFullReport.php" class="student"><b>STUDENT</b></a>
            <a href="parentFullReport.php" class="parent"><b>PARENT</b></a>
            <a href="teacherFullReport.php" class="teacher"><b>TEACHER</b></a>
        </div>
    </form>

    <div class="search-container">
        <form id="form2" name="form2" method="post" style="display: flex; align-items: center;">
            <input name="searchBox" type="text" id="searchBox" placeholder="Search by Teacher ID" value="<?php echo isset($_POST['searchBox']) ? htmlspecialchars($_POST['searchBox']) : ''; ?>">
            <input name="submit" type="submit" id="submit" value="Search">
            <a class="reset-button" href="teacherFullReport.php">Show All</a>
        </form>
    </div>

    <form id="form3" name="form3" method="post">
        <table>
            <tbody>
                <tr>
                    <th>ID</th>
                    <th>NAME</th>
                    <th>GENDER</th>
                    <th>DOB</th>
                    <th>ADDRESS</th>
                    <th>PHONE NO.</th>
                    <th>EMAIL</th>
                    <th>STATUS</th>
                    <th>USERNAME</th>
                    <th>PASSWORD</th>
                </tr>
                <?php if ($totalRows_rsTeacher > 0): ?>
                    <?php do { ?>
                        <tr class="tr-hover">
                        <td><?php echo $row_rsTeacher['TEACHER_ID']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_NAME']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_GENDER']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_DOB']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_ADDRESS']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_PHONENUM']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_EMAIL']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_STATUS']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_USERNAME']; ?></td>
                        <td><?php echo $row_rsTeacher['TEACHER_PWD']; ?></td>
                        </tr>
                    <?php } while ($row_rsTeacher = mysqli_fetch_assoc($rsTeacher)); ?>
                <?php else: ?>
                    <tr>
                        <td colspan="13" style="color: red; font-weight: bold;">No record found</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
    </form>

    <div class="button-container">
        <button type="button" class="proceed-payment-button" onclick="window.location.href = 'Admin_home.php';">
            <i class="fas fa-arrow-left"></i> Back
        </button>
        <button type="button" class="proceed-payment-button" onclick="window.print()">
            <i class="fas fa-arrow-left"></i> 🖨 Print
        </button>
    </div>
</div>
</body>
</html>
<?php mysqli_free_result($rsTeacher);?>
<?php include "../header/footer.php" ?>