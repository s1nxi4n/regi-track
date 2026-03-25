<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'Manage Appointment - RegiTrack';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: dashboard.php');
    exit;
}

$success = $_SESSION['manage_success'] ?? '';
unset($_SESSION['manage_success']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Manage Appointment</h1>
    
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <div class="card">
        <h3>Appointment Details</h3>
        <p><strong>Student ID:</strong> <?= htmlspecialchars($appointment['student_id']) ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($APPOINTMENT_TYPES[$appointment['type']]['label'] ?? $appointment['type']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['date']) ?></p>
        <?php if (!empty($appointment['rescheduled_date'])): ?>
        <p><strong>Requested New Date:</strong> <?= htmlspecialchars($appointment['rescheduled_date']) ?></p>
        <?php endif; ?>
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
    </div>
    
    <?php if ($appointment['status'] === STATUS_PENDING): ?>
    <div class="actions">
        <form action="../../actions/admin/accept-appointment.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">Accept</button>
        </form>
        
        <button onclick="openModal('rejectModal')" class="btn btn-danger">Reject</button>
    </div>
    
    <div id="rejectModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('rejectModal')">&times;</span>
            <h2>Reject Appointment</h2>
            <form action="../../actions/admin/reject-appointment.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-group">
                    <label for="reason">Rejection Reason (Required)</label>
                    <textarea id="reason" name="reason" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if ($appointment['status'] === STATUS_SCHEDULED && !empty($appointment['rescheduled_date'])): ?>
    <div class="actions">
        <form action="../../actions/admin/accept-reschedule.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">Accept Reschedule</button>
        </form>
        
        <form action="../../actions/admin/reject-reschedule.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Reject Reschedule</button>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if ($appointment['status'] === STATUS_IN_PROCESS): ?>
    <div class="actions">
        <form action="../../actions/admin/mark-settled.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">Mark as Settled</button>
        </form>
        
        <form action="../../actions/admin/mark-no-show.php" method="POST" style="display:inline;">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Mark as No-Show</button>
        </form>
    </div>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary" style="margin-top: 1rem;">Back to Dashboard</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
