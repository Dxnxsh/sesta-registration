<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <title>Register Student</title>
</head>

<body
    style="background-image: url(../../image/bg11.jpeg); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script type="module" src="..\..\chatbox\index-Dsumbowl.js"></script>
    <link rel="stylesheet" href="..\..\chatbox\index-vXR3yhj7.css">
    <div id="root"></div>
    <div class="container-sign">
        <div class="box form-box">

            <?php

            include("../config.php");
            if (isset($_POST['submit'])) {
                $ic = $_POST['ic'];
                $email = $_POST['email'];
                $pwd = $_POST['pwd'];
                $pwd2 = $_POST['pwd2'];

                //verifying the ic and password

                $verify_query = mysqli_query($con, "SELECT STUDENT_ID FROM student WHERE student_id='$ic'");

                if ($pwd != $pwd2) {
                    // Redirect to the error page
                    header("Location: error/error_page1.php");
                    exit();
                } else if (mysqli_num_rows($verify_query) != 0) {
                    // Redirect to the error page
                    header("Location: error/error_page2.php");
                    exit();
                } else {

                    mysqli_query($con, "INSERT INTO `student`(`STUDENT_ID`,`STUDENT_EMAIL`, `STUDENT_PWD`) VALUES('$ic','$email', '$pwd')") or die("Error Occurred");
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('200', 'SCHOOL FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('210', 'DORMITORY FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));
                    mysqli_query($con, "INSERT INTO `payment`(`PAYMENT_AMOUNT`, `PAYMENT_TYPE`, `PAYMENT_STATUS`, `STUDENT_ID`) VALUES('100', 'PIBG FEES', 'UNPAID', '$ic')") or die('Error: ' . mysqli_error($con));

                    $showModal = true;
                }
            }


            ?>

            <header>Sign Up Student</header>
            <form id="studentRegForm" action="" method="post">
                <input type="hidden" name="role" value="student">
                <div class="field input">
                    <label for="ic">IC</label>
                    <input type="text" name="ic" id="ic" maxlength="12" autocomplete="off" pattern="\d{12}" required>
                </div>

                <div class="field input">
                    <label for="email">Email</label>
                    <input type="text" name="email" id="email" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="pwd">Password</label>
                    <input type="password" name="pwd" id="pwd" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password2">Re-enter Password</label>
                    <input type="password" name="pwd2" id="pwd2" autocomplete="off" required>
                </div>

                <input type="submit" class="btn" name="submit" value="Register" required>
                <div class="links">
                    Already a member? <a href="login.php">Sign In</a>
                </div>
            </form>
        </div>
    </div>
    <!-- Face Verification Modal -->
    <div id="faceModal" class="modal">
        <div class="modal-overlay"></div>
        <div class="modal-container">
            <div class="modal-content">
                <div class="modal-header">
                    <h2>Face Verification</h2>
                    <button class="close-button" onclick="closeModal()">×</button>
                </div>

                <div class="tabs">
                    <div class="tab-buttons">
                        <button id="uploadTabBtn" class="tab active" onclick="openTab('uploadTab')">
                            <span class="icon">📁</span>
                            <span class="label">Upload Image</span>
                        </button>
                        <button id="webcamTabBtn" class="tab" onclick="openTab('webcamTab')">
                            <span class="icon">📷</span>
                            <span class="label">Use Webcam</span>
                        </button>
                    </div>

                    <div class="tab-content-container">
                        <div id="uploadTab" class="tab-content active">
                            <div class="file-upload-container">
                                <div class="file-upload-area" id="dropArea">
                                    <div class="file-upload-prompt">
                                        <div class="upload-icon">📷</div>
                                        <p>Drag and drop your image here</p>
                                        <p>or</p>
                                        <label for="faceFile" class="custom-file-input">Choose File</label>
                                        <input type="file" accept="image/*" id="faceFile" class="hidden-file-input">
                                    </div>
                                    <div class="file-preview" id="filePreview"></div>
                                </div>
                            </div>
                        </div>

                        <div id="webcamTab" class="tab-content">
                            <div class="webcam-container">
                                <video id="video" width="100%" autoplay playsinline></video>
                                <canvas id="canvas" style="display: none;"></canvas>
                                <div class="capture-container">
                                    <div id="capturedImage" class="captured-image"></div>
                                </div>
                                <button id="captureBtn" class="btn-accent" onclick="captureFace()">
                                    <span class="icon">📸</span> Capture Face
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button class="btn-secondary" onclick="closeModal()">Cancel</button>
                    <button class="btn-primary" onclick="registerFace('<?php echo $ic; ?>')">Submit</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../../js/modal.js"></script>
<?php if (isset($showModal) && $showModal): ?>
    <script>
        window.onload = function() {
            openModal('studentRegForm');
        };
    </script>
<?php endif; ?>

</html>