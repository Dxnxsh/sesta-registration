<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="../../css/style.css">
    <link rel="stylesheet" href="../../css/modal.css">
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
            $query = mysqli_query($con, "SELECT*FROM teacher WHERE TEACHER_ID=$id");

            while ($result = mysqli_fetch_assoc($query)) {
                $res_id = $result['TEACHER_ID'];
            }

            if (isset($_POST['submit'])) {
                $username = $_POST['username'];
                $password = $_POST['password'];

                mysqli_query($con, "UPDATE `teacher` SET `TEACHER_USERNAME`='$username', `TEACHER_PWD`='$password' WHERE `TEACHER_ID`='$id'") or die("Error Occurred student " . mysqli_error($con));


                $showModal = true;
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