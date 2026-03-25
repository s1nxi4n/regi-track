<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'My History - RegiTrack';

$studentId = $_SESSION['student_id'];
$appointments = getAppointmentsByStudent($studentId);

$historyAppointments = [];
foreach ($appointments as $id => $apt) {
    if (in_array($apt['status'], [STATUS_REJECTED, STATUS_SETTLED, STATUS_NO_SHOW, STATUS_CANCELLED])) {
        $apt['id'] = $id;
        $historyAppointments[$id] = $apt;
    }
}

$success = $_SESSION['history_success'] ?? '';
unset($_SESSION['history_success']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>My History</h1>
    
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <div class="actions-bar" style="margin-bottom: 1rem;">
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </div>
    
    <?php if (!empty($historyAppointments)): ?>
    <form action="../../actions/student/clear-history.php" method="POST" style="display:inline; margin-bottom: 1rem;">
        <input type="hidden" name="clear_all" value="1">
        <button type="submit" class="btn btn-danger" onclick="return confirm('Clear ALL history? This cannot be undone.')">Clear All History</button>
    </form>
    <?php endif; ?>
    
    <?php if (empty($historyAppointments)): ?>
        <p>No history yet.</p>
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
                <?php foreach ($historyAppointments as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-<?= str_replace(' ', '-', $apt['status']) ?>"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <div class="actions">
                            <a href="view-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary">View</a>
                            <form action="../../actions/student/clear-history.php" method="POST" style="display:inline;">
                                <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                <button type="submit" class="btn btn-danger" onclick="return confirm('Remove from history?')">Clear</button>
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
