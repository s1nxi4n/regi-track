<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
require_once __DIR__ . '/../../includes/icon.php';

if (isLoggedIn() && $_SESSION['role'] === ROLE_ADMIN) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['login_error'] ?? '';
unset($_SESSION['login_error']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - RegiTrack</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237c5cff' stroke-width='2'><path d='M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2'/><rect x='8' y='2' width='8' height='4' rx='1' ry='1'/></svg>">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo"><?= icon('clipboard', 48) ?></div>
                <h1 class="login-title">Admin Portal</h1>
                <p class="login-subtitle">RegiTrack Appointment System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <?= icon('alert') ?>
                    <div class="alert-content">
                        <div class="alert-message"><?= htmlspecialchars($error) ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="../../actions/auth/login.php" method="POST" class="login-form">
                <input type="hidden" name="admin_login" value="1">
                
                <div class="form-group">
                    <label for="username" class="form-label required">Username</label>
                    <input 
                        type="text" 
                        id="username" 
                        name="admin_username" 
                        class="form-input" 
                        placeholder="Enter admin username"
                        required
                        autocomplete="username"
                    >
                </div>
                
                <div class="form-group">
                    <label for="password" class="form-label required">Password</label>
                    <input 
                        type="password" 
                        id="password" 
                        name="password" 
                        class="form-input" 
                        placeholder="Enter password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    Sign In
                </button>
            </form>
            
            <p class="text-center mt-6">
                <a href="/login" class="text-sm">Switch to Student Login</a>
            </p>
        </div>
    </div>
</body>
</html>
