<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$studentId = trim($_POST['student_id'] ?? '');

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

$existingUser = getUser($studentId);
if ($existingUser) {
    $_SESSION['add_student_error'] = 'Student already exists.';
    header('Location: ../../views/admin/add-student.php');
    exit;
}

createUser($studentId, $studentId, ROLE_STUDENT);

logAdminAction($_SESSION['student_id'], 'Added student', $studentId, 'Created account with default password');

$_SESSION['add_student_success'] = 'Student added successfully! Default password: ' . htmlspecialchars($studentId);
header('Location: ../../views/admin/add-student.php');
exit;
