<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$id = $_POST['id'] ?? '';
$clearAll = $_POST['clear_all'] ?? '';

if ($clearAll) {
    $appointments = getAppointmentsByStudent($_SESSION['student_id']);
    foreach ($appointments as $aptId => $apt) {
        if (in_array($apt['status'], ['Rejected', 'Settled', 'No-Show', 'Cancelled'])) {
            deleteAppointment($aptId);
        }
    }
    $_SESSION['history_success'] = 'All history cleared.';
} elseif ($id) {
    $appointment = getAppointment($id);
    if ($appointment && $appointment['student_id'] === $_SESSION['student_id']) {
        deleteAppointment($id);
        $_SESSION['history_success'] = 'Item removed from history.';
    }
}

header('Location: ../../views/student/history.php');
exit;
