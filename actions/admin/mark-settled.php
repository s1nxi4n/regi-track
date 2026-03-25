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

updateAppointment($id, ['status' => STATUS_SETTLED]);

logAdminAction($_SESSION['student_id'], 'Marked settled', $id, 'Appointment completed');

$_SESSION['manage_success'] = 'Appointment marked as settled.';
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
