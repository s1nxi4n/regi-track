<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($reason)) {
    $_SESSION['manage_error'] = 'Please provide a cancellation reason.';
    header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
    exit;
}

$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

$studentId = $appointment['student_id'];

updateAppointment($id, [
    'status' => STATUS_CANCELLED,
    'cancellation_reason' => $reason
]);

createNotification($studentId, $id, 'cancelled', 'Your appointment has been cancelled. Reason: ' . $reason);

$student = getUser($studentId);
logAdminAction($_SESSION['student_id'], 'Cancelled appointment', $id, ($student['full_name'] ?? $studentId) . ' - Reason: ' . $reason);

$_SESSION['manage_success'] = 'Appointment cancelled.';
header('Location: ../../views/admin/dashboard.php');
exit;
