<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'History';
$currentPage = 'history';
$studentId = $_SESSION['student_id'];
$unreadCount = getUnreadNotificationCount($studentId);

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

$typeIcons = [
    'tor' => '📄',
    'diploma' => '🎓',
    'request_rf' => '📋',
    'certificate' => '✅'
];
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<?php if ($success): ?>
    <div class="alert alert-success mb-6">
        <span class="alert-icon">✅</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($success) ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2>Appointment History</h2>
        <p class="text-muted mb-0">View your past appointments</p>
    </div>
    <?php if (!empty($historyAppointments)): ?>
    <form action="../../actions/student/clear-history.php" method="POST">
        <input type="hidden" name="clear_all" value="1">
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Clear ALL history? This cannot be undone.')">
            🗑️ Clear All
        </button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($historyAppointments)): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">📜</div>
        <h3 class="empty-title">No History</h3>
        <p class="empty-text">Your completed appointments will appear here.</p>
        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>
<?php else: ?>
<div class="card">
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th style="width: 150px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($historyAppointments as $apt): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="appointment-type-icon" style="width:32px;height:32px;font-size:14px;">
                                <?= $typeIcons[$apt['type']] ?? '📋' ?>
                            </div>
                            <?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td>
                        <span class="status-badge status-<?= strtolower(str_replace(' ', '-', $apt['status'])) ?>">
                            <?= htmlspecialchars($apt['status']) ?>
                        </span>
                    </td>
                    <td>
                        <div class="flex gap-2">
                            <a href="view-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary btn-sm">View</a>
                            <form action="../../actions/student/clear-history.php" method="POST">
                                <input type="hidden" name="id" value="<?= $apt['id'] ?>">
                                <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Remove from history?')">×</button>
                            </form>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
