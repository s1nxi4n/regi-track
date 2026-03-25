<?php

require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$studentId = $_SESSION['student_id'];

firebaseRequest(NOTIFICATIONS_PATH . '/' . $studentId, 'DELETE');

header('Location: ../../views/student/notifications.php');
exit;