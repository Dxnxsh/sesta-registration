<?php
session_start();

include("../config.php");
if (!isset($_SESSION['valid'])) {
    header("Location: ../login-logout/login.php");
}
?>

<?php include "../header/studentHeader.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Subject Outline</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
  <link rel="stylesheet" href="../../css/SSO.css">
</head>

<body>
  <div class="container">
    <!-- Header Section -->
    <div class="header">
      <div class="inner-header">
        <h2 id="subject-outline-header"><i class="fas fa-book"></i> SUBJECT OUTLINE</h2>
      </div>
    </div>

    <!-- Search & Back Button -->
    <div id="search-box-container">
      <div class="button-container">
        <button type="button" class="proceed-payment-button" onclick="window.location.href = '../student_home.php';">
          <i class="fas fa-arrow-left"></i> Back
        </button>
      </div>
		
      <div class="search">
        <input type="text" class="searchTerm" id="search-box" onkeyup="searchSubjects()" placeholder="Search for subjects">
        <button type="submit" class="searchButton">
          <i class="fa fa-search"></i>
        </button>
      </div>
    </div>
	
	<!-- Form 1 Table Container -->
	<div id="form1-container">
	  <table id="form1-table">
		<thead>
		  <tr>
			<th>No.</th>
			<th>Form 1 Subjects</th>
		  </tr>
		</thead>
		<tbody>
		  <tr><td><b>1</b></td><td>BAHASA MELAYU</td></tr>
		  <tr><td><b>2</b></td><td>ENGLISH LANGUAGE</td></tr>
		  <tr><td><b>3</b></td><td>MATHEMATICS</td></tr>
		  <tr><td><b>4</b></td><td>SCIENCES</td></tr>
		  <tr><td><b>5</b></td><td>DESIGN & TECHNOLOGY</td></tr>
		  <tr><td><b>6</b></td><td>ISLAMIC STUDIES</td></tr>
		  <tr><td><b>7</b></td><td>HISTORY</td></tr>
		  <tr><td><b>8</b></td><td>GEOGRAPHY</td></tr>
		  <tr><td><b>9</b></td><td>BASIC SCIENCE COMPUTER</td></tr>
		  <tr><td><b>10</b></td><td>JAPANESE LANGUAGE</td></tr>
		  <tr><td><b>11</b></td><td>CHINESE LANGUAGE (MANDARIN)</td></tr>
		  <tr><td><b>12</b></td><td>FRENCH LANGUAGE</td></tr>
		  <tr><td><b>13</b></td><td>MUSICAL ARTS EDUCATION</td></tr>
		  <tr><td><b>14</b></td><td>ARABIC LANGUAGE</td></tr>
		  <tr><td><b>15</b></td><td>PHYSICAL & HEALTH EDUCATION</td></tr>
		  <tr><td><b>16</b></td><td>VISUAL ARTS EDUCATION</td></tr>
		  <tr><td><b>17</b></td><td>COMPUTER PROGRAMMING</td></tr>
		  <tr><td><b>18</b></td><td>PHILOSOPHY</td></tr>
		  <tr><td><b>19</b></td><td>PSYCHOLOGY</td></tr>
		  <tr><td><b>20</b></td><td>ASTRONOMY</td></tr>
		</tbody>
	  </table>
	</div>

	<!-- Form 4 Table Container -->
	<div id="form4-container">
	  <table id="form4-table">
		<thead>
		  <tr>
			<th>No.</th>
			<th>Form 4 Subjects</th>
		  </tr>
		</thead>
		<tbody>
		  <tr><td><b>1</b></td><td>BAHASA MELAYU</td></tr>
		  <tr><td><b>2</b></td><td>ENGLISH LANGUAGE</td></tr>
		  <tr><td><b>3</b></td><td>MATHEMATICS</td></tr>
		  <tr><td><b>4</b></td><td>HISTORY</td></tr>
		  <tr><td><b>5</b></td><td>ISLAMIC STUDIES</td></tr>
		  <tr><td><b>6</b></td><td>ADDITIONAL MATHEMATICS</td></tr>
		  <tr><td><b>7</b></td><td>PHYSICAL & HEALTH EDUCATION</td></tr>
		  <tr><td><b>8</b></td><td>BIOLOGY</td></tr>
		  <tr><td><b>9</b></td><td>PHYSICS</td></tr>
		  <tr><td><b>10</b></td><td>CHEMISTRY</td></tr>
		  <tr><td><b>11</b></td><td>MORAL EDUCATION</td></tr>
		  <tr><td><b>12</b></td><td>JAPANESE LANGUAGE</td></tr>
		  <tr><td><b>13</b></td><td>ARABIC LANGUAGE</td></tr>
		  <tr><td><b>14</b></td><td>CHINESE LANGUAGE (MANDARIN)</td></tr>
		  <tr><td><b>15</b></td><td>ADDITIONAL SCIENCE</td></tr>
		  <tr><td><b>16</b></td><td>ENGLISH LITERATURE</td></tr>
		  <tr><td><b>17</b></td><td>ACCOUNTING</td></tr>
		  <tr><td><b>18</b></td><td>ECONOMICS</td></tr>
		  <tr><td><b>19</b></td><td>STATISTICS</td></tr>
		  <tr><td><b>20</b></td><td>GRAPHIC DESIGN</td></tr>
		</tbody>
	  </table>
	</div>

	<!-- Subject Not Found Message -->
	<p id="no-results-message" style="text-align: center; display: none; color: red; font-weight: bold; margin-top: 20px;">
	  Subject not found
	</p>

  </div>

  <!-- JavaScript -->
  <script>
    function searchSubjects() {
	  var input = document.getElementById("search-box");
	  var filter = input.value.toUpperCase().trim();

	  var table1 = document.getElementById("form1-table");
	  var table2 = document.getElementById("form4-table");
	  var rows1 = table1.getElementsByTagName("tr");
	  var rows2 = table2.getElementsByTagName("tr");

	  var found1 = false;
	  var found2 = false;

	  // Process Form 1 rows (skip header at i = 0)
	  for (let i = 1; i < rows1.length; i++) {
		let td = rows1[i].getElementsByTagName("td")[1];
		if (td) {
		  let text = td.textContent || td.innerText;
		  let match = text.toUpperCase().includes(filter);
		  rows1[i].style.display = match ? "" : "none";
		  if (match) found1 = true;
		}
	  }

	  // Process Form 4 rows (skip header at i = 0)
	  for (let i = 1; i < rows2.length; i++) {
		let td = rows2[i].getElementsByTagName("td")[1];
		if (td) {
		  let text = td.textContent || td.innerText;
		  let match = text.toUpperCase().includes(filter);
		  rows2[i].style.display = match ? "" : "none";
		  if (match) found2 = true;
		}
	  }

	  // Toggle visibility of tables
	  document.getElementById("form1-container").style.display = found1 ? "block" : "none";
	  document.getElementById("form4-container").style.display = found2 ? "block" : "none";

	  // Show error message only if neither table has matches
	  document.getElementById("no-results-message").style.display = (found1 || found2) ? "none" : "block";
	}
  </script>
</body>

</html>
<?php include "../header/footer.php" ?>