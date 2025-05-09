<?php 
   session_start();

   include("../config.php");
   if(!isset($_SESSION['valid'])){
       header("Location: ../login-logout/login.php");
   }

   // Check if the popup has already been shown
   if (!isset($_SESSION['popup_shown'])) {
       $_SESSION['popup_shown'] = false;
   }
?>
<?php include "../header/studentHeader.php" ?>
<!doctype html>

<html>
<head>
<meta charset="utf-8">
<title>Student Homepage</title>
    
<link rel="stylesheet" href="../../css/student_homeStyle.css"/>
<style>
/* Popup styling */
.popup {
    position: fixed;
    top: 50%;
    left: 50%;
    transform: translate(-50%, -50%);
    width: 1072px;
    height: 603px;
    background-color: white;
    border: 2px solid #ccc;
    box-shadow: 0 0 10px rgba(0, 0, 0, 0.5);
    display: none;
    z-index: 1000;
}

.popup img {
    width: 100%;
    height: 100%;
    object-fit: cover;
}

.popup-close {
    position: absolute;
    top: 10px;
    right: 25px;
    cursor: pointer;
    font-size: 37px;
    color: red;
    font-weight: bold;
}

/* Styling for the indicator */
.indicator {
    display: flex;
    justify-content: center;
    align-items: center;
    margin-top: 10px;
}

.indicator-line {
    width: 30px;
    height: 5px;
    background-color: #ccc;
    margin: 0 5px;
    transition: background-color 0.3s ease;
}

.indicator-line.active {
    background-color: #000; /* Highlight active indicator */
}
</style>
</head>

<body>
<?php include("../config.php"); 
            
            $id = $_SESSION['valid'];
            $query = mysqli_query($con,"SELECT*FROM student WHERE STUDENT_ID=$id");
            $queryClass  = mysqli_query($con,"SELECT student.*, class.* FROM student INNER JOIN class ON student.CLASS_CODE = class.CLASS_CODE
            WHERE student.STUDENT_ID = $id");

            // Initialize $res_Class with a default value
            $res_Class = '';
            while($result = mysqli_fetch_assoc($query)){
                $res_IC = $result['STUDENT_ID'];
                $res_Name = $result['STUDENT_NAME'];
                $res_DOB = $result['STUDENT_DOB'];
                
            }

            while($result = mysqli_fetch_assoc($queryClass)){
                $res_Class = $result['CLASS_NAME'];
                
            }

            if($res_Class==''){
                $res_Class='not assigned';
            }

            
          
 ?>

<div class="header">
      <h1 style="color: #FFFFFF">Student Profile</h1>
    </div>
    <div class="box">
        <div class="container">
            <table width="736" border="0">
              <tbody >
                <tr>
                <td class="line" style="color: black">STUDENT IC</td>
              <td class="line" style="color: #727268"><?php echo $res_IC ?></td>
            </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p></td>
                </tr>
                <tr>
                <td class="line" style="color: black">NAME</td>
              <td class="line" style="color: #727268"><?php echo $res_Name ?></td>
            </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p></td>
                </tr>
                <tr>
                <td class="line" style="color: black">DATE OF BIRTH</td>
              <td class="line" style="color: #727268"><?php echo $res_DOB ?></td>
        </tr>
                <tr>
                  <td colspan="2"><p>&nbsp;</p></td>
                </tr>
                <tr>
                <td class="line" style="color: black">CLASS</td>
              <td class="line" style="color: #727268"><?php echo $res_Class ?></td>
            </tr>
            </table>
        </div>
        <div class="button">
            <ul>
                <li>
                <a href="../../html/subject.html">
                        <img src="../../image/icon/subjectOutline.png" alt="Subject Outline">
                </a>
                </li>
                <li>
                <a href="billing/billing.php">
                        <img src="../../image/icon/studentBill.png" alt="Billing">
            </a>
                </li>
                <li>
                <a href="studentCard.php">
                        <img src="../../image/icon/studentCard.png" alt="Student Card">
            </a>
                </li>
            </ul>
        </div>
    </div>

    <!-- Popup for cycling images -->
    <div class="popup" id="imagePopup">
        <span class="popup-close" id="popupClose">&times;</span>
        <img id="popupImage" src="" alt="Cycling Images">
        <div class="indicator" id="imageIndicator"></div> <!-- Indicator container -->
    </div>

    <script>
    // JavaScript for cycling images with indicators
    const images = [
        "../../image/bg1.png",
        "../../image/bg3.jpg",
        "../../image/bg6.png"
    ]; // Add paths to your images here

    let currentIndex = 0;
    const popup = document.getElementById('imagePopup');
    const popupImage = document.getElementById('popupImage');
    const popupClose = document.getElementById('popupClose');
    const indicator = document.getElementById('imageIndicator');

    // Create indicators dynamically based on the number of images
    function createIndicators() {
        indicator.innerHTML = ''; // Clear existing indicators
        images.forEach((_, index) => {
            const line = document.createElement('div');
            line.className = 'indicator-line';
            if (index === 0) line.classList.add('active'); // Set the first indicator as active
            indicator.appendChild(line);
        });
    }

    // Update the active indicator
    function updateIndicators() {
        const lines = document.querySelectorAll('.indicator-line');
        lines.forEach((line, index) => {
            if (index === currentIndex) {
                line.classList.add('active');
            } else {
                line.classList.remove('active');
            }
        });
    }

    function showPopup() {
        popup.style.display = 'block';
        createIndicators(); // Create indicators when the popup is shown
        cycleImages();
    }

    function cycleImages() {
        popupImage.src = images[currentIndex];
        updateIndicators(); // Update the indicator for the current image
        currentIndex = (currentIndex + 1) % images.length;
        setTimeout(cycleImages, 3000); // Change image every 3 seconds
    }

    popupClose.addEventListener('click', () => {
        popup.style.display = 'none';
    });

    // Show popup only if it hasn't been shown yet
    window.onload = () => {
        <?php if (!$_SESSION['popup_shown']): ?>
            showPopup();
            <?php $_SESSION['popup_shown'] = true; ?>
        <?php endif; ?>
    };
    </script>
</body>
</html>
<?php include "../header/footer.php" ?>

