<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$scheduledDate = $_POST['scheduled_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($scheduledDate)) {
    $_SESSION['manage_error'] = 'Please select a pickup date.';
    header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
    exit;
}

if (empty($reason)) {
    $_SESSION['manage_error'] = 'Please provide a reason for reschedule.';
    header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
    exit;
}

$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

updateAppointment($id, [
    'date' => $scheduledDate,
    'rescheduled_by_admin' => $reason
]);

logAdminAction($_SESSION['student_id'], 'Rescheduled in-process appointment', $id, 'New date: ' . $scheduledDate . ', Reason: ' . $reason);

$_SESSION['manage_success'] = 'Appointment rescheduled to ' . htmlspecialchars($scheduledDate);
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
