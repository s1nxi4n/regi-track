<?php

require_once __DIR__ . '/session.php';
startSession();

function requireAuth() {
    requireLogin();
}

function requireOnceRole($role) {
    requireRole($role);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'student_id' => $_SESSION['user_id'],
        'role' => $_SESSION['role'],
        'full_name' => $_SESSION['full_name']
    ];
}
