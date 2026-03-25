<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$id = $_POST['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

$newDate = $appointment['rescheduled_date'];

updateAppointment($id, [
    'date' => $newDate,
    'rescheduled_date' => '',
    'status' => STATUS_IN_PROCESS
]);

logAdminAction($_SESSION['student_id'], 'Accepted reschedule', $id, 'New date: ' . $newDate);

$_SESSION['manage_success'] = 'Reschedule accepted. Date updated to ' . htmlspecialchars($newDate);
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
