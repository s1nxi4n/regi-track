<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$newDate = $_POST['new_date'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($newDate)) {
    $_SESSION['manage_error'] = 'Please select a new date.';
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

$oldDate = $appointment['date'];
$studentId = $appointment['student_id'];
$student = getUser($studentId);

updateAppointment($id, [
    'date' => $newDate,
    'rescheduled_date' => '',
    'reschedule_reason' => '',
    'admin_reschedule_reason' => $reason
]);

createNotification($studentId, $id, 'admin_rescheduled', 'Your appointment has been rescheduled from ' . $oldDate . ' to ' . $newDate . '. Reason: ' . $reason);

logAdminAction($_SESSION['student_id'], 'Rescheduled appointment', $id, ($student['full_name'] ?? $studentId) . " - $oldDate to $newDate. Reason: $reason");

$_SESSION['manage_success'] = 'Appointment rescheduled from ' . htmlspecialchars($oldDate) . ' to ' . htmlspecialchars($newDate);
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
