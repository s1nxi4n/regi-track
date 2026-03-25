<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Activity Logs';
$currentPage = 'history';

$logs = getAdminLogs();
$users = getUsers();
$appointments = getAppointments();

$logsArray = [];
if ($logs) {
    foreach ($logs as $key => $log) {
        $log['key'] = $key;
        
        $aptId = $log['appointment_id'] ?? '';
        $studentId = '';
        
        if ($aptId && isset($appointments[$aptId])) {
            $apt = $appointments[$aptId];
            $studentId = $apt['student_id'] ?? '';
            $log['appointment_type'] = $APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type'] ?? '';
        }
        
        if (empty($studentId) && isset($log['details'])) {
            if (preg_match('/^([0-9]{2}-\d{4}-\d{6})/', $log['details'], $matches)) {
                $studentId = $matches[1];
            }
        }
        
        if ($studentId && isset($users[$studentId])) {
            $log['student_name'] = $users[$studentId]['full_name'] ?? $studentId;
        }
        
        $logsArray[] = $log;
    }
}

usort($logsArray, function($a, $b) {
    return strcmp($b['timestamp'] ?? '', $a['timestamp'] ?? '');
});
?>

<?php include_once __DIR__ . '/../../includes/layout-admin.php'; ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2>Activity Logs</h2>
        <p class="text-muted mb-0">Track all administrative actions</p>
    </div>
    <?php if (!empty($logsArray)): ?>
    <form action="../../actions/admin/clear-logs.php" method="POST">
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Clear all logs?')">
            🗑️ Clear All
        </button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($logsArray)): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">📜</div>
        <h3 class="empty-title">No Activity</h3>
        <p class="empty-text">Admin actions will appear here once recorded.</p>
    </div>
</div>
<?php else: ?>
<div class="log-list stagger">
    <?php foreach ($logsArray as $log): ?>
    <div class="log-item" onclick="showLogDetails('<?= $log['key'] ?>')">
        <?php
        $action = strtolower($log['action'] ?? '');
        $iconClass = '';
        $icon = '📋';
        
        if (strpos($action, 'accept') !== false) { $icon = '✅'; $iconClass = 'accept'; }
        elseif (strpos($action, 'reject') !== false) { $icon = '❌'; $iconClass = 'reject'; }
        elseif (strpos($action, 'reschedul') !== false) { $icon = '📅'; $iconClass = 'reschedule'; }
        elseif (strpos($action, 'cancel') !== false) { $icon = '🚫'; $iconClass = 'cancel'; }
        elseif (strpos($action, 'settled') !== false) { $icon = '🎉'; $iconClass = 'settle'; }
        elseif (strpos($action, 'no-show') !== false) { $icon = '⚠️'; $iconClass = 'cancel'; }
        elseif (strpos($action, 'add') !== false) { $icon = '👤'; $iconClass = 'accept'; }
        ?>
        <div class="log-icon <?= $iconClass ?>"><?= $icon ?></div>
        <div class="log-content">
            <div class="log-action"><?= htmlspecialchars($log['action'] ?? '') ?></div>
            <div class="log-details"><?= htmlspecialchars($log['details'] ?? '') ?></div>
        </div>
        <div class="log-time"><?= htmlspecialchars(date('M d, g:i A', strtotime($log['timestamp'] ?? ''))) ?></div>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<div id="logDetailModal" class="modal-overlay">
    <div class="modal">
        <div class="modal-header">
            <h3 class="modal-title">Log Details</h3>
            <button type="button" class="modal-close" onclick="document.getElementById('logDetailModal').classList.remove('active')">&times;</button>
        </div>
        <div class="modal-body" id="logDetailContent"></div>
        <div class="modal-footer">
            <button type="button" class="btn btn-secondary" onclick="document.getElementById('logDetailModal').classList.remove('active')">Close</button>
        </div>
    </div>
</div>

<?php
$logData = [];
foreach ($logsArray as $log) {
    $logData[$log['key']] = [
        'action' => $log['action'] ?? '',
        'details' => $log['details'] ?? '',
        'admin_id' => $log['admin_id'] ?? '',
        'appointment_id' => $log['appointment_id'] ?? '',
        'timestamp' => $log['timestamp'] ?? '',
        'student_name' => $log['student_name'] ?? '',
        'appointment_type' => $log['appointment_type'] ?? ''
    ];
}
?>

<script>
const logData = <?= json_encode($logData) ?>;

function showLogDetails(key) {
    const log = logData[key];
    if (!log) return;
    
    let html = '<div class="table-container">';
    html += '<table>';
    html += '<tr><th>Action</th><td>' + (log.action || 'N/A') + '</td></tr>';
    html += '<tr><th>Student</th><td>' + (log.student_name || 'N/A') + '</td></tr>';
    html += '<tr><th>Request Type</th><td>' + (log.appointment_type || 'N/A') + '</td></tr>';
    html += '<tr><th>Details</th><td>' + (log.details || 'N/A') + '</td></tr>';
    html += '<tr><th>Admin</th><td>' + (log.admin_id || 'N/A') + '</td></tr>';
    html += '<tr><th>Timestamp</th><td>' + (log.timestamp || 'N/A') + '</td></tr>';
    html += '</table></div>';
    
    document.getElementById('logDetailContent').innerHTML = html;
    document.getElementById('logDetailModal').classList.add('active');
}

document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        document.getElementById('logDetailModal').classList.remove('active');
    }
});
</script>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
