<?php
session_start();
include("../config.php");

if (isset($_POST['submit']) || isset($_POST['username'])) {
    // Sanitize and validate role input
    $role = SecuritySanitizer::sanitize($_POST['role'] ?? '', 'status');
    
    // Validate role is one of allowed values
    if (!in_array($role, ['admin', 'teacher', 'student'])) {
        header("Location: error/invalid_role.php");
        exit();
    }

    // CAPTCHA check for student/teacher
    if (($role === 'teacher' || $role === 'student') && 
        (!isset($_POST['captcha_input']) || 
         SecuritySanitizer::sanitize($_POST['captcha_input'], 'name') !== $_SESSION['captcha_text'])) {
        header("Location: error/error_captcha.php");
        exit();
    }

    // Sanitize credentials
    $username = SecuritySanitizer::sanitizeForDB($_POST['username'] ?? '', 'username', 'username');
    $password = SecuritySanitizer::sanitizeForDB($_POST['password'] ?? '', 'password', 'password');

    // Validate inputs
    if (!SecuritySanitizer::validate($username, 'text', 'username') || 
        !SecuritySanitizer::validate($password, 'password', 'password')) {
        header("Location: error/invalid_input.php");
        exit();
    }

    if ($role === 'admin') {
        $query = "SELECT * FROM admin WHERE ADMIN_USERNAME=? AND ADMIN_PWD=?";
        $sessionKey = 'adminID';
        $sessionValue = 'ADMIN_ID';
        $redirect = "../admin/Admin_home.php";
        $error = "error/error_admin.php";
    } elseif ($role === 'teacher') {
        $query = "SELECT * FROM teacher WHERE TEACHER_USERNAME=? AND TEACHER_PWD=?";
        $sessionKey = 'validTC';
        $sessionValue = 'TEACHER_ID';
        $redirect = "../teacher/teacher_home.php";
        $error = "error/error_pageTC.php";
    } elseif ($role === 'student') {
        $query = "SELECT * FROM student WHERE STUDENT_ID=? AND STUDENT_PWD=?";
        $sessionKey = 'valid';
        $sessionValue = 'STUDENT_ID';
        $redirect = "../student/student_home.php";
        $error = "error/error_page.php";
    } else {
        header("Location: error/invalid_role.php");
        exit();
    }

    // Use prepared statement for better security
    $stmt = mysqli_prepare($con, $query);
    if (!$stmt) {
        error_log("Prepare failed: " . mysqli_error($con));
        header("Location: error/database_error.php");
        exit();
    }
    
    mysqli_stmt_bind_param($stmt, "ss", $username, $password);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);
    $row = mysqli_fetch_assoc($result);

    if ($row) {
        $_SESSION[$sessionKey] = $row[$sessionValue];

        if ($role === 'student') {
            $stmt2 = mysqli_prepare($con, "SELECT STUDENT_NAME FROM student WHERE STUDENT_ID=?");
            mysqli_stmt_bind_param($stmt2, "s", $username);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            $row2 = mysqli_fetch_assoc($result2);
            header("Location: " . ($row2['STUDENT_NAME'] ? $redirect : "../student/StudentRegistration.php"));
            mysqli_stmt_close($stmt2);
        } elseif ($role === 'teacher') {
            $stmt2 = mysqli_prepare($con, "SELECT TEACHER_NAME FROM teacher WHERE TEACHER_ID=?");
            mysqli_stmt_bind_param($stmt2, "s", $row[$sessionValue]);
            mysqli_stmt_execute($stmt2);
            $result2 = mysqli_stmt_get_result($stmt2);
            $row2 = mysqli_fetch_assoc($result2);
            header("Location: " . ($row2['TEACHER_NAME'] ? $redirect : "../teacher/TeacherRegister.php"));
            mysqli_stmt_close($stmt2);
        } else {
            header("Location: $redirect");
        }
    } else {
        header("Location: $error");
    }

    mysqli_stmt_close($stmt);
    unset($_SESSION['captcha_text']);
    exit();
}
?>
