<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'View Appointment - RegiTrack';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: dashboard.php');
    exit;
}

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Appointment Details</h1>
    
    <div class="card">
        <h3><?= htmlspecialchars($APPOINTMENT_TYPES[$appointment['type']]['label'] ?? $appointment['type']) ?></h3>
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['date']) ?></p>
        <p><strong>Status:</strong> <span class="status-<?= str_replace(' ', '-', $appointment['status']) ?>"><?= htmlspecialchars($appointment['status']) ?></span></p>
        
        <?php if (!empty($appointment['details'])): ?>
        <div class="details-box">
            <h4>Details:</h4>
            <?php foreach ($appointment['details'] as $key => $value): ?>
                <?php if (!empty($value)): ?>
                <p><strong><?= htmlspecialchars(ucwords(str_replace('_', ' ', $key))) ?>:</strong> <?= htmlspecialchars($value) ?></p>
                <?php endif; ?>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($appointment['rejection_reason'])): ?>
        <div class="error">
            <strong>Rejection Reason:</strong> <?= htmlspecialchars($appointment['rejection_reason']) ?>
        </div>
        <?php endif; ?>
        
        <?php if (!empty($appointment['rescheduled_date'])): ?>
        <div class="success">
            <strong>Rescheduled Date:</strong> <?= htmlspecialchars($appointment['rescheduled_date']) ?>
        </div>
        <?php endif; ?>
    </div>
    
    <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
