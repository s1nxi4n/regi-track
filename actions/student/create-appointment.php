<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$type = $_POST['type'] ?? '';
$date = $_POST['date'] ?? '';
$details = $_POST['details'] ?? [];

if (empty($type) || empty($date)) {
    $_SESSION['create_error'] = 'Please fill in all required fields.';
    header('Location: ../../views/student/create-appointment.php');
    exit;
}

if (!isset($APPOINTMENT_TYPES[$type])) {
    $_SESSION['create_error'] = 'Invalid appointment type.';
    header('Location: ../../views/student/create-appointment.php');
    exit;
}

$requiredFields = $APPOINTMENT_TYPES[$type]['fields'] ?? [];
$filteredDetails = [];
foreach ($requiredFields as $field) {
    $filteredDetails[$field] = $details[$field] ?? '';
}

$appointmentData = [
    'student_id' => $_SESSION['student_id'],
    'type' => $type,
    'date' => $date,
    'status' => STATUS_PENDING,
    'details' => $filteredDetails,
    'created_at' => date('Y-m-d H:i:s')
];

createAppointment($appointmentData);

header('Location: ../../views/student/dashboard.php');
exit;
