<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

$studentId = $appointment['student_id'];

updateAppointment($id, [
    'rescheduled_date' => '',
    'reschedule_reason' => '',
    'status' => STATUS_SCHEDULED
]);

createNotification($studentId, $id, 'reschedule_rejected', 'Your reschedule request has been denied. Your original date is retained.');

logAdminAction($_SESSION['student_id'], 'Rejected reschedule', $id, 'Reschedule request denied');

$_SESSION['manage_success'] = 'Reschedule rejected. Original date retained.';
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
