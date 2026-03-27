<?php

require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/session.php';

startSession();

$isAdminLogin = isset($_POST['admin_login']);
$password = $_POST['password'] ?? '';

if ($isAdminLogin) {
    $username = trim($_POST['admin_username'] ?? '');
    
    if (empty($username) || empty($password)) {
        $_SESSION['login_error'] = 'Please enter username and password.';
        header('Location: ../../views/admin/login.php');
        exit;
    }
    
    // Prevent student from logging in as admin
    if (strpos($username, '@') !== false || preg_match('/^\d{2}-\d{4}-\d{6}$/', $username)) {
        $_SESSION['login_error'] = 'Student login not allowed here. Use student portal.';
        header('Location: ../../views/admin/login.php');
        exit;
    }
    
    $user = getUser('admin');
    if ($user && password_verify($password, $user['password'])) {
        // Create session - this handles regeneration automatically
        createUserSession('admin', ROLE_ADMIN, $user['full_name'] ?? 'Administrator');
        
        $redirect = getSafeRedirect('/views/admin/dashboard.php');
        header('Location: ' . $redirect);
        exit;
    }
    
    $_SESSION['login_error'] = 'Invalid admin credentials.';
    header('Location: ../../views/admin/login.php');
    exit;
}

// Student login
$loginInput = trim($_POST['student_id'] ?? '');

if (empty($loginInput) || empty($password)) {
    $_SESSION['login_error'] = 'Please enter both student ID/email and password.';
    header('Location: ../../views/login.php');
    exit;
}

// Prevent admin from logging in as student
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
    // Create session - this handles regeneration automatically
    createUserSession($studentId, $user['role'], $user['full_name'] ?? $studentId);
    
    if ($user['is_first_login']) {
        header('Location: ../../views/change-password.php');
        exit;
    }
    
    $redirect = getSafeRedirect('/views/student/dashboard.php');
    header('Location: ' . $redirect);
    exit;
}

$_SESSION['login_error'] = 'Invalid credentials.';
header('Location: ../../views/login.php');
exit;
