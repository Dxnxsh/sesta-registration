<?php 
   session_start();

   include("../../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
<?php include __DIR__ . "/../../header/studentHeader.php"; ?>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="stylesheet" a href="../../../css/bootstrap.css"/>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
  <!-- Include Bootstrap CSS -->
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" integrity="...">
  <!-- Include jQuery -->
  <script src="https://code.jquery.com/jquery-3.6.4.min.js" integrity="..." crossorigin="anonymous"></script>
  <!-- Include Bootstrap JS -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js" integrity="..." crossorigin="anonymous"></script>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10.16.6/dist/sweetalert2.min.js"></script>
  <title>Responsive Billing Page</title>
  
  <script>
    document.addEventListener('DOMContentLoaded', function () {
    // Automatically show the SweetAlert2 popup when the page loads
    let timerInterval;
    Swal.fire({
        title: "Processing Payment!",
        html: "I will close in <b></b> milliseconds.",
        timer: 3000,
        timerProgressBar: true,
        didOpen: () => {
            Swal.showLoading();
            const timer = Swal.getPopup().querySelector("b");
            timerInterval = setInterval(() => {
                timer.textContent = `${Swal.getTimerLeft()}`;
            }, 100);
        },
        willClose: () => {
            clearInterval(timerInterval);
        }
    }).then(() => {
        // Set a delay of 2 seconds before showing the second popup
        setTimeout(() => {
            // Second SweetAlert2 popup
            Swal.fire({
                title: "Payment Successfully!",
                text: "Unlock a world of gratitude! Your financial superhero cape awaits, thank you for effortlessly settling your bill!",
                icon: "success"
            });
        }, 100); // Use milliseconds, so 2 seconds = 2000 milliseconds
    });
});

function downloadFile() {

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
            title: "Email Succesfully send!"
    });
}
  function updateDateTime() {
    var options = {
        year: 'numeric',
        month: 'long',
        day: 'numeric',
        hour: 'numeric',
        minute: 'numeric',
        second: 'numeric',
        hour12: true,
        timeZone: 'Asia/Kuala_Lumpur'
    };
    var currentDate = new Date();

    var formattedDate = currentDate.toLocaleDateString('en-US', options);
    var formattedDay = currentDate.toLocaleDateString('en-US', { weekday: 'long', timeZone: 'Asia/Kuala_Lumpur' });

    document.getElementById('invoiceDate').innerText = formattedDate;
    document.getElementById('invoiceDay').innerText = formattedDay;
}

// Update date and time when the modal3 is shown
$(document).ready(function() {
    $('#blurredOverlayModal3').on('show.bs.modal', function () {
        updateDateTime();
    });
});

$(document).ready(function() {
        $('#blurredOverlayModal3').modal('show');
    });

</script>
</head>

<body style= "background-image: url(../../../image/student.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">

  <div class="container" id="blur">
  <?php include("../../config.php"); 
            
            $id = $_SESSION['valid'];
            $query = mysqli_query($con,"SELECT*FROM student WHERE STUDENT_ID=$id");
            $query2 = mysqli_query($con,"SELECT*FROM payment WHERE STUDENT_ID=$id");
            $queryParent  = mysqli_query($con,"SELECT student.*, parent.* FROM student INNER JOIN parent ON student.PARENT_ID = parent.PARENT_ID
            WHERE student.STUDENT_ID = $id");
            

            while($result = mysqli_fetch_assoc($query)){
                $res_Name = $result['STUDENT_NAME'];
                $res_IC = $result['STUDENT_ID'];
                $res_Add = $result['STUDENT_ADDRESS'];
            }

            while($result = mysqli_fetch_assoc($query2))
            {
                $res_id = $result['PAYMENT_ID'];
                $res_type = $result['PAYMENT_TYPE'];
                $res_amount = $result['PAYMENT_AMOUNT'];
                $res_stts = $result['PAYMENT_STATUS'];

                 // Check if the payment type is "PIBG FEES"
               if ($res_type == "PIBG FEES") {
                    $res_id3 = $res_id;
                    $res_type3 = $res_type;
                    $res_amount3 = $res_amount;
                    $res_stts3 = $res_stts;
               }
            }   

            while ($resultParent = mysqli_fetch_assoc($queryParent)) {
              $res_ParentMonthlyIncome = $resultParent['PARENT_MONTHLY_INCOME'];
              $res_ParentName = $resultParent['PARENT_NAME'];

                // Check if parent monthly income is less than 1000
                if ($res_ParentMonthlyIncome < 1000) {
                     // Apply a 20% discount
                     $discountedIncome = $res_amount3 * 0.8;
                } else {
                     // No discount if the income is 1000 or more
                     $discountedIncome = 0;
                }
          }
 ?>    
  </div>

  <div class="modal fade" id="blurredOverlayModal3" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">PAYMENT RECEIPT</h5>
                <a href="billing.php" class="btn-close" aria-label="Close"></a>
            </div>

            <div class="modal-body">
                 <!-- Invoice Template -->
                <div class="invoice">
                    <div class="invoice-header">
                        <h4 >Receipt # <?php echo $res_IC ?></h4>
                        <p><b>Date: </b><span id="invoiceDate"></span></p>
                        <p><b>Day: </b><span id="invoiceDay"></span></p>
                    </div>
                    <div class="invoice-details">
                        <p><b>Bill To (student): </b><?php echo $res_Name ?></p>
                        <p><b>Bill To (parent): </b><?php echo $res_ParentName ?></p>
                        <address>
                          <p><b>Address : </b><?php echo $res_Add ?></p>
                        </address>
                    </div>
                    <div class="invoice-items">
                      <table class="table">
                            <thead>
                                <tr>
                                    <th>Item</th>
                                    <th>Description</th>
                                    <th>Amount (RM)</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr>
                                  <td><p><span><?php echo $res_type3 ?></span></p></td>
                                  <td><p>PAYMENT FOR <span><?php echo $res_type3 ?></span></p></td>
                                  <td><p><span><?php echo $res_amount3 ?></span></p></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="invoice-method">
                        <p><b>Selected Payment Method: </b><span> FPX INTERNET</span></p>
                    </div>
                    <div class="invoice-total">
                         <p><b>Total Amount: </b>RM<span><?php echo $res_amount3 ?></span></p>
                        <p><b>Discounted Total Amount: </b>RM<span><?php echo $discountedIncome ?></span></p>
                    </div>
                </div>
                <!-- End Invoice Template -->
            </div>

               <div class="modal-footer">
                <a href="pdf_maker3.php?PAYMENT_ID=<?php echo $res_id3 ?>&ACTION=VIEW" class="btn btn-success" target="_blank"><i class="fa fa-file-pdf-o"></i> View PDF</a>
				<a href="pdf_maker3.php?PAYMENT_ID=<?php echo $res_id3 ?>&ACTION=DOWNLOAD" class="btn btn-danger" target="_blank"><i class="fa fa-download"></i> Download PDF</a>
				&nbsp;&nbsp;  
				<a href="pdf_maker3.php?PAYMENT_ID=<?php echo $res_id3 ?>&ACTION=EMAIL" class="btn btn-info" onclick="downloadFile()"><i class="fa fa-envelope"></i> Email PDF</a>
            </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include __DIR__ . "/../../header/footer.php" ?>
</html>