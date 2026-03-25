<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Manage Appointment - RegiTrack';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: dashboard.php');
    exit;
}

$student = getUser($appointment['student_id']);
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
        <p><strong>Name:</strong> <?= htmlspecialchars($student['full_name'] ?? 'N/A') ?></p>
        <p><strong>Email:</strong> <?= htmlspecialchars($student['email'] ?? 'N/A') ?></p>
        <p><strong>Type:</strong> <?= htmlspecialchars($APPOINTMENT_TYPES[$appointment['type']]['label'] ?? $appointment['type']) ?></p>
        <p><strong>Date:</strong> <?= htmlspecialchars($appointment['date']) ?></p>
        <?php if (!empty($appointment['rescheduled_date'])): ?>
        <p><strong>Requested New Date:</strong> <?= htmlspecialchars($appointment['rescheduled_date']) ?></p>
        <?php endif; ?>
        <?php if (!empty($appointment['reschedule_reason'])): ?>
        <p><strong>Reschedule Reason:</strong> <?= htmlspecialchars($appointment['reschedule_reason']) ?></p>
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
    
    <?php if ($appointment['status'] === STATUS_PENDING && empty($appointment['rescheduled_date'])): ?>
    <div class="actions">
        <button onclick="openModal('acceptModal')" class="btn btn-success">Accept & Schedule</button>
        <button onclick="openModal('rejectModal')" class="btn btn-danger">Reject</button>
    </div>
    
    <div id="acceptModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('acceptModal')">&times;</span>
            <h2>Schedule Pickup</h2>
            <form action="../../actions/admin/accept-appointment.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-group">
                    <label for="scheduled_date">Pickup Date</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" required min="<?= date('Y-m-d') ?>">
                </div>
                <button type="submit" class="btn btn-success">Accept & Schedule</button>
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
            </form>
        </div>
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
    
    <?php if ($appointment['status'] === STATUS_PENDING && !empty($appointment['rescheduled_date'])): ?>
    <div class="actions">
        <form action="../../actions/admin/accept-reschedule.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">Accept Reschedule</button>
        </form>
        
        <form action="../../actions/admin/reject-reschedule.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Reject Reschedule</button>
        </form>
    </div>
    <?php endif; ?>
    
    <?php if ($appointment['status'] === STATUS_SCHEDULED): ?>
    <div class="actions">
        <button onclick="openModal('rescheduleModal')" class="btn btn-primary">Reschedule</button>
        <form action="../../actions/admin/mark-settled.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">Mark as Settled</button>
        </form>
        
        <form action="../../actions/admin/mark-no-show.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">Mark as No-Show</button>
        </form>
        
        <button onclick="openModal('cancelModal')" class="btn btn-danger">Cancel Appointment</button>
    </div>
    
    <div id="rescheduleModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('rescheduleModal')">&times;</span>
            <h2>Reschedule Appointment</h2>
            <form action="../../actions/admin/admin-reschedule.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-group">
                    <label for="new_date">New Date</label>
                    <input type="date" id="new_date" name="new_date" required min="<?= date('Y-m-d') ?>">
                </div>
                <div class="form-group">
                    <label for="reason">Reason for Reschedule</label>
                    <textarea id="reason" name="reason" required></textarea>
                </div>
                <button type="submit" class="btn btn-primary">Reschedule</button>
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
            </form>
        </div>
    </div>
    
    <div id="cancelModal" class="modal">
        <div class="modal-content">
            <span class="close-modal" onclick="closeModal('cancelModal')">&times;</span>
            <h2>Cancel Appointment</h2>
            <form action="../../actions/admin/cancel-appointment.php" method="POST">
                <input type="hidden" name="id" value="<?= $id ?>">
                <div class="form-group">
                    <label for="reason">Cancellation Reason (Required)</label>
                    <textarea id="reason" name="reason" required></textarea>
                </div>
                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
                <button type="button" class="btn btn-secondary modal-cancel">Cancel</button>
            </form>
        </div>
    </div>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
