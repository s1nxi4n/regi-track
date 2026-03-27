<?php

require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/session.php';

startSession();
requireLogin(); // Make sure user is logged in

$studentId = $_SESSION['user_id'] ?? '';
$role = $_SESSION['role'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmPassword = $_POST['confirm_password'] ?? '';

if (empty($studentId) || empty($newPassword) || empty($confirmPassword)) {
    $_SESSION['password_error'] = 'All fields are required.';
    header('Location: ../../views/change-password.php');
    exit;
}

if ($newPassword !== $confirmPassword) {
    $_SESSION['password_error'] = 'Passwords do not match.';
    header('Location: ../../views/change-password.php');
    exit;
}

if (strlen($newPassword) < 6) {
    $_SESSION['password_error'] = 'Password must be at least 6 characters.';
    header('Location: ../../views/change-password.php');
    exit;
}

$hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);
updateUser($studentId, [
    'password' => $hashedPassword,
    'is_first_login' => false
]);

$_SESSION['password_success'] = 'Password changed successfully.';

if ($role === ROLE_ADMIN) {
    header('Location: ../../views/admin/dashboard.php');
} else {
    header('Location: ../../views/student/dashboard.php');
}
exit;
