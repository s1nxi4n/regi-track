<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$id = $_POST['id'] ?? '';
$reason = trim($_POST['reason'] ?? '');

if (empty($reason)) {
    $_SESSION['manage_error'] = 'Rejection reason is required.';
    header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
    exit;
}

$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: ../../views/admin/dashboard.php');
    exit;
}

updateAppointment($id, [
    'status' => STATUS_REJECTED,
    'rejection_reason' => $reason
]);

logAdminAction($_SESSION['student_id'], 'Rejected appointment', $id, 'Reason: ' . $reason);

$_SESSION['manage_success'] = 'Appointment rejected.';
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
