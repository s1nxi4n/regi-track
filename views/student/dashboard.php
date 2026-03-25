<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Dashboard - RegiTrack';
$studentId = $_SESSION['student_id'];
$appointments = getAppointmentsByStudent($studentId);

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
    <h1>Welcome, <?= htmlspecialchars($studentId) ?></h1>
    
    <div class="actions-bar">
        <a href="create-appointment.php" class="btn btn-primary">+ New Appointment</a>
        <a href="history.php" class="btn btn-secondary">View History</a>
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
                            <form action="../../actions/student/cancel-appointment.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Cancel this appointment?')">Cancel</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
