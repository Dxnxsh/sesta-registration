<?php 
   session_start();

   include("../../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/loginStudent.php");
   }
?>
<!DOCTYPE html>
<html lang="en">
<head>
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
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
  <title>Responsive Billing Page</title>
  <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }

        td {
            text-align: center;
            padding: 10px;
        }
        th {
            text-align: center;
            padding: 10px;
        }
    </style>
  <script>
        $(document).ready(function() {
             $('#blurredOverlayModal2').modal('show');
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
                $res_type = $result['PAYMENT_TYPE'];
                $res_amount = $result['PAYMENT_AMOUNT'];
                $res_stts = $result['PAYMENT_STATUS'];

                 // Check if the payment type is "DORMITORY FEES"
               if ($res_type == "DORMITORY FEES") {
                    $res_type2 = $res_type;
                    $res_amount2 = $res_amount;
                    $res_stts2 = $res_stts;
               }
            }  

            while ($resultParent = mysqli_fetch_assoc($queryParent)) {
              $res_ParentMonthlyIncome = $resultParent['PARENT_MONTHLY_INCOME'];

                // Check if parent monthly income is less than 1000
                if ($res_ParentMonthlyIncome < 1000) {
                     // Apply a 20% discount
                     $discountedIncome = $res_amount2 * 0.9;
                } else {
                     // No discount if the income is 1000 or more
                     $discountedIncome = 0;
                }
          }
 ?>     
</div>

 <div class="modal fade" id="blurredOverlayModal2" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">PAYMENT RECEIPT</h5>
            </div>

            <div class="modal-body">
                 <!-- Invoice Template -->
                <div class="invoice">
                    <div class="invoice-header">
                        <h4 >Receipt # <?php echo $res_IC ?></h4>
                    </div>
                    <div class="invoice-details">
                        <p><b>Bill To : </b><?php echo $res_Name ?></p>
                        <address>
                          <p><b>Address : </b><?php echo $res_Add ?></p>
                        </address>
                    </div>
                    <div class="invoice-items">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>QR CODE : <span><?php echo $res_type2 ?></span></th>
                                </tr>
                            </thead>
                             <tbody>
                                <tr>
                                    <td><img src="../../../image/qr.jpg" alt="Description of the image" width="350" height="400"></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="invoice-transer">
                        <p><b>Transfer to: </b></p>
                        <p>MUHAMMAD ASYRAAF DANIAL BIN MOHD NASIR</p>
                        <p>12261020155157</p>
                    </div>
                    <div class="invoice-method">
                        <p><b>Selected Payment Method: </b><span> FPX INTERNET</span></p>
                    </div>
                    <div class="invoice-total">
                        <p><b>Total Amount: RM </b><span><?php echo $res_amount2 ?></span></p>
                        <p><b>Discounted Total Amount: RM </b><span><?php echo $discountedIncome ?></span></p>
                    </div>
                    <div >
		                <form action="upload2.php" method="POST" enctype="multipart/form-data">
			        <div class="mb-3">
				        <label for="file" class="form-label"><b>Select Payment Receipt</b></label>
				        <input type="file" class="form-control" name="file" id = "file" autocomplete="off" required>
                        </br>
                        <p>File types allowed: .pdf, .jpg, .png, .jpeg & under 10 MB</p>
			        </div>
			<button type="submit" class="btn btn-primary"><i class="fa fa-upload"></i> Upload File</button>
            <button type="button" class="btn btn-secondary" onclick="window.location.href = 'payment1.php';"><i class="fas fa-arrow-left"></i> Back</button>
		</form>
	</div>
                </div>
                <!-- End Invoice Template -->
            </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
<?php include __DIR__ . "/../../header/footer.php" ?>
</html>