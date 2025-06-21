<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/modal.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <title>Register Teacher</title>
</head>

<body style="background-image: url(../../image/teacher.png); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
    <script type="module" src="..\..\chatbox\index-Dsumbowl.js"></script>
    <link rel="stylesheet" href="..\..\chatbox\index-vXR3yhj7.css">
    <div id="root"></div>
    <div class="container-sign">
        <div class="box form-box">

            <?php
            session_start();
            include("../config.php");

            $id = $_SESSION['validTC'];
            
            // Use prepared statement to get teacher info
            $query = "SELECT TEACHER_ID FROM teacher WHERE TEACHER_ID = ?";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "s", $id);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);
            $teacher = mysqli_fetch_assoc($result);
            $res_id = $teacher['TEACHER_ID'] ?? null;
            mysqli_stmt_close($stmt);

            if (isset($_POST['submit'])) {
                
                try {
                    // Sanitize and validate input data
                    $username = SecuritySanitizer::sanitizeForDB($_POST['username'] ?? '', 'username', 'TEACHER_USERNAME');
                    $password = SecuritySanitizer::sanitizeForDB($_POST['password'] ?? '', 'password', 'TEACHER_PWD');

                    // Validate required fields
                    if (empty($username) || empty($password)) {
                        echo "<div class='message error'>
                              <p>All fields are required!</p>
                              </div>";
                    } else {
                        // Check for malicious input
                        if (detectMaliciousInput($username) || detectMaliciousInput($password)) {
                            SecuritySanitizer::logSecurityEvent('malicious_input_detected', [
                                'field' => 'teacher_account_setup',
                                'teacher_id' => $id
                            ]);
                            echo "<div class='message error'>
                                  <p>Invalid input detected!</p>
                                  </div>";
                        } else {
                            // Check if username already exists using prepared statement
                            $checkUsername = "SELECT TEACHER_USERNAME FROM teacher WHERE TEACHER_USERNAME = ? AND TEACHER_ID != ?";
                            $stmt = mysqli_prepare($con, $checkUsername);
                            mysqli_stmt_bind_param($stmt, "ss", $username, $id);
                            mysqli_stmt_execute($stmt);
                            $checkResult = mysqli_stmt_get_result($stmt);
                            $usernameExists = mysqli_num_rows($checkResult);
                            mysqli_stmt_close($stmt);

                            if ($usernameExists > 0) {
                                echo "<div class='message error'>
                                      <p>Username already exists! Please choose a different username.</p>
                                      </div>";
                            } else {
                                // Update teacher credentials using prepared statement
                                $updateQuery = "UPDATE teacher SET TEACHER_USERNAME = ?, TEACHER_PWD = ? WHERE TEACHER_ID = ?";
                                $stmt = mysqli_prepare($con, $updateQuery);
                                
                                if ($stmt) {
                                    mysqli_stmt_bind_param($stmt, "sss", $username, $password, $id);
                                    
                                    if (mysqli_stmt_execute($stmt)) {
                                        SecuritySanitizer::logSecurityEvent('teacher_account_setup_completed', [
                                            'teacher_id' => $id,
                                            'username' => $username
                                        ]);
                                        $showModal = true;
                                    } else {
                                        SecuritySanitizer::logSecurityEvent('teacher_account_setup_failed', [
                                            'teacher_id' => $id,
                                            'error' => mysqli_error($con)
                                        ]);
                                        echo "<div class='message error'>
                                              <p>Error updating account. Please try again.</p>
                                              </div>";
                                    }
                                    mysqli_stmt_close($stmt);
                                } else {
                                    echo "<div class='message error'>
                                          <p>Database error. Please try again.</p>
                                          </div>";
                                }
                            }
                        }
                    }
                } catch (InvalidArgumentException $e) {
                    echo "<div class='message error'>
                          <p>Invalid input format. Please check your entries.</p>
                          </div>";
                }
            }

            ?>

            <header>Teacher Account Registration</header>
            <form id="teacherRegForm" action="" method="post">
                <input type="hidden" name="role" value="teacher">
                <div class="field input">
                    <label for="username">Username</label>
                    <input type="text" name="username" id="username" placeholder="Enter username here" autocomplete="off" required>
                </div>

                <div class="field input">
                    <label for="password">Password</label>
                    <input type="password" name="password" id="password" placeholder="Enter password here" autocomplete="off" required>
                </div>

                <div class="field">

                    <input type="submit" class="btn" name="submit" value="Register" required>
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
                    <button class="btn-primary" onclick="registerFace('<?php echo $username; ?>')">Submit</button>
                </div>
            </div>
        </div>
    </div>
</body>
<script src="../../js/modal.js"></script>
<?php if (isset($showModal) && $showModal): ?>
    <script>
        window.onload = function() {
            openModal('teacherRegForm');
        };
    </script>
<?php endif; ?>

</html>