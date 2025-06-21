<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
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
                
                try {
                    // Sanitize and validate input data
                    $ic = SecuritySanitizer::sanitizeForDB($_POST['ic'] ?? '', 'id', 'STUDENT_ID');
                    $email = SecuritySanitizer::sanitizeForDB($_POST['email'] ?? '', 'email', 'STUDENT_EMAIL');
                    $pwd = SecuritySanitizer::sanitizeForDB($_POST['pwd'] ?? '', 'password', 'STUDENT_PWD');
                    $pwd2 = SecuritySanitizer::sanitizeForDB($_POST['pwd2'] ?? '', 'password', 'STUDENT_PWD');

                    // Validate required fields
                    if (empty($ic) || empty($email) || empty($pwd) || empty($pwd2)) {
                        header("Location: error/error_page.php");
                        exit();
                    }

                    // Check for malicious input
                    $inputs = [$ic, $email, $pwd, $pwd2];
                    foreach ($inputs as $input) {
                        if (detectMaliciousInput($input)) {
                            SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                                'field' => 'student_registration',
                                'student_id' => $ic
                            ]);
                            header("Location: error/error_page.php");
                            exit();
                        }
                    }

                } catch (InvalidArgumentException $e) {
                    header("Location: error/error_page.php");
                    exit();
                }

                // Verify password match
                if ($pwd != $pwd2) {
                    header("Location: error/error_page1.php");
                    exit();
                }

                // Check if student ID already exists using prepared statement
                $verify_query = "SELECT STUDENT_ID FROM student WHERE STUDENT_ID = ?";
                $stmt = mysqli_prepare($con, $verify_query);
                
                if (!$stmt) {
                    header("Location: error/error_page.php");
                    exit();
                }
                
                mysqli_stmt_bind_param($stmt, "s", $ic);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) != 0) {
                    mysqli_stmt_close($stmt);
                    header("Location: error/error_page2.php");
                    exit();
                }
                mysqli_stmt_close($stmt);

                // Insert student record using prepared statement
                $insert_student = "INSERT INTO student (STUDENT_ID, STUDENT_EMAIL, STUDENT_PWD) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($con, $insert_student);
                
                if (!$stmt) {
                    header("Location: error/error_page.php");
                    exit();
                }
                
                mysqli_stmt_bind_param($stmt, "sss", $ic, $email, $pwd);
                
                if (!mysqli_stmt_execute($stmt)) {
                    mysqli_stmt_close($stmt);
                    header("Location: error/error_page.php");
                    exit();
                }
                mysqli_stmt_close($stmt);

                // Insert payment records using prepared statements
                $payment_records = [
                    ['200', 'SCHOOL FEES', 'UNPAID'],
                    ['210', 'DORMITORY FEES', 'UNPAID'],
                    ['100', 'PIBG FEES', 'UNPAID']
                ];

                $insert_payment = "INSERT INTO payment (PAYMENT_AMOUNT, PAYMENT_TYPE, PAYMENT_STATUS, STUDENT_ID) VALUES (?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $insert_payment);
                
                if (!$stmt) {
                    header("Location: error/error_page.php");
                    exit();
                }

                $payment_success = true;
                foreach ($payment_records as $payment) {
                    mysqli_stmt_bind_param($stmt, "dsss", $payment[0], $payment[1], $payment[2], $ic);
                    if (!mysqli_stmt_execute($stmt)) {
                        $payment_success = false;
                        break;
                    }
                }
                mysqli_stmt_close($stmt);

                if (!$payment_success) {
                    SecuritySanitizer::logSecurityEvent('student_account_creation_failed', [
                        'student_id' => $ic,
                        'error' => 'payment_insertion_failed'
                    ]);
                    header("Location: error/error_page.php");
                    exit();
                }

                SecuritySanitizer::logSecurityEvent('student_account_created', [
                    'student_id' => $ic,
                    'email' => $email
                ]);

                $showModal = true;
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
                    <input type="email" name="email" id="email" autocomplete="off" required>
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