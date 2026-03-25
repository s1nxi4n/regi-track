<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$studentId = trim($_POST['student_id'] ?? '');
$fullName = trim($_POST['full_name'] ?? '');
$email = trim($_POST['email'] ?? '');

if (empty($studentId)) {
    $_SESSION['add_student_error'] = 'Student ID is required.';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

if (!preg_match('/^\d{2}-\d{4}-\d{6}$/', $studentId)) {
    $_SESSION['add_student_error'] = 'Invalid format. Use: xx-xxxx-xxxxxx';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

if (empty($fullName)) {
    $_SESSION['add_student_error'] = 'Full name is required.';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

if (empty($email)) {
    $_SESSION['add_student_error'] = 'Email is required.';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

if (!preg_match('/^.+\.ui@phinmaed\.com$/', $email)) {
    $_SESSION['add_student_error'] = 'Invalid email format. Use: *.ui@phinmaed.com';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

$existingUser = getUser($studentId);
if ($existingUser) {
    $_SESSION['add_student_error'] = 'Student already exists.';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

createUser($studentId, $studentId, ROLE_STUDENT, $fullName, $email);

logAdminAction($_SESSION['student_id'], 'Added student', $studentId, 'Created account with default password');

$_SESSION['add_student_success'] = 'Student added successfully! Default password: ' . htmlspecialchars($studentId);
header('Location: ../../views/admin/add-student.php');
exit;
