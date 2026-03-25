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
$student = getUser($studentId);

updateAppointment($id, ['status' => STATUS_NO_SHOW]);

createNotification($studentId, $id, 'no_show', 'You did not show up for your appointment. Please contact the registrar.');

logAdminAction($_SESSION['student_id'], 'Marked no-show', $id, ($student['full_name'] ?? $studentId) . ' - Did not arrive');

$_SESSION['manage_success'] = 'Appointment marked as no-show.';
header('Location: ../../views/admin/manage-appointment.php?id=' . $id);
exit;
