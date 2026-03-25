<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../includes/icon.php';

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';
$studentId = $_SESSION['student_id'];
$appointments = getAppointmentsByStudent($studentId);
$unreadCount = getUnreadNotificationCount($studentId);
$cancelError = $_SESSION['cancel_error'] ?? '';
unset($_SESSION['cancel_error']);

$activeAppointments = [];
$statusOrder = STATUS_ORDER;

foreach ($appointments as $id => $apt) {
    if (!in_array($apt['status'], [STATUS_REJECTED, STATUS_SETTLED, STATUS_NO_SHOW, STATUS_CANCELLED])) {
        $apt['id'] = $id;
        $activeAppointments[$id] = $apt;
    }
}

usort($activeAppointments, function($a, $b) use ($statusOrder) {
    $orderA = isset($statusOrder[$a['status']]) ? $statusOrder[$a['status']] : 99;
    $orderB = isset($statusOrder[$b['status']]) ? $statusOrder[$b['status']] : 99;
    if ($orderA !== $orderB) return $orderA - $orderB;
    return strcmp($a['date'], $b['date']);
});

$typeIcons = [
    'tor' => 'file-text',
    'diploma' => 'graduation',
    'request_rf' => 'clipboard',
    'certificate' => 'check'
];
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<?php if ($cancelError): ?>
    <div class="alert alert-danger">
        <?= icon('alert') ?>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($cancelError) ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2>Welcome back, <?= htmlspecialchars($_SESSION['full_name'] ?? $studentId) ?></h2>
        <p class="text-muted mb-0">Manage your appointments</p>
    </div>
    <a href="create-appointment.php" class="btn btn-primary">
        <?= icon('plus') ?> New Appointment
    </a>
</div>

<?php if (empty($activeAppointments)): ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon"><?= icon('clipboard', 64) ?></div>
            <h3 class="empty-title">No Active Appointments</h3>
            <p class="empty-text">Create your first appointment to get started.</p>
            <a href="create-appointment.php" class="btn btn-primary">Create Appointment</a>
        </div>
    </div>
<?php else: ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Active Appointments</h3>
                <p class="card-subtitle"><?= count($activeAppointments) ?> appointment(s)</p>
            </div>
        </div>
        
        <div class="appointment-list stagger">
            <?php foreach ($activeAppointments as $apt): ?>
                <div class="appointment-item">
                    <div class="appointment-type-icon">
                        <?= isset($typeIcons[$apt['type']]) ? icon($typeIcons[$apt['type']]) : icon('clipboard') ?>
                    </div>
                    <div class="appointment-info">
                        <div class="appointment-type">
                            <?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?>
                        </div>
                        <div class="appointment-meta">
                            <span><?= icon('calendar', 14) ?> <?= htmlspecialchars($apt['date']) ?></span>
                            <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $apt['status'])) ?>">
                                <?= htmlspecialchars($apt['status']) ?>
                            </span>
                        </div>
                    </div>
                    <div class="appointment-actions">
                        <?php if ($apt['status'] === STATUS_SCHEDULED): ?>
                            <a href="reschedule-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary btn-sm">Reschedule</a>
                        <?php endif; ?>
                        <?php if ($apt['status'] !== STATUS_SCHEDULED): ?>
                            <a href="edit-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary btn-sm">Edit</a>
                        <?php endif; ?>
                        <a href="view-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                        <button type="button" class="btn btn-danger btn-sm" onclick="openCancelModal('<?= $apt['id'] ?>', '<?= $apt['status'] ?>')">Cancel</button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
<?php endif; ?>

<div id="cancelModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Cancel Appointment</h3>
            <button type="button" class="modal-close" onclick="closeModal('cancelModal')">&times;</button>
        </div>
        <form action="../../actions/student/cancel-appointment.php" method="POST">
            <div class="modal-body">
                <input type="hidden" name="id" id="cancelAppointmentId">
                <div id="cancelReasonGroup" class="form-group hidden">
                    <label for="reason" class="form-label required">Reason for Cancellation</label>
                    <textarea id="reason" name="reason" class="form-textarea" placeholder="Please provide a reason..."></textarea>
                </div>
                <p class="text-muted">Are you sure you want to cancel this appointment?</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" onclick="closeModal('cancelModal')">Keep Appointment</button>
                <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
            </div>
        </form>
    </div>
</div>

<script>
function openCancelModal(id, status) {
    document.getElementById('cancelAppointmentId').value = id;
    var reasonGroup = document.getElementById('cancelReasonGroup');
    var reasonInput = document.getElementById('reason');
    if (status === 'Scheduled') {
        reasonGroup.classList.remove('hidden');
        reasonInput.required = true;
    } else {
        reasonGroup.classList.add('hidden');
        reasonInput.required = false;
        reasonInput.value = '';
    }
    document.getElementById('cancelModal').classList.add('active');
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
