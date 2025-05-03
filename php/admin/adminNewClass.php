<?php
session_start();
include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/login.php");
    exit();
}

$selectClassCategory = "SELECT CLASS_CODE, CLASS_NAME FROM class";
$queryClassCategory = mysqli_query($con, $selectClassCategory);

$selectClassTeacher = "SELECT TEACHER_ID, TEACHER_NAME FROM teacher
                      WHERE NOT EXISTS (SELECT 1 FROM class WHERE class.TEACHER_ID = teacher.TEACHER_ID)";
$queryClassTeacher = mysqli_query($con, $selectClassTeacher); 

// Check if the query result is empty
if (mysqli_num_rows($queryClassTeacher) == 0) {
    echo "<script>
        setTimeout(function() {
            Swal.fire({
                title: 'No Teacher Available!',
                text: 'Adding a class is prohibited when there are no available teachers.',
                icon: 'error'
            }).then(function() {
                window.location.href = 'adminClass.php';
            });
        }, 500); // 500 milliseconds = 0.5 seconds
    </script>";

    // Prevent further execution of the script
}

?>


<?php include "../header/adminHeader.php" ?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <title>Insert New Class Record</title>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-image: url("../../image/admin.png");
            background-repeat: no-repeat;
            background-attachment: fixed;
            background-size: 100% 100%;
            margin: 0;
        }

        .container {
            max-width: 600px;
            margin: 100px auto;
            padding: 20px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }

        h2 {
            text-align: center;
            color: #333;
        }

        form {
            display: grid;
            gap: 10px;
        }

        label {
            font-weight: bold;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            box-sizing: border-box;
        }

        .buttons {
            display: flex;
            justify-content: space-between;
        }

        button {
            padding: 10px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            color: #fff;
        }

        button[type="reset"] {
            background-color: #FF0004;
        }

        button[type="button"] {
            background-color: #4caf50;
        }

        .back-button {
            background-color: #4285f4;
            color: #fff;
            padding: 10px 15px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 10px;
            margin-bottom: 5px;
        }

        .back-button:hover {
            background-color: #357ae8;
        }
    </style>
</head>

<body>

    
    <div class="container">
        <h2>Insert New Class Record</h2>
        <form action="" name="classForm" method="POST" id="classForm">
    <p>
                <label for="code">Code:</label>
                <input type="text" name="code" id="code" required>

                <label for="name">Name:</label>
                <input type="text" name="name" id="name" required>

                <label for="level">Study level:</label>
                <select name="level" id="level" placeholder="<?php echo $lvl ?>" required>
                    <option value="Form 1">Form 1</option>
                    <option value="Form 4">Form 4</option>
                </select>

                <label for="block">Block:</label>
                <select name="block" id="block" value="<?php echo $blck ?>" required>
                    <option value="A">Block A</option>
                    <option value="B">Block B</option>
                    <option value="C">Block C</option>
                    <option value="C">Block D</option>
                    <option value="C">Block E</option>
                </select>
                <label for="floor">Floor:</label>
                <select name="floor" id="floor" value="<?php echo $flr ?>" required>
                    <option value="1">1st Floor</option>
                    <option value="2">2nd Floor</option>
                    <option value="3">3rd Floor</option>
                </select>


                <label for="category">Category:</label>
                <select name="category" id="category" value="" required>
                    <option value="" disabled selected>Select Category</option>
                    <option value="Main Stream" class="form1-option">Arus Perdana</option>
                    <option value="Science Stream" class="form4-option">Sains Tulen</option>
                    <option value="Art Stream" class="form4-option">Sastera</option>
                    <option value="STEM" class="form4-option">STEM</option>
                </select>

                <label for="teacherID">Teacher ID:</label>
                <select name="teacherID" required>
                    <option value="" disabled selected>Select Teacher ID</option>
                    <?php
                    if (mysqli_num_rows($queryClassTeacher) > 0) {
                        while ($resultTeacher = mysqli_fetch_array($queryClassTeacher)) {
                            echo "<option value=" . $resultTeacher["TEACHER_ID"] . ">" . $resultTeacher["TEACHER_ID"] . ' - ' . $resultTeacher["TEACHER_NAME"] . "</option>";
                        }
                    }
                    ?>
                </select>

            </p>
            <div class="buttons">
                <button type="reset">Reset</button>
                <button type="button" id="save" name="submit" value="classForm">Insert</button>
            </div>
        </form>
        <a href="adminClass.php"><input class="back-button" type="button" value="BACK"></a>

    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@10"></script>
    <script>
        $(document).ready(function () {
            $('#level').change(function () {
                // Get the selected value
                var selectedLevel = $(this).val();

                // Show/hide options based on the selected level
                if (selectedLevel === 'Form 1') {
                    $('#category').val('Main Stream');  // Automatically set the value to 'Arus Perdana'
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


    document.addEventListener('DOMContentLoaded', function () {
        const form = document.getElementById('classForm');
        const saveButton = document.getElementById('save');

        saveButton.addEventListener('click', function (event) {
            event.preventDefault();

            // Gather form data
            const formData = new FormData(form);

            // Submit form data using AJAX
            fetch('insert_class.php', {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Handle the response from the server
                if (data.success) {
                    Swal.fire({
                        title: 'Class Record Inserted',
                        text: 'The new class record has been inserted successfully.',
                        icon: 'success',
                        confirmButtonColor: '#4caf50',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'adminClass.php';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Class Code Exist',
                        text: 'Class code already exist, Please try another code.',
                        icon: 'error',
                        confirmButtonColor: '#FF0004',
                    });
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        });
    });
</script>
</body>

</html>