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
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"></path>
            <polyline points="22 4 12 14.01 9 11.01"></polyline>
        </svg>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($success) ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert alert-danger mb-6">
        <svg class="alert-icon" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
            <circle cx="12" cy="12" r="10"></circle>
            <line x1="12" y1="8" x2="12" y2="12"></line>
            <line x1="12" y1="16" x2="12.01" y2="16"></line>
        </svg>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($error) ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="card" style="max-width: 500px; <?= $isAdmin ? 'margin: 0 auto;' : '' ?>">
    <div class="card-header">
        <div class="flex items-center gap-3">
            <div class="stat-icon" style="width:40px;height:40px;background:var(--accent-dim);color:var(--accent);border-radius:var(--radius);display:flex;align-items:center;justify-content:center;">
                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"></rect>
                    <path d="M7 11V7a5 5 0 0 1 10 0v4"></path>
                </svg>
            </div>
            <div>
                <h3 class="card-title" style="margin-bottom:0;">Change Password</h3>
                <p class="card-subtitle">Update your account password</p>
            </div>
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
