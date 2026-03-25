<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Reschedule Appointment';
$currentPage = 'dashboard';
$studentId = $_SESSION['student_id'];
$unreadCount = getUnreadNotificationCount($studentId);

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: dashboard.php');
    exit;
}

if ($appointment['status'] !== STATUS_SCHEDULED) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['reschedule_error'] ?? '';
unset($_SESSION['reschedule_error']);

$today = date('Y-m-d');
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Request Reschedule</h3>
            <p class="card-subtitle">Change your appointment date</p>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger mb-6">
            <span class="alert-icon">⚠️</span>
            <div class="alert-content">
                <div class="alert-message"><?= htmlspecialchars($error) ?></div>
            </div>
        </div>
    <?php endif; ?>
    
    <div class="alert alert-info mb-6">
        <span class="alert-icon">ℹ️</span>
        <div class="alert-content">
            <div class="alert-title">Current Appointment</div>
            <div class="alert-message">
                Date: <strong><?= htmlspecialchars($appointment['date']) ?></strong> | 
                Status: <span class="status-badge status-scheduled"><?= htmlspecialchars($appointment['status']) ?></span>
            </div>
        </div>
    </div>
    
    <form action="../../actions/student/reschedule-appointment.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        
        <div class="form-group">
            <label for="new_date" class="form-label required">New Preferred Date</label>
            <input 
                type="date" 
                id="new_date" 
                name="new_date" 
                class="form-input" 
                min="<?= $today ?>"
                required
            >
        </div>
        
        <div class="form-group">
            <label for="reason" class="form-label required">Reason for Reschedule</label>
            <textarea 
                id="reason" 
                name="reason" 
                class="form-textarea" 
                placeholder="Please explain why you need to reschedule..."
                required
            ></textarea>
        </div>
        
        <div class="flex gap-4 mt-6">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Request Reschedule</button>
        </div>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
