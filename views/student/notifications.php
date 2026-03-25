<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Notifications - RegiTrack';
$studentId = $_SESSION['student_id'];
$notifications = getNotifications($studentId);

$notificationsArray = [];
if ($notifications) {
    foreach ($notifications as $id => $notif) {
        $notif['id'] = $id;
        $notificationsArray[] = $notif;
    }
}

usort($notificationsArray, function($a, $b) {
    return strcmp($b['created_at'] ?? '', $a['created_at'] ?? '');
});

if (!empty($notificationsArray)) {
    markAllNotificationsRead($studentId);
}

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Notifications</h1>
    
    <?php if (!empty($notificationsArray)): ?>
    <form action="../../actions/student/clear-notifications.php" method="POST" class="mb-4">
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Clear all notifications?')">Clear All</button>
    </form>
    <?php endif; ?>
    
    <?php if (empty($notificationsArray)): ?>
        <p>No notifications.</p>
    <?php else: ?>
        <div class="notifications-list">
            <?php foreach ($notificationsArray as $notif): ?>
            <div class="card notification-card <?= empty($notif['is_read']) ? 'unread' : '' ?>">
                <div class="notification-icon">
                    <?php
                    $icon = '📋';
                    switch($notif['type']) {
                        case 'accepted': $icon = '✅'; break;
                        case 'rejected': $icon = '❌'; break;
                        case 'reschedule_accepted': $icon = '📅'; break;
                        case 'reschedule_rejected': $icon = '❌'; break;
                        case 'admin_rescheduled': $icon = '📅'; break;
                        case 'cancelled': $icon = '🚫'; break;
                        case 'settled': $icon = '🎉'; break;
                        case 'no_show': $icon = '⚠️'; break;
                    }
                    echo $icon;
                    ?>
                </div>
                <div class="notification-content">
                    <p><?= htmlspecialchars($notif['message']) ?></p>
                    <small><?= htmlspecialchars($notif['created_at']) ?></small>
                </div>
                <div class="notification-actions">
                    <a href="view-appointment.php?id=<?= htmlspecialchars($notif['appointment_id']) ?>" class="btn btn-secondary">View</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
    
    <a href="dashboard.php" class="btn btn-secondary mt-4">Back to Dashboard</a>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
