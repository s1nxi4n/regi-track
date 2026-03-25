<?php
require_once __DIR__ . '/../config/constants.php';
require_once __DIR__ . '/../includes/auth-check.php';

$error = $_SESSION['password_error'] ?? '';
$success = $_SESSION['password_success'] ?? '';
unset($_SESSION['password_error'], $_SESSION['password_success']);

$pageTitle = 'Change Password';

$isAdmin = isset($_SESSION['role']) && $_SESSION['role'] === ROLE_ADMIN;
if ($isAdmin) {
    require_once __DIR__ . '/../includes/firebase-helper.php';
    $currentPage = 'password';
    include_once __DIR__ . '/../includes/layout-admin.php';
} else {
    require_once __DIR__ . '/../includes/firebase-helper.php';
    $studentId = $_SESSION['student_id'] ?? '';
    $unreadCount = getUnreadNotificationCount($studentId);
    $currentPage = 'password';
    include_once __DIR__ . '/../includes/layout-student.php';
}
?>

<?php if ($success): ?>
    <div class="alert alert-success mb-6">
        <span class="alert-icon">✅</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($success) ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger mb-6">
        <span class="alert-icon">⚠️</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($error) ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 500px;">
    <div class="card-header">
        <div>
            <h3 class="card-title">🔐 Change Password</h3>
            <p class="card-subtitle">Update your account password</p>
        </div>
    </div>
    
    <form action="../actions/auth/change-password.php" method="POST">
        <div class="form-group">
            <label for="new_password" class="form-label required">New Password</label>
            <input 
                type="password" 
                id="new_password" 
                name="new_password" 
                class="form-input" 
                placeholder="Enter new password"
                required 
                minlength="6"
            >
            <p class="form-hint">Minimum 6 characters</p>
        </div>
        
        <div class="form-group">
            <label for="confirm_password" class="form-label required">Confirm New Password</label>
            <input 
                type="password" 
                id="confirm_password" 
                name="confirm_password" 
                class="form-input" 
                placeholder="Confirm new password"
                required 
                minlength="6"
            >
        </div>
        
        <div class="flex gap-4 mt-6">
            <a href="<?= $isAdmin ? '/views/admin/dashboard.php' : '/views/student/dashboard.php' ?>" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Change Password</button>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../includes/layout-end.php'; ?>
