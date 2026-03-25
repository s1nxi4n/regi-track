<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$scheduledDate = $_POST['scheduled_date'] ?? '';

if (empty($scheduledDate)) {
    $_SESSION['manage_error'] = 'Please select a pickup date.';
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
    'status' => STATUS_SCHEDULED
]);

logAdminAction($_SESSION['student_id'], 'Scheduled pickup', $id, 'Pickup date: ' . $scheduledDate);

$_SESSION['manage_success'] = 'Appointment scheduled for pickup on ' . htmlspecialchars($scheduledDate);
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
