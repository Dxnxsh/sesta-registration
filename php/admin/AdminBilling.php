<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}
?>
<?php include "../header/adminHeader.php" ?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <title>Billing Management</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <style>
        /* Page-specific styles for AdminBilling.php */
        /* Override container width for billing page - needs more space */
        .container {
            width: 90%;
        }

        .manage-buttons a.back-button {
            background-color: #007BFF;
            width: fit-content;
            margin-top: 30px;
        }

        /* Custom hover animation for this page */
        @keyframes buttonHover {
            0% {
                transform: translateY(0);
            }
            50% {
                transform: translateY(-5px);
            }
            100% {
                transform: translateY(0);
            }
        }

        .manage-buttons a:hover {
            animation: buttonHover 0.3s ease;
            opacity: 0.9;
        }

        /* Page-specific heading style */
        h1 {
            font-size: 40px;
            color: black;
            margin-bottom: 10px;
            text-align: center;
        }

        /* Custom search styling for this page */
        .search-container {
            position: relative;
            display: flex;
            align-items: center;
        }

        .search-container img {
            margin-right: 10px;
            cursor: pointer;
        }

        .selectSearch {
            margin-right: 10px;
        }

        #searchType {
            padding: 8px;
            border: 1px solid #ccc;
            border-radius: 5px;
            background-color: aliceblue;
            cursor: pointer;
        }

        #searchType::after {
            content: '\25BC';
            position: absolute;
            right: 8px;
            top: 50%;
            transform: translateY(-50%);
        }

        /* Override common searchBox style for this page */
        #searchBox {
            width: fit-content;
            padding: 8px;
            border: none;
            border-radius: 5px;
            background-color: aliceblue;
        }

        /* Custom submit button for this page */
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
            background-color: #45a049;
        }

        /* Unique billing page styles */
        .status-select {
            padding: 5px;
            border: 1px solid #ccc;
            border-radius: 4px;
            font-size: 12px;
            width: 100%;
        }

        .manage-buttons button {
            display: inline-block;
            background-color: #28A745;
            color: white;
            padding: 8px;
            text-align: center;
            text-decoration: none;
            font-size: 14px;
            border-radius: 4px;
            margin: 4px;
            transition: background-color 0.3s;
            border: none;
            cursor: pointer;
            width: fit-content;
        }

        .manage-buttons .download-button {
            background-color: #007BFF;
        }
    </style>
    <?php
    include("../config.php");

    // Handle search form submission
    $searchCondition = "";
    $searchType = isset($_GET['searchType']) ? $_GET['searchType'] : 'STUDENT_ID';

    if (isset($_GET['searchBox']) && !empty($_GET['searchBox'])) {
        $searchValue = $_GET['searchBox'];
        if ($searchType === 'STUDENT_ID') {
            $searchCondition = "WHERE STUDENT_ID LIKE '%$searchValue%'";
        } elseif ($searchType === 'PAYMENT_TYPE') {
            $searchCondition = "WHERE PAYMENT_TYPE LIKE '%$searchValue%'";
        } elseif ($searchType === 'PAYMENT_STATUS') {
            $searchCondition = "WHERE PAYMENT_STATUS LIKE '%$searchValue%'";
        }
    }

    $sql = "SELECT * FROM payment $searchCondition";
    $result = $con->query($sql);

    // Get student information for display
    $queryParent = mysqli_query($con, "SELECT payment.*, student.* FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");
    $studentInfo = array();
    while ($resultStud = mysqli_fetch_assoc($queryParent)) {
        $studentInfo[$resultStud['STUDENT_ID']] = $resultStud['STUDENT_NAME'];
    }
    ?>
</head>
</head>

