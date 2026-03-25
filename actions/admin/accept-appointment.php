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

updateAppointment($id, ['status' => STATUS_IN_PROCESS]);

logAdminAction($_SESSION['student_id'], 'Accepted appointment', $id, 'Pending → In Process');

$_SESSION['manage_success'] = 'Appointment accepted.';
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
