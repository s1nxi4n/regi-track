<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Admin Activity Log';

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

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Activity Log</h1>
    
    <?php if (!empty($logsArray)): ?>
    <form action="../../actions/admin/clear-logs.php" method="POST" style="margin-bottom: 1rem;">
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Clear all?')">Clear</button>
    </form>
    <?php endif; ?>
    
    <?php if (empty($logsArray)): ?>
        <p>No activity yet.</p>
    <?php else: ?>
        <div class="log-list">
            <?php foreach ($logsArray as $log): ?>
            <div class="log-item" onclick="showLogDetails('<?= $log['key'] ?>')">
                <span class="log-icon">
                    <?php
                    $action = strtolower($log['action'] ?? '');
                    if (strpos($action, 'accept') !== false) echo '✅';
                    elseif (strpos($action, 'reject') !== false) echo '❌';
                    elseif (strpos($action, 'reschedul') !== false) echo '📅';
                    elseif (strpos($action, 'cancel') !== false) echo '🚫';
                    elseif (strpos($action, 'settled') !== false) echo '🎉';
                    elseif (strpos($action, 'no-show') !== false) echo '⚠️';
                    elseif (strpos($action, 'add') !== false) echo '👤';
                    else echo '📋';
                    ?>
                </span>
                <span class="log-text">
                    <strong><?= htmlspecialchars($log['action'] ?? '') ?></strong> · <?= htmlspecialchars($log['details'] ?? '') ?>
                </span>
                <span class="log-time"><?= htmlspecialchars(date('M d, g:i A', strtotime($log['timestamp'] ?? ''))) ?></span>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary" style="margin-top: 1rem;">Back</a>
</div>

<div id="logDetailModal" class="modal">
    <div class="modal-content">
        <span class="close-modal" onclick="closeModal('logDetailModal')">&times;</span>
        <h2>Log Details</h2>
        <div id="logDetailContent"></div>
        <button type="button" class="btn btn-secondary" onclick="closeModal('logDetailModal')">Close</button>
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
    
    let html = '<div class="details-box">';
    html += '<p><strong>Action:</strong> ' + (log.action || 'N/A') + '</p>';
    html += '<p><strong>Student:</strong> ' + (log.student_name || 'N/A') + '</p>';
    html += '<p><strong>Request Type:</strong> ' + (log.appointment_type || 'N/A') + '</p>';
    html += '<p><strong>Details:</strong> ' + (log.details || 'N/A') + '</p>';
    html += '<p><strong>Admin:</strong> ' + (log.admin_id || 'N/A') + '</p>';
    html += '<p><strong>Timestamp:</strong> ' + (log.timestamp || 'N/A') + '</p>';
    html += '</div>';
    
    document.getElementById('logDetailContent').innerHTML = html;
    document.getElementById('logDetailModal').classList.add('active');
}
</script>

<style>
.log-list { display: flex; flex-direction: column; gap: 0.5rem; }
.log-item { display: flex; align-items: center; gap: 0.75rem; padding: 0.75rem; background: #fff; border-radius: 4px; border-left: 3px solid #667eea; cursor: pointer; transition: background 0.2s; }
.log-item:hover { background: #f5f5f5; }
.log-icon { font-size: 1.2rem; }
.log-text { flex: 1; font-size: 0.9rem; }
.log-text strong { color: #333; }
.log-time { font-size: 0.75rem; color: #888; white-space: nowrap; }
.btn-sm { padding: 0.4rem 0.8rem; font-size: 0.85rem; }
.details-box p { margin-bottom: 0.5rem; }
</style>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>