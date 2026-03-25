<?php

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/auth-check.php';

if (!isLoggedIn()) {
    header('Location: /views/login.php');
    exit;
}

if ($_SESSION['role'] === ROLE_ADMIN) {
    header('Location: /views/admin/dashboard.php');
} else {
    header('Location: /views/student/dashboard.php');
}
exit;
