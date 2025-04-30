<?php 
   session_start();
/*PIBG FEES */
   include("../../config.php");
   if(!isset($_SESSION['valid'])){
    header("Location: ../login-logout/loginStudent.php");
   }
?>
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
                $res_Email = $result['STUDENT_EMAIL'];
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

<?php                
 
include_once('../../../tcpdf/tcpdf.php');

	$inv_mst_data_row = mysqli_fetch_array($inv_mst_results, MYSQLI_ASSOC);

	//----- Code for generate pdf
	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);  
	//$pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
	$pdf->SetHeaderData('', '', PDF_HEADER_TITLE, PDF_HEADER_STRING);  
	$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));  
	$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));  
	$pdf->SetDefaultMonospacedFont('helvetica');  
	$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);  
	$pdf->SetMargins(PDF_MARGIN_LEFT, '5', PDF_MARGIN_RIGHT);  
	$pdf->setPrintHeader(false);  
	$pdf->setPrintFooter(false);  
	$pdf->SetAutoPageBreak(TRUE, 10);  
	$pdf->SetFont('helvetica', '', 12);  
	$pdf->AddPage(); //default A4
	//$pdf->AddPage('P','A5'); //when you require custome page size 
	
	$content = '
    <style type="text/css">
        body {
            font-size: 12px;
            line-height: 18px;
            font-family: "Helvetica Neue", "Helvetica", Helvetica, Arial, sans-serif;
            color: #333;
            margin: 0;
            padding: 0;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }

        th {
            background-color: #f2f2f2;
        }

        .header {
            text-align: center;
            padding: 20px;
            border-bottom: 2px solid #ddd;
        }

        .student-info {
            font-weight: bold;
        }

        .parent-info {
            font-weight: bold;
        }

        .invoice-details {
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }

        .invoice-total {
            font-weight: bold;
            text-align: left;
        }

        .thank-you {
            text-align: center;
            font-weight: bold;
            margin-top: 20px;
        }
        .logo {
            text-align: center;
            margin-bottom: 20px;
        }

    </style>

     <div class="logo">
        <img src="../../../image/icon/logoSESTA2.png" alt="School Logo" width="300" height="auto">
    </div>

    <div class="header">
        <h2>SEKOLAH MENENGAH SAINS TAPAH</h2>
        <p>Contact: +05 4018745 | Website: www.sesta.com | Email : AEA0043@moe.edu.my</p>
        <p>Address: SM Sains Tapah, Jalan Pahang, 35000 Tapah Perak</p>
    </div>

    <div class="invoice-details">
        <p class="parent-info">Parent Name: '.$res_ParentName.'</p>
        <p class="student-info">Student Name: '.$res_Name.'</p>
        <p class="student-info">Student IC Number: '.$res_IC.'</p>
        <p>BILL DATE: '.date("d-m-Y").' | BILL NUMBER: '.$res_id3.'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th><b>ITEM</b></th>
                <th><b>DESCRIPTION</b></th>
                <th><b>AMOUNT (RM)</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>'.$res_type3.'</td>
                <td>PAYMENT FOR '.$res_type3.'</td>
                <td>'.$res_amount3.'</td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-total">
        <p>Total Amount: RM'.$res_amount3.'</p>
        <p>Discounted Total Amount: RM'.$discountedIncome.'</p>
    </div>

    <div class="thank-you">
        <p>THANK YOU! VISIT AGAIN</p>
    </div>
';
$pdf->writeHTML($content);

$file_location = $_SERVER['DOCUMENT_ROOT'] . "/Sesta-registration/pdf"; //add your full path of your server
//$file_location = "/opt/lampp/htdocs/examples/generate_pdf/pdf/"; //for local xampp server

$datetime=date('dmY_hms');
$file_name = "INV_".$datetime.".pdf";
ob_end_clean();

if($_GET['ACTION']=='VIEW') 
{
	$pdf->Output($file_name, 'I'); // I means Inline view
} 
else if($_GET['ACTION']=='DOWNLOAD')
{
	$pdf->Output($file_name, 'D'); // D means download
}
else if ($_GET['ACTION'] == 'EMAIL') {
    $pdf->Output($file_location . $file_name, 'F');

    include_once '../../../PHPMailer/PHPMailerAutoload.php';

    $body = '<html>
        <head>
            <style type="text/css">
                body {
                    font-family: Calibri;
                    font-size: 16px;
                    color: #000;
                }
            </style>
        </head>
        <body>
            Dear Student,
            <br>
            Please find attached receipt copy.
            <br>
            Thank you!
        </body>
    </html>';

    $mail = new PHPMailer();
    $mail->CharSet = 'UTF-8';
    $mail->IsSMTP();
    
    // Configure SMTP settings if using SMTP
    // $mail->Host = 'your-smtp-host';
    // $mail->Port = 587; // Adjust the port accordingly
    // $mail->SMTPSecure = 'tls'; // Use 'ssl' or 'tls' depending on your server
    // $mail->SMTPAuth = true;
    // $mail->Username = 'your-smtp-username';
    // $mail->Password = 'your-smtp-password';

    $mail->Subject = 'Invoice details';
    $mail->From = 'mail@sesta.com';
    $mail->FromName = 'SEKOLAH MENENGAH SAINS TAPAH';
    $mail->IsHTML(true);
    $mail->AddAddress($res_Email);
    $mail->AddAttachment($file_location . $file_name);
    $mail->MsgHTML($body);
    $mail->WordWrap = 50;
    if ($mail->Send()) {
        // Email sent successfully
        echo '<script>alert("Email sent successfully!");</script>';
    } else {
        // Email sending failed
        echo '<script>alert("Email could not be sent.");</script>';
    }

    // Redirect back to the original page
    echo '<script>window.location.href = window.history.back();</script>';
}
//----- End Code for generate pdf
	

?>