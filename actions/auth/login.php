<?php

require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

session_start();

$isAdminLogin = isset($_POST['admin_login']);
$password = $_POST['password'] ?? '';

if ($isAdminLogin) {
    $username = trim($_POST['admin_username'] ?? '');
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter username and password.';
        header('Location: ../../views/admin/login.php');
        exit;
    }
    
    if (strpos($username, '@') !== false || preg_match('/^\d{2}-\d{4}-\d{6}$/', $username)) {
        $_SESSION['login_error'] = 'Student login not allowed here. Use student portal.';
        header('Location: ../../views/admin/login.php');
        exit;
    }
    
    $user = getUser('admin');
    if ($user && password_verify($password, $user['password'])) {
        $_SESSION['student_id'] = 'admin';
        $_SESSION['role'] = ROLE_ADMIN;
        $_SESSION['full_name'] = $user['full_name'] ?? 'Administrator';
        header('Location: ../../views/admin/dashboard.php');
        exit;
    }
    
    $_SESSION['login_error'] = 'Invalid admin credentials.';
    header('Location: ../../views/admin/login.php');
    exit;
}

$loginInput = trim($_POST['student_id'] ?? '');

if (empty($loginInput) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both student ID/email and password.';
    header('Location: ../../views/login.php');
    exit;
}

if (strtolower($loginInput) === 'admin') {
    $_SESSION['login_error'] = 'Admin login not allowed here. Use admin portal.';
    header('Location: ../../views/login.php');
    exit;
}

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

$_SESSION['login_error'] = 'Invalid credentials.';
header('Location: ../../views/login.php');
exit;
