<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../includes/icon.php';

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

$todayCount = count($todayScheduled);
$pendingCount = count($pendingAppointments);
$futureCount = count($futureScheduled);
$rescheduleCount = count($rescheduleRequests);

$typeIcons = [
    'tor' => 'file-text',
    'diploma' => 'graduation',
    'request_rf' => 'clipboard',
    'certificate' => 'check'
];

$activeTab = $_GET['tab'] ?? 'today';
?>

<?php include_once __DIR__ . '/../../includes/layout-admin.php'; ?>

<div class="stats-grid">
    <a href="?tab=today" class="stat-card <?= $activeTab === 'today' ? 'active' : '' ?>" style="text-decoration:none;color:inherit;">
        <div class="stat-icon primary"><?= icon('calendar') ?></div>
        <div class="stat-content">
            <div class="stat-label">Today's Appointments</div>
            <div class="stat-value"><?= $todayCount ?></div>
        </div>
    </a>
    <a href="?tab=pending" class="stat-card <?= $activeTab === 'pending' ? 'active' : '' ?>" style="text-decoration:none;color:inherit;">
        <div class="stat-icon warning"><?= icon('clock') ?></div>
        <div class="stat-content">
            <div class="stat-label">Pending Requests</div>
            <div class="stat-value"><?= $pendingCount ?></div>
        </div>
    </a>
    <a href="?tab=future" class="stat-card <?= $activeTab === 'future' ? 'active' : '' ?>" style="text-decoration:none;color:inherit;">
        <div class="stat-icon success"><?= icon('calendar-check') ?></div>
        <div class="stat-content">
            <div class="stat-label">Future Scheduled</div>
            <div class="stat-value"><?= $futureCount ?></div>
        </div>
    </a>
    <a href="?tab=reschedule" class="stat-card <?= $activeTab === 'reschedule' ? 'active' : '' ?>" style="text-decoration:none;color:inherit;">
        <div class="stat-icon danger"><?= icon('refresh') ?></div>
        <div class="stat-content">
            <div class="stat-label">Reschedule Requests</div>
            <div class="stat-value"><?= $rescheduleCount ?></div>
        </div>
    </a>
</div>

<style>
.stat-card.active {
    border-color: var(--accent);
    background: var(--accent-subtle);
}
</style>

<?php if ($activeTab === 'today'): ?>
    <?php if (!empty($todayScheduled)): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Today's Schedule</h3>
                <p class="card-subtitle"><?= count($todayScheduled) ?> appointment(s) for today - Click to manage</p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($todayScheduled as $apt): ?>
                    <tr class="clickable-row" onclick="window.location='manage-appointment.php?id=<?= $apt['id'] ?>'">
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
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">📅</div>
            <h3 class="empty-title">No Appointments Today</h3>
            <p class="empty-text">There are no appointments scheduled for today.</p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($activeTab === 'future'): ?>
    <?php if (!empty($futureScheduled)): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Upcoming Appointments</h3>
                <p class="card-subtitle"><?= count($futureScheduled) ?> scheduled for future dates - Click to manage</p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($futureScheduled as $apt): ?>
                    <tr class="clickable-row" onclick="window.location='manage-appointment.php?id=<?= $apt['id'] ?>'">
                        <td><?= htmlspecialchars($apt['student_id']) ?></td>
                        <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                        <td><?= htmlspecialchars($apt['date']) ?></td>
                        <td><span class="status-badge status-scheduled"><?= htmlspecialchars($apt['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">📆</div>
            <h3 class="empty-title">No Future Appointments</h3>
            <p class="empty-text">There are no upcoming appointments scheduled.</p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($activeTab === 'pending'): ?>
    <?php if (!empty($pendingAppointments)): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Pending Requests</h3>
                <p class="card-subtitle"><?= count($pendingAppointments) ?> appointment(s) awaiting review - Click to manage</p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($pendingAppointments as $apt): ?>
                    <tr class="clickable-row" onclick="window.location='manage-appointment.php?id=<?= $apt['id'] ?>'">
                        <td><?= htmlspecialchars($apt['student_id']) ?></td>
                        <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                        <td><?= htmlspecialchars($apt['date']) ?></td>
                        <td><span class="status-badge status-pending"><?= htmlspecialchars($apt['status']) ?></span></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">⏳</div>
            <h3 class="empty-title">No Pending Requests</h3>
            <p class="empty-text">All appointments have been reviewed.</p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php if ($activeTab === 'reschedule'): ?>
    <?php if (!empty($rescheduleRequests)): ?>
    <div class="card">
        <div class="card-header">
            <div>
                <h3 class="card-title">Reschedule Requests</h3>
                <p class="card-subtitle"><?= count($rescheduleRequests) ?> appointment(s) requesting date change - Click to manage</p>
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
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($rescheduleRequests as $apt): ?>
                    <tr class="clickable-row" onclick="window.location='manage-appointment.php?id=<?= $apt['id'] ?>'">
                        <td><?= htmlspecialchars($apt['student_id']) ?></td>
                        <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                        <td><?= htmlspecialchars($apt['date']) ?></td>
                        <td><?= htmlspecialchars($apt['rescheduled_date']) ?></td>
                        <td><?= htmlspecialchars($apt['reschedule_reason'] ?? 'N/A') ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
    <?php else: ?>
    <div class="card">
        <div class="empty-state">
            <div class="empty-icon">🔄</div>
            <h3 class="empty-title">No Reschedule Requests</h3>
            <p class="empty-text">There are no pending reschedule requests.</p>
        </div>
    </div>
    <?php endif; ?>
<?php endif; ?>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
