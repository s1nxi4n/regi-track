<?php

require_once __DIR__ . '/../config/firebase.php';

function firebaseRequest($path, $method = 'GET', $data = null) {
    $url = FIREBASE_DB_URL . $path . '.json?auth=' . FIREBASE_SECRET;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    
    switch ($method) {
        case 'POST':
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PUT':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'PATCH':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PATCH');
            curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
            break;
        case 'DELETE':
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
            break;
    }
    
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}

function getUsers() {
    return firebaseRequest(USERS_PATH) ?? [];
}

function getUser($studentId) {
    $users = firebaseRequest(USERS_PATH) ?? [];
    return $users[$studentId] ?? null;
}

function createUser($studentId, $password, $role = ROLE_STUDENT) {
    return firebaseRequest(USERS_PATH . '/' . $studentId, 'PUT', [
        'student_id' => $studentId,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'is_first_login' => true,
        'role' => $role
    ]);
}

function updateUser($studentId, $data) {
    return firebaseRequest(USERS_PATH . '/' . $studentId, 'PATCH', $data);
}

function getAppointments() {
    return firebaseRequest(APPOINTMENTS_PATH) ?? [];
}

function getAppointment($id) {
    $appointments = getAppointments();
    return $appointments[$id] ?? null;
}

function getAppointmentsByStudent($studentId) {
    $appointments = getAppointments();
    $result = [];
    foreach ($appointments as $id => $appointment) {
        if ($appointment['student_id'] === $studentId) {
            $result[$id] = $appointment;
        }
    }
    return $result;
}

function createAppointment($data) {
    $id = uniqid('apt_');
    return firebaseRequest(APPOINTMENTS_PATH . '/' . $id, 'PUT', $data);
}

function updateAppointment($id, $data) {
    return firebaseRequest(APPOINTMENTS_PATH . '/' . $id, 'PATCH', $data);
}

function deleteAppointment($id) {
    return firebaseRequest(APPOINTMENTS_PATH . '/' . $id, 'DELETE');
}

function logAdminAction($adminId, $action, $appointmentId, $details = '') {
    $timestamp = date('Y-m-d H:i:s');
    firebaseRequest(ADMIN_LOGS_PATH, 'POST', [
        'timestamp' => $timestamp,
        'admin_id' => $adminId,
        'action' => $action,
        'appointment_id' => $appointmentId,
        'details' => $details
    ]);
}

function getAdminLogs() {
    return firebaseRequest(ADMIN_LOGS_PATH) ?? [];
}
