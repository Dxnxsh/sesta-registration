<?php 
   session_start();
/*SCHOOL FEES */
   include("../../config.php");
   
   if(!isset($_SESSION['valid'])){
    SecuritySanitizer::logSecurityEvent('unauthorized_access', 'PDF maker access without valid student session');
    header("Location: ../../login-logout/login.php");
    exit();
   }
?>
<?php include("../../config.php"); 
            
            $id = SecuritySanitizer::sanitize($_SESSION['valid'], 'id', 'STUDENT_ID');
            
            // Use prepared statements for all queries
            $query = mysqli_prepare($con, "SELECT * FROM student WHERE STUDENT_ID = ?");
            if (!$query) {
                SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare student query in PDF maker: ' . mysqli_error($con));
                die('Database error occurred');
            }
            
            mysqli_stmt_bind_param($query, "s", $id);
            mysqli_stmt_execute($query);
            $result = mysqli_stmt_get_result($query);
            
            $query2 = mysqli_prepare($con, "SELECT * FROM payment WHERE STUDENT_ID = ?");
            if (!$query2) {
                SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare payment query in PDF maker: ' . mysqli_error($con));
                die('Database error occurred');
            }
            
            mysqli_stmt_bind_param($query2, "s", $id);
            mysqli_stmt_execute($query2);
            $result2 = mysqli_stmt_get_result($query2);
            
            $queryParent = mysqli_prepare($con, "SELECT student.*, parent.* FROM student INNER JOIN parent ON student.PARENT_ID = parent.PARENT_ID WHERE student.STUDENT_ID = ?");
            if (!$queryParent) {
                SecuritySanitizer::logSecurityEvent('sql_error', 'Failed to prepare parent query in PDF maker: ' . mysqli_error($con));
                die('Database error occurred');
            }
            
            mysqli_stmt_bind_param($queryParent, "s", $id);
            mysqli_stmt_execute($queryParent);
            $resultParent = mysqli_stmt_get_result($queryParent);

            // Initialize variables to prevent undefined variable warnings
            $res_Name = $res_IC = $res_Add = $res_Email = '';
            $res_id1 = $res_type1 = $res_amount1 = $res_stts1 = '';
            $res_ParentMonthlyIncome = $res_ParentName = '';
            $discountedIncome = 0;

            if($row = mysqli_fetch_assoc($result)){
                $res_Name = $row['STUDENT_NAME'];
                $res_IC = $row['STUDENT_ID'];
                $res_Add = $row['STUDENT_ADDRESS'];
                $res_Email = $row['STUDENT_EMAIL'];
            }

            while($row2 = mysqli_fetch_assoc($result2))
            {
                $res_id = $row2['PAYMENT_ID'];
                $res_type = $row2['PAYMENT_TYPE'];
                $res_amount = $row2['PAYMENT_AMOUNT'];
                $res_stts = $row2['PAYMENT_STATUS'];

                 // Check if the payment type is "SCHOOL FEES"
               if ($res_type == "SCHOOL FEES") {
                    $res_id1 = $res_id;
                    $res_type1 = $res_type;
                    $res_amount1 = $res_amount;
                    $res_stts1 = $res_stts;
               }
            }  

            if ($rowParent = mysqli_fetch_assoc($resultParent)) {
              $res_ParentMonthlyIncome = $rowParent['PARENT_MONTHLY_INCOME'];
              $res_ParentName = $rowParent['PARENT_NAME'];

                // Check if parent monthly income is less than 1000
                if ($res_ParentMonthlyIncome < 1000) {
                     // Apply a 20% discount
                     $discountedIncome = $res_amount1 * 0.8;
                } else {
                     // No discount if the income is 1000 or more
                     $discountedIncome = 0;
                }
          }
 ?>     

<?php                
 
// Include Composer autoloader to load tcpdf
require_once('../../../vendor/autoload.php');

	//----- Code for generate pdf
	$pdf = new TCPDF('P', PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);
	$pdf->SetCreator(PDF_CREATOR);  
	//$pdf->SetTitle("Export HTML Table data to PDF using TCPDF in PHP");  
	$pdf->SetHeaderData('', 0, PDF_HEADER_TITLE, PDF_HEADER_STRING);  
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
        <p>BILL DATE: '.date("d-m-Y").' | BILL NUMBER: '.$res_id1.'</p>
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
                <td>'.$res_type1.'</td>
                <td>PAYMENT FOR '.$res_type1.'</td>
                <td>'.$res_amount1.'</td>
            </tr>
        </tbody>
    </table>

    <div class="invoice-total">
        <p>Total Amount: RM'.$res_amount1.'</p>
        <p>Discounted Total Amount: RM'.$discountedIncome.'</p>
    </div>

    <div class="thank-you">
        <p>THANK YOU! VISIT AGAIN</p>
    </div>
';
$pdf->writeHTML($content);
$file_location = $_SERVER['DOCUMENT_ROOT'] . "pdf/"; //add your full path of your server
//$file_location = "/opt/lampp/htdocs/examples/generate_pdf/uploads/"; //for local xampp server

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

    include_once '../../config/email_config.php';

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

    $result = EmailConfig::sendEmailWithAttachment(
        $res_Email, 
        'Invoice details', 
        $body, 
        $file_location . $file_name,
        $file_name
    );
    
    if ($result['success']) {
        // Email sent successfully
        echo '<script>alert("Email sent successfully!");</script>';
    } else {
        // Email sending failed
        echo '<script>alert("Email could not be sent: ' . $result['message'] . '");</script>';
    }

    // Redirect back to the original page
    echo '<script>window.location.href = window.history.back();</script>';
}
//----- End Code for generate pdf
	

?>