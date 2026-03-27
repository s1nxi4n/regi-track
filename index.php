<?php

require_once __DIR__ . '/config/constants.php';
require_once __DIR__ . '/includes/session.php';

startSession();

// If user is logged in and visits root, send to their dashboard
if (isLoggedIn() && isSessionValid()) {
    if ($_SESSION['role'] === ROLE_ADMIN) {
        header('Location: /views/admin/dashboard.php');
    } else {
        header('Location: /views/student/dashboard.php');
    }
    exit;
}

// User not logged in - show login page
if (!isLoggedIn()) {
    header('Location: /views/login.php');
    exit;
}

// Handle 404 for non-existing routes
$requestUri = $_SERVER['REQUEST_URI'];
$path = parse_url($requestUri, PHP_URL_PATH);
$filePath = __DIR__ . $path;

if (!file_exists($filePath)) {
    // Show 404 page instead of redirecting
    http_response_code(404);
    echo '<!DOCTYPE html><html><head><title>404 Not Found</title></head><body>';
    echo '<h1>404 Not Found</h1>';
    echo '<p>The page you requested does not exist.</p>';
    echo '<a href="/views/login.php">Go to Login</a>';
    echo '</body></html>';
    exit;
}
