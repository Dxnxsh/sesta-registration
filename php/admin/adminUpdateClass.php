<?php session_start();

include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

// Handle url parameter
if (isset($_GET['id'])) {
    $classcode = $_GET['id'];
}


if (isset($_GET['id'])) {
    $classCode = $_GET['id'];
    $selectClass = "SELECT * FROM class
  WHERE CLASS_CODE = '$classCode'";
    $queryClass = mysqli_query($con, $selectClass);


    // Fetch and process the results
    while ($row = mysqli_fetch_assoc($queryClass)) {
        // Process each row of data
        // $row contains the combined data from both "teacher" and "class" tables
        $className = $row['CLASS_NAME'];
        $classlvl = $row['CLASS_LEVEL'];
        $blck = $row['CLASS_BLOCK'];
        $flr = $row['CLASS_FLOOR'];
        $cat = $row['CLASS_CAT'];
    }
}

// Fetch the list of teachers who are not assigned to any class
$selectClassTeacher = "SELECT TEACHER_ID, TEACHER_NAME FROM teacher
                      WHERE NOT EXISTS (SELECT 1 FROM class WHERE class.TEACHER_ID = teacher.TEACHER_ID)";
$queryClassTeacher = mysqli_query($con, $selectClassTeacher); ?>

<?php include "../header/adminHeader.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <link rel="stylesheet" href="../../css/button.css">
    <title>Assign Class</title>
    <link rel="stylesheet" href="../../css/admin-common.css">
    <link rel="stylesheet" href="../../css/button.css">
    <style>
        /* Page-specific styles for adminUpdateClass.php */
        /* Override container for form pages - minimum width */
        .container {
            min-width: 30%;
        }

        h2 {
            text-align: center;
            color: #333;
        }

        input,
        select {
            margin-bottom: 10px;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>

<body>
    <div class="container">
        <form id="form1" name="form1" method="POST">
            <h2>Update Class Detail</h2>
            <p>
                <label for="name">Name:</label>
                <input name="name" type="text" id="name" value="<?php echo $className ?>">
            </p>
            <p>
                <label for="level">Study level:</label>
                <select name="level" id="level" required>
                    <option value="Form 1" <?php echo ($classlvl == 'Form 1') ? 'selected' : ''; ?>>Form 1</option>
                    <option value="Form 4" <?php echo ($classlvl == 'Form 4') ? 'selected' : ''; ?>>Form 4</option>
                </select>
            </p>
            <p>
                <label for="block">Block:</label>
                <select name="block" id="block" required>
                    <option value="A" <?php echo ($blck == 'A') ? 'selected' : ''; ?>>Block A</option>
                    <option value="B" <?php echo ($blck == 'B') ? 'selected' : ''; ?>>Block B</option>
                    <option value="C" <?php echo ($blck == 'C') ? 'selected' : ''; ?>>Block C</option>
                    <option value="D" <?php echo ($blck == 'D') ? 'selected' : ''; ?>>Block D</option>
                    <option value="E" <?php echo ($blck == 'E') ? 'selected' : ''; ?>>Block E</option>
                </select>
            </p>
            <p>
                <label for="floor">Floor:</label>
                <select name="floor" id="floor" required>
                    <option value="1" <?php echo ($flr == '1') ? 'selected' : ''; ?>>1st Floor</option>
                    <option value="2" <?php echo ($flr == '2') ? 'selected' : ''; ?>>2nd Floor</option>
                    <option value="3" <?php echo ($flr == '3') ? 'selected' : ''; ?>>3rd Floor</option>
                </select>
            </p>
            <p>
                <label for="category">Category:</label>
                <select name="category" id="category" value="" required>
                    <option value="" disabled>Select Category</option>
                    <option value="Main Stream" class="form1-option" <?php echo ($cat == 'Main Stream') ? 'selected' : ''; ?>>Main Stream</option>
                    <option value="Science Stream" class="form4-option" <?php echo ($cat == 'Science Stream') ? 'selected' : ''; ?>>Science Stream</option>
                    <option value="Art Stream" class="form4-option" <?php echo ($cat == 'Art Stream') ? 'selected' : ''; ?>>Art Stream</option>
                    <option value="STEM" class="form4-option" <?php echo ($cat == 'STEM') ? 'selected' : ''; ?>>STEM</option>
                </select>

            <p>Teacher ID:
                <select name="teacherID" required>
                    <option value="" disabled>Select Teacher ID</option>
                    <option value="">Revoke current Teacher</option>
                    <?php
                    // Fetch the current teacher assigned to the class
                    $currentTeacherQuery = "SELECT TEACHER_ID, TEACHER_NAME FROM teacher WHERE TEACHER_ID = (SELECT TEACHER_ID FROM class WHERE CLASS_CODE = '$classCode')";
                    $currentTeacherResult = mysqli_query($con, $currentTeacherQuery);
                    $currentTeacher = mysqli_fetch_assoc($currentTeacherResult);

                    if ($currentTeacher) {
                        echo "<option value='" . $currentTeacher["TEACHER_ID"] . "' selected>" . $currentTeacher["TEACHER_ID"] . " - " . $currentTeacher["TEACHER_NAME"] . "</option>";
                    }

                    // Populate the list of teachers who are not assigned to any class
                    if (mysqli_num_rows($queryClassTeacher) > 0) {
                        while ($resultTeacher = mysqli_fetch_array($queryClassTeacher)) {
                            $selected = isset($teachid) && ($resultTeacher["TEACHER_ID"] == $teachid) ? 'selected' : '';
                            echo "<option value='" . $resultTeacher["TEACHER_ID"] . "' $selected>" . $resultTeacher["TEACHER_ID"] . " - " . $resultTeacher["TEACHER_NAME"] . "</option>";
                        }
                    }
                    ?>
                </select>
            </p>
            <input type="hidden" name="cCode" id="cCode" value="<?php echo $classCode; ?>">

            <div class="buttons">
                <a href="adminClass.php"><input class="back-button" type="button" value="Back"></a>
                <div class="spacer"></div>
                <button type="submit" id="update" name="submit" value="update">Update</button>
            </div>
        </form>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function() {
            $('#level').change(function() {
                // Get the selected value
                var selectedLevel = $(this).val();

                // Show/hide options based on the selected level
                if (selectedLevel === 'Form 1') {
                    $('#category').val('Main Stream'); // Automatically set the value to 'Arus Perdana'
                    $('.form1-option').show();
                    $('.form4-option').hide();
                } else if (selectedLevel === 'Form 4') {
                    $('.form1-option').hide();
                    $('.form4-option').show();
                } else {
                    $('.form1-option').hide();
                    $('.form4-option').hide();
                }
            });
        });




        document.addEventListener('DOMContentLoaded', function() {
            const form = document.getElementById('form1');
            const saveButton = document.getElementById('update');

            saveButton.addEventListener('click', function(event) {
                event.preventDefault();

                // Gather form data
                const formData = new FormData(form);

                // Log form data for debugging
                for (var pair of formData.entries()) {
                    console.log(pair[0] + ', ' + pair[1]);
                }

                // Submit form data using AJAX
                fetch('update_class.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => {
                        // Check if response is ok
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(data => {
                        // Log the response for debugging
                        console.log('Response data:', data);

                        // Handle the response from the server
                        if (data.success) {
                            if (data.error.includes('No changes were made to the class information')) {
                                Swal.fire({
                                    title: 'No Changes Made',
                                    text: data.error,
                                    icon: 'info',
                                    confirmButtonColor: '#4caf50',
                                }).then(() => {
                                    // Redirect to the class list page
                                    window.location.href = 'adminClass.php';
                                });
                            } else {
                                // Show success message
                                Swal.fire({
                                    title: 'Class Record Updated',
                                    text: 'The new class record has been updated successfully.',
                                    icon: 'success',
                                    confirmButtonColor: '#4caf50',
                                }).then(() => {
                                    // Redirect to the class list page
                                    window.location.href = 'adminClass.php';
                                });
                            }
                        } else {
                            // Check if it's a teacher assignment error
                            if (data.error && data.error.includes('Teacher is already assigned')) {
                                Swal.fire({
                                    title: 'Teacher Assignment Error',
                                    text: data.error,
                                    icon: 'error',
                                    confirmButtonColor: '#d14529',
                                });
                            } else {
                                Swal.fire({
                                    title: 'Error',
                                    text: data.error || 'Failed to update class record. Please check the console for details.',
                                    icon: 'error',
                                    confirmButtonColor: '#d14529',
                                });
                            }
                        }
                    })
                    .catch(error => {
                        console.error('Fetch error:', error);
                        Swal.fire({
                            title: 'Network Error',
                            text: 'Failed to connect to server. Please check your connection and try again.',
                            icon: 'error',
                            confirmButtonColor: '#d14529',
                        });
                    });
            });
        });
    </script>
</body>

</html>

<?php include "../header/footer.php" ?>