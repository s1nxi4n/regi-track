<?php

session_start();

function requireAuth() {
    if (!isset($_SESSION['student_id'])) {
        header('Location: ../views/login.php');
        exit;
    }
}

function requireRole($role) {
    requireAuth();
    if ($_SESSION['role'] !== $role) {
        header('Location: ../index.php');
        exit;
    }
}

function requireOnceRole($role) {
    requireRole($role);
}

function isLoggedIn() {
    return isset($_SESSION['student_id']);
}

function getCurrentUser() {
    if (!isLoggedIn()) {
        return null;
    }
    return [
        'student_id' => $_SESSION['student_id'],
        'role' => $_SESSION['role']
    ];
}
