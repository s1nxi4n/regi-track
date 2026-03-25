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
</head>
<body class="login-page">
    <div class="login-container">
        <h1>RegiTrack</h1>
        <h2>Appointment Tracking System</h2>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form action="../actions/auth/login.php" method="POST">
            <div class="form-group">
                <label for="student_id">Student ID / Admin Username</label>
                <input type="text" id="student_id" name="student_id" required 
                       placeholder="xx-xxxx-xxxxxx or admin">
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <button type="submit">Login</button>
        </form>
    </div>
</body>
</html>
