<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Manage Appointment';
$currentPage = 'dashboard';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment) {
    header('Location: dashboard.php');
    exit;
}

$student = getUser($appointment['student_id']);
$success = $_SESSION['manage_success'] ?? '';
unset($_SESSION['manage_success']);

$typeIcons = [
    'tor' => '📄',
    'diploma' => '🎓',
    'request_rf' => '📋',
    'certificate' => '✅'
];

$today = date('Y-m-d');
?>

<?php include_once __DIR__ . '/../../includes/layout-admin.php'; ?>

<?php if ($success): ?>
    <div class="alert alert-success mb-6">
        <span class="alert-icon">✅</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($success) ?></div>
        </div>
    </div>
<?php endif; ?>

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
            <label class="form-label">Student ID</label>
            <p class="font-mono"><?= htmlspecialchars($appointment['student_id']) ?></p>
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Name</label>
            <p><?= htmlspecialchars($student['full_name'] ?? 'N/A') ?></p>
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Email</label>
            <p><?= htmlspecialchars($student['email'] ?? 'N/A') ?></p>
        </div>
        <div class="form-group mb-0">
            <label class="form-label">Date</label>
            <p class="font-mono"><?= htmlspecialchars($appointment['date']) ?></p>
        </div>
    </div>
    
    <?php if (!empty($appointment['rescheduled_date'])): ?>
    <div class="alert alert-warning mb-4">
        <span class="alert-icon">📅</span>
        <div class="alert-content">
            <div class="alert-title">Requested New Date</div>
            <div class="alert-message"><?= htmlspecialchars($appointment['rescheduled_date']) ?></div>
        </div>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['reschedule_reason'])): ?>
    <div class="form-group">
        <label class="form-label">Reschedule Reason</label>
        <p><?= htmlspecialchars($appointment['reschedule_reason']) ?></p>
    </div>
    <?php endif; ?>
    
    <?php if (!empty($appointment['details'])): ?>
    <div class="form-group">
        <label class="form-label">Request Details</label>
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
</div>

<?php if ($appointment['status'] === STATUS_PENDING && empty($appointment['rescheduled_date'])): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Review Request</h3>
    </div>
    <div class="flex gap-4">
        <button onclick="openModal('acceptModal')" class="btn btn-success">✅ Accept & Schedule</button>
        <button onclick="openModal('rejectModal')" class="btn btn-danger">❌ Reject</button>
    </div>
</div>

<div id="acceptModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Schedule Pickup</h3>
            <button type="button" class="modal-close" onclick="closeModal('acceptModal')">&times;</button>
        </div>
        <form action="../../actions/admin/accept-appointment.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="scheduled_date" class="form-label required">Pickup Date</label>
                    <input type="date" id="scheduled_date" name="scheduled_date" class="form-input" min="<?= $today ?>" required>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('acceptModal')">Cancel</button>
                <button type="submit" class="btn btn-success">Accept & Schedule</button>
            </div>
        </form>
    </div>
</div>

<div id="rejectModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Reject Appointment</h3>
            <button type="button" class="modal-close" onclick="closeModal('rejectModal')">&times;</button>
        </div>
        <form action="../../actions/admin/reject-appointment.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="reason" class="form-label required">Rejection Reason</label>
                    <textarea id="reason" name="reason" class="form-textarea" placeholder="Explain why this request is being rejected..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rejectModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Rejection</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($appointment['status'] === STATUS_PENDING && !empty($appointment['rescheduled_date'])): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Review Reschedule Request</h3>
    </div>
    <div class="flex gap-4">
        <form action="../../actions/admin/accept-reschedule.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">✅ Accept Reschedule</button>
        </form>
        <form action="../../actions/admin/reject-reschedule.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">❌ Reject Reschedule</button>
        </form>
    </div>
</div>
<?php endif; ?>

<?php if ($appointment['status'] === STATUS_SCHEDULED): ?>
<div class="card">
    <div class="card-header">
        <h3 class="card-title">Manage Appointment</h3>
    </div>
    <div class="flex gap-4 flex-wrap">
        <button onclick="openModal('rescheduleModal')" class="btn btn-primary">📅 Reschedule</button>
        <form action="../../actions/admin/mark-settled.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-success">✅ Mark as Settled</button>
        </form>
        <form action="../../actions/admin/mark-no-show.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <button type="submit" class="btn btn-danger">⚠️ Mark as No-Show</button>
        </form>
        <button onclick="openModal('cancelModal')" class="btn btn-danger">🚫 Cancel Appointment</button>
    </div>
</div>

<div id="rescheduleModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Reschedule Appointment</h3>
            <button type="button" class="modal-close" onclick="closeModal('rescheduleModal')">&times;</button>
        </div>
        <form action="../../actions/admin/admin-reschedule.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="new_date" class="form-label required">New Date</label>
                    <input type="date" id="new_date" name="new_date" class="form-input" min="<?= $today ?>" required>
                </div>
                <div class="form-group">
                    <label for="reason" class="form-label required">Reason for Reschedule</label>
                    <textarea id="reason" name="reason" class="form-textarea" placeholder="Explain why this appointment is being rescheduled..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('rescheduleModal')">Cancel</button>
                <button type="submit" class="btn btn-primary">Reschedule</button>
            </div>
        </form>
    </div>
</div>

<div id="cancelModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cancel Appointment</h3>
            <button type="button" class="modal-close" onclick="closeModal('cancelModal')">&times;</button>
        </div>
        <form action="../../actions/admin/cancel-appointment.php" method="POST">
            <input type="hidden" name="id" value="<?= $id ?>">
            <div class="modal-body">
                <div class="form-group">
                    <label for="reason" class="form-label required">Cancellation Reason</label>
                    <textarea id="reason" name="reason" class="form-textarea" placeholder="Explain why this appointment is being cancelled..." required></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelModal')">Cancel</button>
                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>
<?php endif; ?>

<div class="mt-6">
    <a href="dashboard.php" class="btn btn-secondary">← Back to Dashboard</a>
</div>

<script>
function openModal(modalId) {
    document.getElementById(modalId).classList.add('active');
}

function closeModal(modalId) {
    document.getElementById(modalId).classList.remove('active');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.querySelectorAll('.modal-overlay.active').forEach(modal => {
            modal.classList.remove('active');
        });
    }
});

document.querySelectorAll('.modal-overlay').forEach(modal => {
    modal.addEventListener('click', function(e) {
        if (e.target === modal) {
            modal.classList.remove('active');
        }
    });
});
</script>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
