<?php 
   session_start();
   include("../../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/login.php");
   }
?>


<?php include __DIR__ . "/../../header/studentHeader.php"; ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" integrity="sha512-...." crossorigin="anonymous" />
  <title>Responsive Billing Page</title>
  <style>
    body {
      font-family: 'Poppins', sans-serif;
      margin: 0;
      background: url("../image/student.jpeg") no-repeat center center fixed;
      background-size: cover;
    }


    .container {
      max-width: 1000px;
      margin: 20px auto;
      background-color: #ffffff;
      box-shadow: 0 0 20px rgba(255, 255, 255, 0.5);
      border-radius: 3px;
      overflow: hidden;
      position: relative;
    }

    .container:hover::before {
      opacity: 1;
    }

    .logo {
      text-align: center;
      padding: 20px;
    }

    .logo img {
      max-width: 100%;
      height: auto;
    }

    h2 {
      font-size: 26px;
      margin: 20px 0;
      text-align: center;
    }

    table {
      border-collapse: separate;
      border-spacing: 10px; /* Adjust the spacing between cells */
      width: 100%;
    }

    th, td {
      padding: 20px;
      text-align: left;
      color: #333;
      border-radius: 0; /* Remove border-radius for squared corners */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.1); /* Add shadow to each cell */
    }

    th {
      font-weight: bold;
      font-size: 16px;
      text-transform: uppercase;
      letter-spacing: 0.03em;
      background-color: #233858;
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

    /* Remove border-right for the last cell in each row */
    .payment-type-row th:last-child,
    .amount-row th:last-child,
    .status-row th:last-child,
    .payment-type-row td:last-child,
    .amount-row td:last-child,
    .status-row td:last-child {
      border-right: 0;
    }

    .responsive-table {
      display: none;
    }

    .responsive-table .col {
      position: relative;
    }

    .responsive-table .col:not(:last-child) {
      border-right: 1px solid #ddd;
    }

    @media all and (max-width: 767px) {
      .container {
        border-radius: 0;
        box-shadow: none;
      }

      table:not(.responsive-table) {
        display: none;
      }

      .responsive-table {
        display: block;
        list-style: none;
        padding: 0;
        margin: 0;
      }

      .responsive-table li {
        border-radius: 3px;
        padding: 20px;
        margin-bottom: 20px;
        background-color: #fff;
        box-shadow: 0px 0px 9px 0px rgba(0, 0, 0, 0.1);
        display: flex;
        flex-wrap: wrap;
        justify-content: space-between;
      }

      .responsive-table .col {
        flex-basis: 32%;
        display: flex;
        padding: 10px 0;
        position: relative;
      }

      .responsive-table .col:after {
        content: "";
        position: absolute;
        top: 0;
        right: 0;
        height: 100%;
        width: 1px;
        background-color: #ddd;
      }

      .responsive-table .col:last-child:after {
        content: none;
      }

      .responsive-table .col:before {
        color: #233858;
        padding-right: 10px;
        content: attr(data-label);
        flex-basis: 50%;
        text-align: right;
      }
    }

    .payment-type-row,
    .amount-row,
    .status-row {
      background-color: #233858;
      color: #fff;
    }

    tr {
      margin-bottom: 30px;
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
      margin: 0 auto; /* Center the button horizontally within its container */
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
      background-image: linear-gradient(to right, #000428 0%, #004e92  51%, #000428  100%);
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
    h2 {
      display: flex;
      align-items: center;
      justify-content: center;
      background-image: linear-gradient(to right, #000428 0%, #004e92  51%, #000428  100%);
      
      color: #fff;
      padding: 10px 20px;
      border-radius: 5px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
    }

    i {
      margin-right: 10px;
    }
  </style>
  <script>
    function confirmPay1() {
     var confirmDelete = confirm("This action cannot be reversed! Proceed with caution.");
    
     if (confirmDelete) {
        window.location.href = 'qr.php';
     } else {
      // Do nothing or handle the cancellation
      }
    }

    function confirmPay2() {
     var confirmDelete = confirm("This action cannot be reversed! Proceed with caution.");
    
     if (confirmDelete) {
        window.location.href = 'qr2.php';
     } else {
      // Do nothing or handle the cancellation
      }
    }

    function confirmPay3() {
     var confirmDelete = confirm("This action cannot be reversed! Proceed with caution.");
    
     if (confirmDelete) {
        window.location.href = 'qr3.php';
     } else {
      // Do nothing or handle the cancellation
      }
    }
</script>
</head>

<body style= "background-image: url(../../../image/student.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
  <div class="container">
   <?php include("../../config.php"); 
            
            $id = $_SESSION['valid'];
            $query = mysqli_query($con,"SELECT*FROM student WHERE STUDENT_ID=$id");
            $query2 = mysqli_query($con,"SELECT*FROM payment WHERE STUDENT_ID=$id");

            while($result = mysqli_fetch_assoc($query)){
                $res_Name = $result['STUDENT_NAME'];
                $res_IC = $result['STUDENT_ID'];
                $res_Add = $result['STUDENT_ADDRESS'];
            }
 ?>
    <div class="logo">
    <img src="<?php echo '/Sesta-registration/image/icon/logoSESTA2.png'; ?>" alt="Logo">
</div>
    <h2>
      <i class="fas fa-money-bill"></i> BILLING
    </h2>
    <table>
      <tr>
        <th>NAME</th>
        <td colspan="7"><b><?php echo $res_Name ?></b></td>
      </tr>
      <tr>
        <th>STUDENT IC</th>
        <td colspan="7"><b><?php echo $res_IC ?></b></td>
      </tr>
      <tr class="payment-type-row">
        <th style="text-align: center;" colspan="1">Choose Payment</th>
        <th style="text-align: center;" colspan="2">PAYMENT TYPE</th>
        <th style="text-align: center;">AMOUNT (RM)</th>
        <th style="text-align: center;">STATUS</th>
      </tr>
      <?php 
        while($result = mysqli_fetch_assoc($query2))
        {
            $res_type = $result['PAYMENT_TYPE'];
            $res_amount = $result['PAYMENT_AMOUNT'];
            $res_stts = $result['PAYMENT_STATUS'];

              // Check if the payment type is "SCHOOL FEES"
               if ($res_type == "SCHOOL FEES") {
              $res_type1 = $res_type;
              $res_amount1 = $res_amount;
              $res_stts1 = $res_stts;

                // Check if the status is "UNPAID"
                 if ($res_stts1 == "UNPAID") {
                ?>
                <tr class="amount-row">
                    <td class="button-cell">
                      <button type="button" class="proceed-payment-button" style="width: 210px;" onclick="confirmPay1()">
                      <i class="fas fa-credit-card"></i> Confirm Payment
                      </button>
                    </td>
                    <td style="text-align: center;" colspan="2"><b><?php echo $res_type1 ?></td>
                    <td style="text-align: center;"><b><?php echo $res_amount1 ?></td>
                    <td style="text-align: center; color: <?php echo $res_stts1 == 'UNPAID' ? 'red' : ''; ?>"><b><?php echo $res_stts1 ?></td>
                </tr>
                <?php 
                }
               }
              // Check if the payment type is "DORMITORY FEES"
              if ($res_type == "DORMITORY FEES") {
              $res_type2 = $res_type;
              $res_amount2 = $res_amount;
              $res_stts2 = $res_stts;

              // Check if the status is "UNPAID"
              if ($res_stts2 == "UNPAID") {
                ?>
                <tr class="amount-row">
                    <td class="button-cell">
                      <button type="button" class="proceed-payment-button" style="width: 210px;" onclick="confirmPay2()">
                      <i class="fas fa-credit-card"></i> Confirm Payment
                      </button>
                    </td>
                    <td style="text-align: center;" colspan="2"><b><?php echo $res_type2 ?></td>
                    <td style="text-align: center;"><b><?php echo $res_amount2 ?></td>
                    <td style="text-align: center; color: <?php echo $res_stts2 == 'UNPAID' ? 'red' : ''; ?>"><b><?php echo $res_stts2 ?></td>
                </tr>
                <?php 
                }
              }
              // Check if the payment type is "PIBG FEES"
              if ($res_type == "PIBG FEES") {
              $res_type3 = $res_type;
              $res_amount3 = $res_amount;
              $res_stts3 = $res_stts;

              // Check if the status is "UNPAID"
              if ($res_stts3 == "UNPAID") {
                ?>
                <tr class="amount-row">
                    <td class="button-cell">
                      <button type="button" class="proceed-payment-button" style="width: 210px;" onclick="confirmPay3()">
                      <i class="fas fa-credit-card"></i> Confirm Payment
                      </button>
                    </td>
                    <td style="text-align: center;" colspan="2"><b><?php echo $res_type3 ?></td>
                    <td style="text-align: center;"><b><?php echo $res_amount3 ?></td>
                    <td style="text-align: center; color: <?php echo $res_stts3 == 'UNPAID' ? 'red' : ''; ?>"><b><?php echo $res_stts3 ?></td>
                </tr>
                <?php 
                }
              }
        }  
    ?> 
    </table>
    <div class="button-container">
      <button type="button" class="proceed-payment-button" onclick="window.location.href = 'billing.php';">
        <i class="fas fa-arrow-left"></i> Back </button>
    </div>
        </br>
  </div>
</body>

</html>
<?php include __DIR__ . "/../../header/footer.php" ?>
