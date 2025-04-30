<?php 
            include("config.php");
           $query = mysqli_query($con, "SELECT * FROM student");
           $query2 = mysqli_query($con, "SELECT * FROM teacher");
           $query3 = mysqli_query($con, "SELECT * FROM class");
           $query4 = mysqli_query($con, "SELECT * FROM payment");

           if ($query) {
                $rowCount = mysqli_num_rows($query);
            } else {
                echo "Error in the query: " . mysqli_error($con);
            }

            if ($query2) {
                $rowCount2 = mysqli_num_rows($query2);
            } else {
                echo "Error in the query: " . mysqli_error($con);
            }

            if ($query3) {
                $rowCount3 = mysqli_num_rows($query3);
            } else {
                echo "Error in the query: " . mysqli_error($con);
            }


            $rowCount4 = 0;
            $rowCount5 = 0;
            while ($result = mysqli_fetch_assoc($query2)) {
                $res_gender = $result['TEACHER_GENDER'];
    
                if ($res_gender == "Male") {
                    $rowCount4++;
                }
                if ($res_gender == "Female") {
                    $rowCount5++;
            }
            }

            // Counting the number of students in level 1 and level 4
            $rowCount6 = 0;
            $rowCount7 = 0;
            while ($result = mysqli_fetch_assoc($query)) {
                $res_level = $result['STUDENT_LEVEL'];
    
            if ($res_level == "1") {
                $rowCount6++;
            }
            if ($res_level == "4") {
                $rowCount7++;
            }
            }

           $totalAmount8 = 0;
            $totalAmount9 = 0;

            while ($result = mysqli_fetch_assoc($query4)) {
                $res_level = $result['PAYMENT_STATUS'];
                $res_payment_amount = $result['PAYMENT_AMOUNT'];
    
            if ($res_level == "UNPAID") {
                $totalAmount8 += $res_payment_amount;  // Add payment amount for UNPAID
            }

            if ($res_level == "COMPLETED") {
                $totalAmount9 += $res_payment_amount;  // Add payment amount for COMPLETED
            }
            }
            ?>     

<?php                
 
include_once('../tcpdf_6_2_13/tcpdf/tcpdf.php');

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
            text-align: center;
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
        <img src="../image/logoSESTA2.png" alt="School Logo" width="300" height="auto">
    </div>

    <div class="header">
        <h2>SEKOLAH MENENGAH SAINS TAPAH</h2>
        <p>Contact: +05 4018745 | Website: www.sesta.com | Email : AEA0043@moe.edu.my</p>
        <p>Address: SM Sains Tapah, Jalan Pahang, 35000 Tapah Perak</p>
    </div>

    <div class="invoice-details">
        <h2>SYSTEM SUMMARY</h2>
        <p>SYSTEM SUMMARY DATE: '.date("d-m-Y").'</p>
    </div>

    <table>
        <thead>
            <tr>
                <th><b>SUMMARY</b></th>
                <th><b>TOTAL</b></th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>Total Student</td>
                <td>'.$rowCount.' Student</td>
            </tr>
            <tr>
                <td>Total Teacher</td>
                <td>'.$rowCount2.' Teacher</td>
            </tr>
            <tr>
                <td>Total Class</td>
                <td>'.$rowCount3.' Class</td>
            </tr>
            <tr>
                <td>Total Paid Payment</td>
                <td>RM '.$totalAmount9.'</td>
            </tr>
            <tr>
                <td>Total Unpaid Payment</td>
                <td>RM '.$totalAmount8.'</td>
            </tr>
        </tbody>
    </table>

    <div class="thank-you">
        <p>THANK YOU! VISIT AGAIN</p>
    </div>
';
$pdf->writeHTML($content);

$file_location =  "D:/xamp/htdocs/school-registration 2/pdf"; //add your full path of your server
//$file_location = "/opt/lampp/htdocs/examples/generate_pdf/pdf/"; //for local xampp server

$datetime=date('dmY_hms');
$file_name = "INV_".$datetime.".pdf";
ob_end_clean();

if($_GET['ACTION']=='VIEW') 
{
	$pdf->Output($file_name, 'I'); // I means Inline view
}
//----- End Code for generate pdf
	

?>