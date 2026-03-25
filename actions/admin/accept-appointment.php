<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$scheduledDate = $_POST['scheduled_date'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

if (empty($scheduledDate)) {
    $_SESSION['manage_error'] = 'Please select a pickup date.';
    header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
    exit;
}

updateAppointment($id, [
    'date' => $scheduledDate,
    'status' => STATUS_SCHEDULED
]);

$studentId = $appointment['student_id'];
createNotification($studentId, $id, 'accepted', 'Your appointment has been accepted and scheduled for ' . $scheduledDate);

logAdminAction($_SESSION['student_id'], 'Accepted appointment', $id, 'Pending → Scheduled on ' . $scheduledDate);

$_SESSION['manage_success'] = 'Appointment accepted and scheduled for ' . htmlspecialchars($scheduledDate);
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