<body>
    <div class="container">
        <form id="form2" name="form2" method="get">
            <h1>Billing Management</h1>
            <div class="search-container">
                <input name="searchBox" type="text" id="searchBox" placeholder="Search by Student ID" value="<?php echo isset($_GET['searchBox']) ? htmlspecialchars($_GET['searchBox']) : ''; ?>">
                <input name="submit" type="submit" id="submit" value="Search">
                <a class="reset-button" href="AdminBilling.php">Show All</a>
            </div>
        </form>
        <form id="form1" name="form1" method="post">
            <table width="163%">
            <thead>
                <tr>
                    <th>PAYMENT ID</th>
                    <th>STUDENT NAME</th>
                    <th>STUDENT IC</th>
                    <th>PAYMENT TYPE</th>
                    <th>PAYMENT AMOUNT</th>
                    <th>STATUS</th>
                    <th>UPDATE STATUS</th>
                    <th colspan="2">MANAGE</th>
                </tr>
            </thead>
            <tbody>
                <?php
                $num = mysqli_num_rows($result);
                if ($num > 0) {
                    while ($row = $result->fetch_assoc()) {
                        $file_path = "../../uploads/" . $row['PAYMENT_RECEIPT'];
                        $res_paymentType = $row['PAYMENT_TYPE'];
                        $res_paymentAmount = $row['PAYMENT_AMOUNT'];
                        $res_paymentID = $row['PAYMENT_ID'];
                        $res_paymentStatus = $row['PAYMENT_STATUS'];
                        $res_StudId = $row['STUDENT_ID'];

                        if (array_key_exists($res_StudId, $studentInfo)) {
                            $res_StudName = $studentInfo[$res_StudId];
                        } else {
                            $res_StudName = "Unknown";
                        }

                        $statusColor = 'red';
                        if ($res_paymentStatus == 'PENDING') {
                            $statusColor = 'orange';
                        } elseif ($res_paymentStatus == 'COMPLETED') {
                            $statusColor = 'green';
                        }

                        echo "
                        <tr>
                            <td>" . $res_paymentID . "</td>
                            <td>" . $res_StudName . "</td>
                            <td>" . $res_StudId . "</td>
                            <td>" . $res_paymentType . "</td>
                            <td>RM " . $res_paymentAmount . "</td>
                            <td style='text-align: center; color: " . $statusColor . ";'>" . $res_paymentStatus . "</td>
                            <td>
                                <select name='selectStatus' class='status-select' data-payment-id='" . $res_paymentID . "'>
                                    <option value='PENDING'" . (($res_paymentStatus == 'PENDING') ? ' selected' : '') . ">PENDING</option>
                                    <option value='COMPLETED'" . (($res_paymentStatus == 'COMPLETED') ? ' selected' : '') . ">COMPLETED</option>
                                    <option value='UNPAID'" . (($res_paymentStatus == 'UNPAID') ? ' selected' : '') . ">UNPAID</option>
                                </select>
                            </td>
                            <td class='manage-buttons'><button class='update-button' data-payment-id='" . $res_paymentID . "' onclick='updateFunction(this)'>UPDATE</button></td>";

                        // Check if file exists for download
                        if (!empty($row['PAYMENT_RECEIPT']) && file_exists($file_path)) {
                            echo "<td class='manage-buttons'><button class='download-button' onclick='downloadFile2(\"" . $file_path . "\")'>DOWNLOAD</button></td>";
                        } else {
                            echo "<td class='manage-buttons'><button class='download-button' onclick='downloadFile()'>DOWNLOAD</button></td>";
                        }

                        echo "</tr>";
                    }
                } else {
                    echo "
                    <tr>
                        <td colspan='9'>No billing records found.</td>
                    </tr>
                    ";
                }
                ?>
            </tbody>
        </table>
        </form>
        <div class='manage-buttons'><a class='back-button' href='Admin_home.php'>Back</a></div>
    </div>

    <script>
        function updateFunction(button) {
            var paymentID = button.getAttribute('data-payment-id');
            var selectedStatus = $(button).closest('tr').find('select[name="selectStatus"]').val();

            console.log("Payment ID: " + paymentID + ", Status: " + selectedStatus);

            // Make an AJAX request to update the payment status
            $.ajax({
                type: "POST",
                url: "update_payment_status.php",
                data: {
                    paymentID: paymentID,
                    status: selectedStatus
                },
                success: function(response) {
                    console.log('Response from server:', response);
                    var trimmedResponse = response.trim();

                    if (trimmedResponse === 'success') {
                        Swal.fire({
                            title: "Update!",
                            text: "Payment status updated successfully.",
                            icon: "success"
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: "Error!",
                            text: "Error updating payment status.",
                            icon: "error"
                        });
                    }
                },
                error: function(error) {
                    console.error('AJAX error:', error);
                    Swal.fire({
                        title: "Error!",
                        text: "Error updating payment status.",
                        icon: "error"
                    });
                }
            });
        }

        function downloadFile() {
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: 'File not found or payment receipt is empty.',
            });
        }

        function downloadFile2(file_path) {
            const Toast = Swal.mixin({
                toast: true,
                position: "top-end",
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
                didOpen: (toast) => {
                    toast.onmouseenter = Swal.stopTimer;
                    toast.onmouseleave = Swal.resumeTimer;
                }
            });
            Toast.fire({
                icon: "success",
                title: "Download successful"
            });

            // Create a hidden link and simulate a click to trigger the download
            const downloadLink = document.createElement("a");
            downloadLink.href = file_path;
            downloadLink.download = file_path.split("/").pop();
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>

</body>

</html>
<?php include "../header/footer.php" ?>
<?php
$con->close();
?>