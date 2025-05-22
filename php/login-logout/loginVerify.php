<?php
session_start();
include("../config.php");

if (isset($_POST['submit']) || isset($_POST['username'])) {
    $role = $_POST['role'];

    // CAPTCHA check for student/teacher
    if (($role === 'teacher' || $role === 'student') && 
        (!isset($_POST['captcha_input']) || $_POST['captcha_input'] !== $_SESSION['captcha_text'])) {
        header("Location: error/error_captcha.php");
        exit();
    }

    $username = mysqli_real_escape_string($con, $_POST['username']);
    $password = mysqli_real_escape_string($con, $_POST['password']);

    if ($role === 'admin') {
        $query = "SELECT * FROM admin WHERE ADMIN_USERNAME='$username' AND ADMIN_PWD='$password'";
        $sessionKey = 'adminID';
        $sessionValue = 'ADMIN_ID';
        $redirect = "../admin/admin_home.php";
        $error = "error/error_admin.php";
    } elseif ($role === 'teacher') {
        $query = "SELECT * FROM teacher WHERE TEACHER_USERNAME='$username' AND TEACHER_PWD='$password'";
        $sessionKey = 'validTC';
        $sessionValue = 'TEACHER_ID';
        $redirect = "../teacher/teacher_home.php";
        $error = "error/error_pageTC.php";
    } elseif ($role === 'student') {
        $query = "SELECT * FROM student WHERE STUDENT_ID='$username' AND STUDENT_PWD='$password'";
        $sessionKey = 'valid';
        $sessionValue = 'STUDENT_ID';
        $redirect = "../student/student_home.php";
        $error = "error/error_page.php";
    } else {
        header("Location: error/invalid_role.php");
        exit();
    }

    $result = mysqli_query($con, $query) or die("Select Error");
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $_SESSION[$sessionKey] = $row[$sessionValue];

        if ($role === 'student') {
            $check = mysqli_query($con, "SELECT STUDENT_NAME FROM student WHERE STUDENT_ID='$username'");
            $row = mysqli_fetch_assoc($check);
            header("Location: " . ($row['STUDENT_NAME'] ? $redirect : "../student/StudentRegistration.php"));
        } elseif ($role === 'teacher') {
            $check = mysqli_query($con, "SELECT TEACHER_NAME FROM teacher WHERE TEACHER_ID='$row[$sessionValue]'");
            $row = mysqli_fetch_assoc($check);
            header("Location: " . ($row['TEACHER_NAME'] ? $redirect : "../teacher/TeacherRegister.php"));
        } else {
            header("Location: $redirect");
        }
    } else {
        header("Location: $error");
    }

    unset($_SESSION['captcha_text']);
    exit();
}
?>
