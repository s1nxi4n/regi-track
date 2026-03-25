<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Dashboard';
$currentPage = 'dashboard';

$appointments = getAppointments();
$today = date('Y-m-d');

$todayScheduled = [];
$futureScheduled = [];
$pendingAppointments = [];
$rescheduleRequests = [];

foreach ($appointments as $id => $apt) {
    $apt['id'] = $id;
    
    if ($apt['status'] === STATUS_PENDING && !empty($apt['rescheduled_date'])) {
        $rescheduleRequests[$id] = $apt;
    } elseif ($apt['status'] === STATUS_PENDING) {
        $pendingAppointments[$id] = $apt;
    } elseif ($apt['status'] === STATUS_SCHEDULED) {
        if ($apt['date'] === $today) {
            $todayScheduled[$id] = $apt;
        } elseif ($apt['date'] > $today) {
            $futureScheduled[$id] = $apt;
        }
    }
}

function sortByDate($a, $b) {
    return strcmp($a['date'], $b['date']);
}

usort($todayScheduled, 'sortByDate');
usort($futureScheduled, 'sortByDate');
usort($pendingAppointments, 'sortByDate');
usort($rescheduleRequests, 'sortByDate');

$typeIcons = [
    'tor' => '📄',
    'diploma' => '🎓',
    'request_rf' => '📋',
    'certificate' => '✅'
];
?>

<?php include_once __DIR__ . '/../../includes/layout-admin.php'; ?>

<div class="stats-grid stagger">
    <div class="stat-card">
        <div class="stat-icon primary">📅</div>
        <div class="stat-content">
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-value"><?= count($todayScheduled) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon warning">⏳</div>
        <div class="stat-content">
            <div class="stat-label">Pending Requests</div>
            <div class="stat-value"><?= count($pendingAppointments) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon success">📆</div>
        <div class="stat-content">
            <div class="stat-label">Future Scheduled</div>
            <div class="stat-value"><?= count($futureScheduled) ?></div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon danger">🔄</div>
        <div class="stat-content">
            <div class="stat-label">Reschedule Requests</div>
            <div class="stat-value"><?= count($rescheduleRequests) ?></div>
        </div>
    </div>
</div>

<?php if (!empty($todayScheduled)): ?>
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Today's Schedule</h3>
            <p class="card-subtitle"><?= count($todayScheduled) ?> appointment(s) for today</p>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($todayScheduled as $apt): ?>
                <tr>
                    <td>
                        <div class="flex items-center gap-3">
                            <div class="user-avatar" style="width:32px;height:32px;font-size:12px;">
                                <?= strtoupper(substr($apt['student_id'], -4)) ?>
                            </div>
                            <?= htmlspecialchars($apt['student_id']) ?>
                        </div>
                    </td>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-badge status-scheduled"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-primary btn-sm">Manage</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($futureScheduled)): ?>
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Upcoming Appointments</h3>
            <p class="card-subtitle"><?= count($futureScheduled) ?> scheduled for future dates</p>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach (array_slice($futureScheduled, 0, 5) as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($apt['student_id']) ?></td>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-badge status-scheduled"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-secondary btn-sm">Manage</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($pendingAppointments)): ?>
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Pending Requests</h3>
            <p class="card-subtitle"><?= count($pendingAppointments) ?> appointment(s) awaiting review</p>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($pendingAppointments as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($apt['student_id']) ?></td>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-badge status-pending"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-primary btn-sm">Review</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (!empty($rescheduleRequests)): ?>
<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Reschedule Requests</h3>
            <p class="card-subtitle"><?= count($rescheduleRequests) ?> appointment(s) requesting date change</p>
        </div>
    </div>
    <div class="table-container">
        <table>
            <thead>
                <tr>
                    <th>Student</th>
                    <th>Type</th>
                    <th>Current Date</th>
                    <th>Requested Date</th>
                    <th>Reason</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($rescheduleRequests as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($apt['student_id']) ?></td>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><?= htmlspecialchars($apt['rescheduled_date']) ?></td>
                    <td><?= htmlspecialchars($apt['reschedule_reason'] ?? 'N/A') ?></td>
                    <td>
                        <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-primary btn-sm">Review</a>
                    </td>
                </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>
<?php endif; ?>

<?php if (empty($todayScheduled) && empty($futureScheduled) && empty($pendingAppointments) && empty($rescheduleRequests)): ?>
<div class="card">
    <div class="empty-state">
        <div class="empty-icon">🎉</div>
        <h3 class="empty-title">All Caught Up!</h3>
        <p class="empty-text">No appointments to manage right now.</p>
    </div>
</div>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
