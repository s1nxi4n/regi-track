<?php

// Simple session management - easy to understand

// Start session
function startSession() {
    // Set secure cookie settings
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false, // set to true if using HTTPS
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    session_start();
}

// Regenerate session ID (prevents session hijacking)
function regenerateSession() {
    session_regenerate_id(true);
}

// Destroy session completely
function destroySession() {
    $_SESSION = [];
    
    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params['path'], $params['domain'],
            $params['secure'], $params['httponly']
        );
    }
    
    session_destroy();
}

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Check if session is valid (not expired)
function isSessionValid($timeout = 1800) { // 30 minutes
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    
    $elapsed = time() - $_SESSION['last_activity'];
    return $elapsed < $timeout;
}

// Update last activity time
function updateActivity() {
    $_SESSION['last_activity'] = time();
}

// Create user session after login
function createUserSession($userId, $role, $fullName) {
    regenerateSession(); // Prevent session fixation
    
    $_SESSION['user_id'] = $userId;
    $_SESSION['student_id'] = $userId; // Backward compatibility
    $_SESSION['role'] = $role;
    $_SESSION['full_name'] = $fullName;
    $_SESSION['login_time'] = time();
    $_SESSION['last_activity'] = time();
}

// Require user to be logged in
function requireLogin() {
    startSession();
    
    if (!isLoggedIn() || !isSessionValid()) {
        destroySession();
        header('Location: /views/login.php');
        exit;
    }
    
    updateActivity();
}

// Require specific role
function requireRole($requiredRole) {
    requireLogin();
    
    if ($_SESSION['role'] !== $requiredRole) {
        // Redirect to their own dashboard
        if ($_SESSION['role'] === 'admin') {
            header('Location: /views/admin/dashboard.php');
        } else {
            header('Location: /views/student/dashboard.php');
        }
        exit;
    }
}

// Get safe redirect URL (prevents open redirect)
function getSafeRedirect($default = '/views/student/dashboard.php') {
    if (!isset($_SESSION['redirect_after_login'])) {
        return $default;
    }
    
    $redirect = $_SESSION['redirect_after_login'];
    unset($_SESSION['redirect_after_login']);
    
    // Only allow relative paths starting with /
    if (strpos($redirect, '/') !== 0) {
        return $default;
    }
    
    // Block dangerous redirects
    $blocked = ['://', 'javascript:', 'data:'];
    foreach ($blocked as $pattern) {
        if (strpos($redirect, $pattern) !== false) {
            return $default;
        }
    }
    
    // Only allow known safe paths
    $allowed = [
        '/views/student/dashboard.php',
        '/views/admin/dashboard.php',
        '/views/change-password.php'
    ];
    
    if (!in_array($redirect, $allowed)) {
        return $default;
    }
    
    return $redirect;
}

// Set redirect URL
function setRedirect($url) {
    $_SESSION['redirect_after_login'] = $url;
}
