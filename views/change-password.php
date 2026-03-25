<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth-check.php';

$error = $_SESSION['password_error'] ?? '';
$success = $_SESSION['password_success'] ?? '';
unset($_SESSION['password_error'], $_SESSION['password_success']);

$pageTitle = 'Change Password - RegiTrack';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <div class="container">
        <h1>Change Password</h1>
        
        <?php if ($error): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <p>Welcome! Please change your default password to continue.</p>
        
        <form action="../actions/auth/change-password.php" method="POST">
            <div class="form-group">
                <label for="new_password">New Password</label>
                <input type="password" id="new_password" name="new_password" required minlength="6">
            </div>
            
            <div class="form-group">
                <label for="confirm_password">Confirm New Password</label>
                <input type="password" id="confirm_password" name="confirm_password" required minlength="6">
            </div>
            
            <button type="submit">Change Password</button>
            <a href="<?= $_SESSION['role'] === ROLE_ADMIN ? '/views/admin/dashboard.php' : '/views/student/dashboard.php' ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</body>
</html>
