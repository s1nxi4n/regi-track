<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Admin Logs - RegiTrack';

$logs = getAdminLogs();

$logsArray = [];
if ($logs) {
    foreach ($logs as $key => $log) {
        $log['key'] = $key;
        $logsArray[] = $log;
    }
}

usort($logsArray, function($a, $b) {
    return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
});

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Admin Action Logs</h1>
    
    <?php if (empty($logsArray)): ?>
        <p>No admin actions recorded yet.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Admin ID</th>
                    <th>Action</th>
                    <th>Appointment ID</th>
                    <th>Details</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($logsArray as $log): ?>
                <tr>
                    <td><?= htmlspecialchars($log['timestamp'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['admin_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['action'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['appointment_id'] ?? '') ?></td>
                    <td><?= htmlspecialchars($log['details'] ?? '') ?></td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary" style="margin-top: 1rem;">Back to Dashboard</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
