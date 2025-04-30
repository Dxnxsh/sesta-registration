<?php session_start();

include("../config.php");
if (!isset($_SESSION['adminID'])) {
    header("Location: ../login-logout/loginadmin.php");
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
    <title>Assign Class</title>
    <style>
        
        body {
            font-family: Arial, sans-serif;
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

        label {
            display: block;
            margin-bottom: 5px;
        }

        input,
        select {
            width: 100%;
            padding: 8px;
            margin-bottom: 10px;
            box-sizing: border-box;
        }

        p {
            text-align: center;
            margin-top: 20px;
        }

        .button-update,
        .button-back {
            text-align: left;
            margin-bottom: 10px;
        }

        #update {
            background-color: #28A745;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 7px;
        }

        #back {
            background-color: #007BFF;
            color: #fff;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            border-radius: 7px;
        }

        #update:hover {
            background-color: #ed9005f2;
        }

        #back:hover {
            background-color: #d14529;
        }

</style>
</head>

<body
    style="background-image: url('../../image/admin.png'); background-repeat: no-repeat; background-attachment: fixed; background-size: 100% 100%">
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
        <option value="<?php echo $teachid ?>'-'<?php echo $teachName ?>" disabled selected>Select Teacher ID</option>
        <option value="">Revoke current Teacher</option>
 <?php
        if (mysqli_num_rows($queryClassTeacher) > 0) {
            while ($resultTeacher = mysqli_fetch_array($queryClassTeacher)) {
                $selected = ($resultTeacher["TEACHER_ID"] == $teachid) ? 'selected' : '';
                echo "<option value=" . $resultTeacher["TEACHER_ID"] . " $selected >" . $resultTeacher["TEACHER_ID"] . ' - ' . $resultTeacher["TEACHER_NAME"] . "</option>";
            }
        }
        ?>
    </select>
</p>
            <input type="hidden" name="cCode" id="cCode" value="<?php echo $classCode; ?>">
            <p>
                <span style="text-align: left"><strong>
                        <div class="button-update"><input name="submit" type="submit" id="update" value="UPDATE"></div>
                    </strong></span>
            </p>
            </p>
        </form>
        <div class="button-back"><a href="adminClass.php"><input name="back" type="submit" id="back"
                    value="BACK TO ADMIN CLASS"></a></div>
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
    const form = document.getElementById('form1');
    const saveButton = document.getElementById('update');

    saveButton.addEventListener('click', function (event) {
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
            .then(response => response.json())
            .then(data => {
                // Log the response for debugging
                console.log(data);

                // Handle the response from the server
                if (data.success) {
                    Swal.fire({
                        title: 'Class Record Updated',
                        text: 'The new class record has been updated successfully.',
                        icon: 'success',
                        confirmButtonColor: '#4caf50',
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'adminClass.php';
                        }
                    });
                } else {
                    Swal.fire({
                        title: 'Error',
                        text: 'Failed to update class record. Please check the console for details.',
                        icon: 'error',
                        confirmButtonColor: '#d14529',
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

<?php include "../header/footer.php" ?>