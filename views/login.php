<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth-check.php';

if (isLoggedIn()) {
    $redirect = $_SESSION['role'] === ROLE_ADMIN ? '../views/admin/dashboard.php' : '../views/student/dashboard.php';
    header('Location: ' . $redirect);
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
    <title>Login - RegiTrack</title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">
</head>
<body>
    <div class="login-page">
        <div class="login-card">
            <div class="login-header">
                <div class="login-logo">📋</div>
                <h1 class="login-title">RegiTrack</h1>
                <p class="login-subtitle">Appointment Tracking System</p>
            </div>
            
            <?php if ($error): ?>
                <div class="alert alert-danger">
                    <span class="alert-icon">⚠️</span>
                    <div class="alert-content">
                        <div class="alert-message"><?= htmlspecialchars($error) ?></div>
                    </div>
                </div>
            <?php endif; ?>
            
            <form action="../actions/auth/login.php" method="POST" class="login-form">
                <div class="form-group">
                    <label for="student_id" class="form-label required">Student ID / Email</label>
                    <input 
                        type="text" 
                        id="student_id" 
                        name="student_id" 
                        class="form-input" 
                        placeholder="xx-xxxx-xxxxxx or email@phinmaed.com"
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
                        placeholder="Enter your password"
                        required
                        autocomplete="current-password"
                    >
                </div>
                
                <button type="submit" class="btn btn-primary btn-lg">
                    Sign In
                </button>
            </form>
            
            <p class="text-center text-muted mt-6 text-sm">
                For admin access, use username: <code class="font-mono">admin</code>
            </p>
        </div>
    </div>
</body>
</html>
