<?php
session_start();
include("../config.php");

if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Sanitize admin session ID
$adminId = SecuritySanitizer::sanitize($_SESSION['adminID'], 'id', 'ADMIN_ID');
if (!$adminId) {
    SecuritySanitizer::logSecurityEvent('Invalid admin session ID in AdminBilling.php', 'HIGH');
    header("Location: ../login-logout/login.php");
    exit();
}
?>
<?php include "../header/adminHeader.php" ?>
<?php
// Handle search form submission with proper sanitization
if (isset($_GET['submit'])) {
    $searchId = isset($_GET['searchBox']) ? trim($_GET['searchBox']) : '';
    
    if (!empty($searchId)) {
        // Sanitize and validate student ID
        $searchId = SecuritySanitizer::sanitize($searchId, 'id', 'STUDENT_ID');
        
        if ($searchId) {
            // Use prepared statement for search
            $stmt = $con->prepare("SELECT * FROM payment WHERE STUDENT_ID = ?");
            $stmt->bind_param("s", $searchId);
            $stmt->execute();
            $result = $stmt->get_result();
            $isSearch = true;
            SecuritySanitizer::logSecurityEvent("Admin $adminId searched for student payments: $searchId", 'INFO');
        } else {
            SecuritySanitizer::logSecurityEvent("Invalid student ID search attempted by admin $adminId", 'MEDIUM');
            // Set empty result for invalid search
            $result = $con->query("SELECT * FROM payment WHERE 1=0"); // Empty result
            $isSearch = true;
        }
    } else {
        // If search box is empty, retrieve all data
        $result = $con->query("SELECT * FROM payment");
        $isSearch = false;
    }
} else {
    // Default query without search
    $result = $con->query("SELECT * FROM payment");
    $isSearch = false;
}

?>
<!doctype html>

<html>

