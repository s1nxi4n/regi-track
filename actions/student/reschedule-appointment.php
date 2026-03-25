<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$id = $_POST['id'] ?? '';
$newDate = $_POST['new_date'] ?? '';

$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: ../../views/student/dashboard.php');
    exit;
}

if ($appointment['status'] !== STATUS_SCHEDULED) {
    header('Location: ../../views/student/dashboard.php');
    exit;
}

if (empty($newDate)) {
    $_SESSION['reschedule_error'] = 'Please select a new date.';
    header('Location: ../../views/student/reschedule-appointment.php?id=' . $id);
    exit;
}

updateAppointment($id, [
    'rescheduled_date' => $newDate,
    'status' => STATUS_PENDING
]);

header('Location: ../../views/student/dashboard.php');
exit;
