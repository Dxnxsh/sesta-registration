<?php
session_start();
include("../config.php");

if (isset($_POST['submit'])) {
    $role = $_POST['role']; // Get the role (admin, teacher, student) from the form

    // Validate CAPTCHA for teacher and student roles only
    if (($role === 'teacher' || $role === 'student') && 
        (!isset($_POST['captcha_input']) || $_POST['captcha_input'] !== $_SESSION['captcha_text'])) {
        // CAPTCHA validation failed
        header("Location: error/error_captcha.php");
        exit();
    }

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if ($role === 'admin') {
        $query = "SELECT * FROM admin WHERE ADMIN_USERNAME='$username' AND ADMIN_PWD='$password'";
        $redirectSuccess = "../admin/admin_home.php";
        $redirectError = "error/error_admin.php";
        $sessionKey = 'adminID';
        $sessionValue = 'ADMIN_ID';
    } elseif ($role === 'teacher') {
        $query = "SELECT * FROM teacher WHERE TEACHER_USERNAME='$username' AND TEACHER_PWD='$password'";
        $redirectSuccess = "../teacher/teacher_home.php";
        $redirectError = "error/error_pageTC.php";
        $sessionKey = 'validTC';
        $sessionValue = 'TEACHER_ID';
    } elseif ($role === 'student') {
        $query = "SELECT * FROM student WHERE STUDENT_ID='$username' AND STUDENT_PWD='$password'";
        $redirectError = "error/error_page.php";
        $sessionKey = 'valid';
        $sessionValue = 'STUDENT_ID';
    } else {
        // Invalid role
        header("Location: error/invalid_role.php");
        exit();
    }

    $result = mysqli_query($con, $query) or die("Select Error");
    $row = mysqli_fetch_assoc($result);
    
    if (is_array($row) && !empty($row)) {
        $_SESSION[$sessionKey] = $row[$sessionValue];

        if ($role === 'student') {
            // Additional logic for students
            $studentId = $_SESSION['valid'];
            $result = mysqli_query($con, "SELECT * FROM student WHERE STUDENT_ID='$studentId'");
            $row = mysqli_fetch_assoc($result);

            if (isset($row['STUDENT_NAME']) && !empty($row['STUDENT_NAME'])) {
                header("Location: ../student/student_home.php");
            } else {
                header("Location: ../student/StudentRegistration.php");
            }
        } elseif ($role === 'teacher') {
            // Additional logic for teachers
            $teacherId = $_SESSION['validTC'];
            $result = mysqli_query($con, "SELECT * FROM teacher WHERE TEACHER_ID='$teacherId'");
            $row = mysqli_fetch_assoc($result);

            if (isset($row['TEACHER_NAME']) && !empty($row['TEACHER_NAME'])) {
                header("Location: ../teacher/teacher_home.php");
            } else {
                header("Location: ../teacher/TeacherRegister.php");
            }
        } else {
            header("Location: $redirectSuccess");
        }
    } else {
        header("Location: $redirectError");
    }
    unset($_SESSION['captcha_text']);
    exit();
}
?>