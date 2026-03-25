<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$reason = trim($_POST['reason'] ?? '');

$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: ../../views/student/dashboard.php');
    exit;
}

if ($appointment['status'] === STATUS_SCHEDULED && empty($reason)) {
    $_SESSION['cancel_error'] = 'Please provide a reason for cancellation.';
    header('Location: ../../views/student/dashboard.php');
    exit;
}

if ($appointment['status'] === STATUS_SCHEDULED) {
    updateAppointment($id, [
        'status' => STATUS_CANCELLED,
        'cancellation_reason' => $reason
    ]);
    logAdminAction('student', 'Cancelled appointment', $id, ($_SESSION['full_name'] ?? $_SESSION['student_id']) . ' - Reason: ' . $reason);
} else {
    updateAppointment($id, ['status' => STATUS_CANCELLED]);
}

header('Location: ../../views/student/dashboard.php');
exit;
