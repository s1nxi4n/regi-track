<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$id = $_POST['id'] ?? '';
$type = $_POST['type'] ?? '';
$date = $_POST['date'] ?? '';
$details = $_POST['details'] ?? [];

$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: ../../views/student/dashboard.php');
    exit;
}

if ($appointment['status'] === STATUS_SCHEDULED) {
    $_SESSION['edit_error'] = 'Cannot edit scheduled appointments. Please reschedule instead.';
    header('Location: ../../views/student/edit-appointment.php?id=' . $id);
    exit;
}

if (empty($type) || empty($date)) {
    $_SESSION['edit_error'] = 'Please fill in all required fields.';
    header('Location: ../../views/student/edit-appointment.php?id=' . $id);
    exit;
}

$requiredFields = $APPOINTMENT_TYPES[$type]['fields'] ?? [];
$filteredDetails = [];
foreach ($requiredFields as $field) {
    $filteredDetails[$field] = $details[$field] ?? '';
}

updateAppointment($id, [
    'type' => $type,
    'date' => $date,
    'details' => $filteredDetails,
    'status' => STATUS_PENDING
]);

header('Location: ../../views/student/dashboard.php');
exit;
