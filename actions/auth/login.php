<?php

require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

session_start();

$loginInput = trim($_POST['student_id'] ?? '');
$password = $_POST['password'] ?? '';

if (empty($loginInput) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both student ID/email and password.';
    header('Location: ../../views/login.php');
    exit;
}

if ($loginInput === 'admin') {
    $user = getUser('admin');
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = 'admin';
        $_SESSION['role'] = ROLE_ADMIN;
        $_SESSION['full_name'] = $user['full_name'] ?? 'Administrator';
        header('Location: ../../views/admin/dashboard.php');
        exit;
    }
} else {
    $user = null;
    $studentId = $loginInput;
    
    if (strpos($loginInput, '@') !== false) {
        $result = getUserByEmail($loginInput);
        if ($result) {
            $user = $result['user'];
            $studentId = $result['id'];
        }
    } else {
        $user = getUser($loginInput);
    }
    
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = $studentId;
        $_SESSION['role'] = $user['role'];
        $_SESSION['full_name'] = $user['full_name'] ?? $studentId;
        
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
