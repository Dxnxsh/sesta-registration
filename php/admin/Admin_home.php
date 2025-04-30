<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
	header("Location: ../login-logout/loginadmin.php");
}
?>

<?php include "../header/adminHeader.php" ?>
<!doctype html>
<html>

<head>
	<meta charset="UTF-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge" />
	<meta name="viewport" content="width=device-width, initial-scale=1.0" />
	<title>Admin Page</title>
	<link href="https://unpkg.com/boxicons@2.1.2/css/boxicons.min.css" rel="stylesheet" />
	<link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
	<link rel="stylesheet" href="../../css/admin_homeStyle.css" />

</head>

<body>
	<?php
	include("../config.php");
	$query = mysqli_query($con, "SELECT * FROM student");
	$query2 = mysqli_query($con, "SELECT * FROM teacher");
	$query3 = mysqli_query($con, "SELECT * FROM admin");
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
	<div class="header">
		<h1 style="color: #FFFFFF">Administration</h1>
	</div>
	<section id="content">
		<main>
			<div class="container">
				<ul class="box-info">
					<li>
						<i class='bx bxs-graduation'></i>
						<span class="text">
							<h3>
								<?php echo $rowCount ?>
							</h3>
							<p>Total Student</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-book-reader'></i>
						<span class="text">
							<h3>
								<?php echo $rowCount2 ?>
							</h3>
							<p>Total Teacher</p>
						</span>
					</li>
					<li>
						<i class='bx bxs-user'></i>
						<span class="text">
							<h3>
								<?php echo $rowCount3 ?>
							</h3>
							<p>Total Admin</p>
						</span>
					</li>
				</ul>
				<div class="belowbox">
					<div class="chartbox">
						<div class="charts-card">
							<p class="chart-title">Chart</p>
							<div id="bar-chart"></div>
						</div>
					</div>
					<div class="box">
						<ul class="lists">
							<li class="list">
								<a href="admin_home.php" class="nav-link">
									<i class='bx bxs-home icon'></i>
									<span class="link">Home</span>
								</a>
							</li>
							<li class="list">
								<a href="adminclass.php" class="nav-link">
									<i class='bx bxs-chalkboard icon'></i>
									<span class="link">Class</span>
								</a>
							</li>
							<li class="list">
								<a href="AdminList.php" class="nav-link">
									<i class='bx bxs-user-circle icon'></i>
									<span class="link">Admin</span>
								</a>
							</li>
							<li class="list">
								<a href="teacherList.php" class="nav-link">
									<i class='bx bxs-book-reader icon'></i>
									<span class="link">Teacher</span>
								</a>
							</li>
							<li class="list">
								<a href="studentlist.php" class="nav-link">
									<i class='bx bxs-graduation icon'></i>
									<span class="link">Student</span>
								</a>
							</li>
							<li class="list">
								<a href="AdminBilling.php"
									class="nav-link">
									<i class='bx bxs-dollar-circle icon'></i>
									<span class="link">Billing</span>
								</a>
							</li>
							<li class="list">
								<a href="AdminbackupSummary.php" class="nav-link">
									<i class='bx bxs-pie-chart-alt-2 icon'></i>
									<span class="link">System Summary</span>
								</a>
							</li>
							<li class="list">
								<a href="studentFullReport.php" class="nav-link">
									<i class='bx bxs-report icon'></i>
									<span class="link">Full Report</span>
								</a>
							</li>
						</ul>
					</div>
				</div>
			</div>
		</main>
	</section>
</body>
<script src="https://cdnjs.cloudflare.com/ajax/libs/apexcharts/3.45.1/apexcharts.min.js"></script>
<script>
	const barChartOptions = {
		series: [
			{
				data: [<?php echo $rowCount ?>, <?php echo $rowCount2 ?>, <?php echo $rowCount3 ?>,],
			},
		],
		chart: {
			type: 'bar',
			height: 350,
			toolbar: {
				show: false,
			},
		},
		colors: ['#3C91E6', '#008000', '#FF0000'],
		plotOptions: {
			bar: {
				distributed: true,
				borderRadius: 4,
				horizontal: false,
				columnWidth: '40%',
			},
		},
		dataLabels: {
			enabled: true,
		},
		legend: {
			show: true,
		},
		xaxis: {
			categories: ['Student', 'Teacher', 'Admin'],
		},
	};

	const barChart = new ApexCharts(document.querySelector('#bar-chart'), barChartOptions);
	barChart.render();
</script>

</html>
<?php include "../header/footer.php" ?>