<head>
    <meta charset="UTF-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" type="text/css" href="../../css/AB.css" />
    <link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
    <script src="https://code.jquery.com/jquery-3.7.0.js"></script>
    <script src="https://code.jquery.com/jquery-migrate-3.4.1.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.1/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.all.min.js"></script>
    <title>Billings</title>
    <script>
        function updateFunction(button) {
            var paymentID = button.getAttribute('data-payment-id');
            var selectedStatus = $(button).closest('tr').find('select[name="selectStatus"]').val();

            console.log("Payment ID: " + paymentID + ", Status: " + selectedStatus);

            // Make an AJAX request to update the payment status
            $.ajax({
                type: "POST",
                url: "update_payment_status.php", // Change this to the actual PHP file handling the update
                data: {
                    paymentID: paymentID,
                    status: selectedStatus
                },
                success: function (response) {
                    console.log('Response from server:', response);

                    // Trim the response to remove leading/trailing whitespaces
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
                error: function (error) {
                    console.error('AJAX error:', error);
                    alert('Error updating payment status');
                }
            });
        }



        function downloadFile() {

            // Show an error message using SweetAlert2
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
            downloadLink.download = file_path.split("/").pop(); // Set the filename
            document.body.appendChild(downloadLink);
            downloadLink.click();
            document.body.removeChild(downloadLink);
        }
    </script>
</head>

<body>

    <?php include("../config.php");

    // Use prepared statement for joining payment and student data
    $stmt = $con->prepare("SELECT payment.*, student.* FROM payment INNER JOIN student ON payment.STUDENT_ID = student.STUDENT_ID");
    $stmt->execute();
    $queryParent = $stmt->get_result();

    $studentInfo = [];
    while ($resultStud = $queryParent->fetch_assoc()) {
        $studentInfo[$resultStud['STUDENT_ID']] = SecuritySanitizer::sanitize($resultStud['STUDENT_NAME'], 'name');
    }
    $stmt->close();
    ?>
    <div class="container">  
        <h1>Manage Billings</h1>
        <form action="" method="get">
            <p><input name="searchBox" type="text" id="searchBox" placeholder="Search by student id">
                <input name="submit" type="submit" id="submit" formaction="AdminBilling.php" value="Search">
            </p>
        </form>

    </div>

    <div class="container2">
        <table width="90%" border="1" cellspacing="5">
            <tbody>
                <tr class="tr-color">
                    <td width="13%">PAYMENT ID</td>
                    <td width="19%">STUDENT NAME</td>
                    <td width="15%">STUDENT IC</td>
                    <td width="17%">PAYMENT TYPE</td>
                    <td width="10%">PAYMENT AMOUNT</td>
                    <td width="10%">TYPE</td>
                    <td width="25%">UPDATE STATUS </td>
                    <td colspan="2">MANAGE</td>
                </tr>
                <?php
                // Display the uploaded files and download links
                if ($result->num_rows > 0) {
                    while ($row = $result->fetch_assoc()) {
                        // Sanitize all outputs for XSS protection
                        $res_paymentID = SecuritySanitizer::sanitize($row['PAYMENT_ID'], 'id');
                        $res_paymentType = SecuritySanitizer::sanitize($row['PAYMENT_TYPE'], 'enum');
                        $res_paymentAmount = SecuritySanitizer::sanitize($row['PAYMENT_AMOUNT'], 'decimal');
                        $res_paymentStatus = SecuritySanitizer::sanitize($row['PAYMENT_STATUS'], 'enum');
                        $res_StudId = SecuritySanitizer::sanitize($row['STUDENT_ID'], 'id');
                        $paymentReceipt = SecuritySanitizer::sanitize($row['PAYMENT_RECEIPT'], 'file_path');
                        
                        $file_path = "../../uploads/" . $paymentReceipt;

                        if (array_key_exists($res_StudId, $studentInfo)) {
                            $res_StudName = $studentInfo[$res_StudId];
                        } else {
                            $res_StudName = "Unknown"; // You can set a default name if needed
                        }

                        // Check if the file path is not empty and the file exists
                        if (!empty($paymentReceipt) && file_exists($file_path)) {
                            ?>
                            <tr class="tr-hover">
                                <td>
                                    <?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_StudName, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_StudId, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_paymentType, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>RM
                                    <?php echo htmlspecialchars($res_paymentAmount, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="text-align: center; color: <?php
                                if ($res_paymentStatus == 'UNPAID') {
                                    echo 'red';
                                } elseif ($res_paymentStatus == 'PENDING') {
                                    echo 'orange';
                                } else {
                                    echo 'green';
                                }
                                ?>;">
                                    <?php echo htmlspecialchars($res_paymentStatus, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <select name="selectStatus" class="status-select"
                                        data-payment-id="<?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?>">
                                        <option value="PENDING" <?php echo ($res_paymentStatus == 'PENDING') ? 'selected' : ''; ?>>
                                            PENDING</option>
                                        <option value="COMPLETED" <?php echo ($res_paymentStatus == 'COMPLETED') ? 'selected' : ''; ?>>COMPLETED</option>
                                        <option value="UNPAID" <?php echo ($res_paymentStatus == 'UNPAID') ? 'selected' : ''; ?>>
                                            UNPAID</option>
                                    </select>
                                </td>
                                <td width="6%"><button class="manage-buttons update-button"
                                        data-payment-id="<?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?>"
                                        onclick="updateFunction(this)">Update</button></td>

                                <td><button class="manage-buttons view-button download-button"
                                        onclick="downloadFile2('<?php echo htmlspecialchars($file_path, ENT_QUOTES, 'UTF-8'); ?>')">Download</button></td>

                            </tr>
                            <?php
                        } else {
                            ?>
                            <tr>
                                <td>
                                    <?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_StudName, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_StudId, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <?php echo htmlspecialchars($res_paymentType, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>RM
                                    <?php echo htmlspecialchars($res_paymentAmount, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td style="text-align: center; color: <?php
                                if ($res_paymentStatus == 'UNPAID') {
                                    echo 'red';
                                } elseif ($res_paymentStatus == 'PENDING') {
                                    echo 'yellow';
                                } else {
                                    echo 'green';
                                }
                                ?>;">
                                    <?php echo htmlspecialchars($res_paymentStatus, ENT_QUOTES, 'UTF-8') ?>
                                </td>
                                <td>
                                    <select name="selectStatus" id="selectStatus" class="status-select">
                                        <option value="PENDING" <?php echo ($res_paymentStatus == 'PENDING') ? 'selected' : ''; ?>>
                                            PENDING</option>
                                        <option value="COMPLETED" <?php echo ($res_paymentStatus == 'COMPLETED') ? 'selected' : ''; ?>>COMPLETED</option>
                                        <option value="UNPAID" <?php echo ($res_paymentStatus == 'UNPAID') ? 'selected' : ''; ?>>
                                            UNPAID</option>
                                    </select>
                                </td>
                                <td width="6%"><button class="manage-buttons update-button"
                                        data-payment-id="<?php echo htmlspecialchars($res_paymentID, ENT_QUOTES, 'UTF-8') ?>"
                                        onclick="updateFunction(this)">Update</button></td>
                                <td width="6%"><button class="manage-buttons view-button" onclick="downloadFile()">Download</button>
                                </td>
                            </tr>
                            <?php
                        }
                    }
                } else {
                    ?>
                    <tr>
                        <td colspan="2">Student ID not found.</td>
                    </tr>
                    <?php
                }
                ?>
            </tbody>
        </table>

    </div>
</body>

</html>
<?php include "../header/footer.php" ?>
<?php
$con->close();
?>