<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Dashboard - RegiTrack';
$studentId = $_SESSION['student_id'];
$appointments = getAppointmentsByStudent($studentId);
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

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Welcome, <?= htmlspecialchars($_SESSION['full_name'] ?? $studentId) ?></h1>
    
    <?php if ($cancelError): ?>
        <div class="error"><?= htmlspecialchars($cancelError) ?></div>
    <?php endif; ?>
    
    <div class="actions-bar">
        <a href="create-appointment.php" class="btn btn-primary">+ New Appointment</a>
    </div>
    
    <h2>Active Appointments</h2>
    
    <?php if (empty($activeAppointments)): ?>
        <p>No active appointments. Create one to get started!</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($activeAppointments as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-<?= str_replace(' ', '-', $apt['status']) ?>"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <div class="actions">
                            <?php if ($apt['status'] === STATUS_SCHEDULED): ?>
                                <a href="reschedule-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary">Reschedule</a>
                            <?php endif; ?>
                            <?php if ($apt['status'] !== STATUS_SCHEDULED): ?>
                                <a href="edit-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary">Edit</a>
                            <?php endif; ?>
                            <a href="view-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary">View</a>
                            <button type="button" class="btn btn-danger" onclick="openCancelModal('<?= $apt['id'] ?>', '<?= $apt['status'] ?>')">Cancel</button>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<div id="cancelModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('cancelModal')">&times;</span>
        <h2>Cancel Appointment</h2>
        <form action="../../actions/student/cancel-appointment.php" method="POST">
            <input type="hidden" name="id" id="cancelAppointmentId">
            <div id="cancelReasonGroup" class="form-group" style="display:none;">
                <label for="reason">Reason for Cancellation</label>
                <textarea id="reason" name="reason"></textarea>
            </div>
            <button type="submit" class="btn btn-danger">Confirm Cancellation</button>
            <button type="button" class="btn btn-secondary modal-cancel" onclick="closeModal('cancelModal')">Cancel</button>
        </form>
    </div>
</div>

<script>
function openCancelModal(id, status) {
    document.getElementById('cancelAppointmentId').value = id;
    var reasonGroup = document.getElementById('cancelReasonGroup');
    var reasonInput = document.getElementById('reason');
    if (status === 'Scheduled') {
        reasonGroup.style.display = 'block';
        reasonInput.required = true;
    } else {
        reasonGroup.style.display = 'none';
        reasonInput.required = false;
        reasonInput.value = '';
    }
    document.getElementById('cancelModal').classList.add('active');
}
</script>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
