<?php

require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

session_start();

$studentId = trim($_POST['student_id'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($studentId) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both student ID and password.';
    header('Location: ../../views/login.php');
    exit;
}

if ($studentId === 'admin') {
    $user = getUser('admin');
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = 'admin';
        $_SESSION['role'] = ROLE_ADMIN;
        header('Location: ../../views/admin/dashboard.php');
        exit;
    }
} else {
    $user = getUser($studentId);
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = $studentId;
        $_SESSION['role'] = $user['role'];
        
        if ($user['is_first_login']) {
            header('Location: ../../views/change-password.php');
            exit;
        }
        
        header('Location: ../../views/student/dashboard.php');
        exit;
    }
}

$_SESSION['login_error'] = 'Invalid credentials.';
header('Location: ../../views/login.php');
exit;
