<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Notifications';
$currentPage = 'notifications';
$studentId = $_SESSION['student_id'];
$unreadCount = getUnreadNotificationCount($studentId);

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
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<div class="flex justify-between items-center mb-6">
    <div>
        <h2>Notifications</h2>
        <p class="text-muted mb-0">Stay updated on your appointments</p>
    </div>
    <?php if (!empty($notificationsArray)): ?>
    <form action="../../actions/student/clear-notifications.php" method="POST">
        <button type="submit" class="btn btn-danger btn-sm" onclick="return confirm('Clear all notifications?')">
            🗑️ Clear All
        </button>
    </form>
    <?php endif; ?>
</div>

<?php if (empty($notificationsArray)): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">🔔</div>
        <h3 class="empty-title">No Notifications</h3>
        <p class="empty-text">You're all caught up! Check back later for updates.</p>
        <a href="dashboard.php" class="btn btn-primary">Go to Dashboard</a>
    </div>
</div>
<?php else: ?>
<div class="notification-list stagger">
    <?php foreach ($notificationsArray as $notif): ?>
    <div class="notification-item <?= empty($notif['is_read']) ? 'unread' : '' ?>">
        <?php
        $icon = '📋';
        $iconClass = '';
        switch($notif['type']) {
            case 'accepted': 
                $icon = '✅'; 
                $iconClass = 'accepted';
                break;
            case 'rejected': 
                $icon = '❌'; 
                $iconClass = 'rejected';
                break;
            case 'reschedule_accepted': 
                $icon = '📅'; 
                $iconClass = 'rescheduled';
                break;
            case 'reschedule_rejected': 
                $icon = '❌'; 
                $iconClass = 'rejected';
                break;
            case 'admin_rescheduled': 
                $icon = '📅'; 
                $iconClass = 'rescheduled';
                break;
            case 'cancelled': 
                $icon = '🚫'; 
                $iconClass = 'rejected';
                break;
            case 'settled': 
                $icon = '🎉'; 
                $iconClass = 'settled';
                break;
            case 'no_show': 
                $icon = '⚠️'; 
                $iconClass = 'rejected';
                break;
        }
        ?>
        <div class="notification-icon <?= $iconClass ?>"><?= $icon ?></div>
        <div class="notification-content">
            <p class="notification-message mb-2"><?= htmlspecialchars($notif['message']) ?></p>
            <span class="notification-time"><?= htmlspecialchars($notif['created_at']) ?></span>
        </div>
        <a href="view-appointment.php?id=<?= htmlspecialchars($notif['appointment_id']) ?>" class="btn btn-secondary btn-sm">
            View
        </a>
    </div>
    <?php endforeach; ?>
</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
