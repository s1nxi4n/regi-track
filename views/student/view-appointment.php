<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'View Appointment';
$currentPage = 'dashboard';
$studentId = $_SESSION['student_id'];
$unreadCount = getUnreadNotificationCount($studentId);

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: dashboard.php');
    exit;
}

$typeIcons = [
    'tor' => '📄',
    'diploma' => '🎓',
    'request_rf' => '📋',
    'certificate' => '✅'
];
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<div class="card">
    <div class="card-header">
        <div class="flex items-center gap-4">
            <div class="appointment-type-icon" style="width:56px;height:56px;font-size:24px;">
                <?= $typeIcons[$appointment['type']] ?? '📋' ?>
            </div>
            <div>
                <h3 class="card-title"><?= htmlspecialchars($APPOINTMENT_TYPES[$appointment['type']]['label'] ?? $appointment['type']) ?></h3>
                <p class="card-subtitle mb-0">Appointment ID: <?= htmlspecialchars($id) ?></p>
            </div>
        </div>
        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $appointment['status'])) ?>">
            <?= htmlspecialchars($appointment['status']) ?>
        </span>
    </div>
    
    <div class="flex gap-6 mb-6">
        <div class="form-group mb-0">
            <label class="form-label">Date</label>
            <p class="text-primary font-mono"><?= htmlspecialchars($appointment['date']) ?></p>
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Created</label>
            <p class="text-muted"><?= htmlspecialchars($appointment['created_at'] ?? 'N/A') ?></p>
        </div>
    </div>
    
    <?php if (!empty($appointment['details'])): ?>
    <div class="form-group">
        <label class="form-label">Details</label>
        <div class="table-container">
            <table>
                <?php foreach ($appointment['details'] as $key => $value): ?>
                    <?php if (!empty($value)): ?>
                    <tr>
                        <th style="width:40%"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?></th>
                        <td><?= htmlspecialchars($value) ?></td>
                    </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
            </table>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['rejection_reason'])): ?>
    <div class="alert alert-danger">
        <span class="alert-icon">❌</span>
        <div class="alert-content">
            <div class="alert-title">Rejection Reason</div>
            <div class="alert-message"><?= htmlspecialchars($appointment['rejection_reason']) ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['cancellation_reason'])): ?>
    <div class="alert alert-danger">
        <span class="alert-icon">🚫</span>
        <div class="alert-content">
            <div class="alert-title">Cancellation Reason</div>
            <div class="alert-message"><?= htmlspecialchars($appointment['cancellation_reason']) ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['rescheduled_date'])): ?>
    <div class="alert alert-warning">
        <span class="alert-icon">📅</span>
        <div class="alert-content">
            <div class="alert-title">Rescheduled Date</div>
            <div class="alert-message">New date: <?= htmlspecialchars($appointment['rescheduled_date']) ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['reschedule_reason'])): ?>
    <div class="form-group">
        <label class="form-label">Your Reschedule Reason</label>
        <p><?= htmlspecialchars($appointment['reschedule_reason']) ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['admin_reschedule_reason'])): ?>
    <div class="alert alert-info">
        <span class="alert-icon">📅</span>
        <div class="alert-content">
            <div class="alert-title">Rescheduled by Admin</div>
            <div class="alert-message"><?= htmlspecialchars($appointment['admin_reschedule_reason']) ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <div class="flex gap-4 mt-6">
        <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
