<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Admin Dashboard - RegiTrack';

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

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Admin Dashboard</h1>
    
    <div class="grid">
        <div class="card">
            <h3>Today's Scheduled (<?= count($todayScheduled) ?>)</h3>
            <?php if (empty($todayScheduled)): ?>
                <p>No appointments scheduled for today.</p>
            <?php else: ?>
                <table>
                    <tr><th>Student</th><th>Type</th><th>Date</th><th>Action</th></tr>
                    <?php foreach ($todayScheduled as $apt): ?>
                    <tr>
                        <td><?= htmlspecialchars($apt['student_id']) ?></td>
                        <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                        <td><?= htmlspecialchars($apt['date']) ?></td>
                        <td><a href="manage-appointment.php?id=<?= $apt['id'] ?>">Manage</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
        
        <div class="card">
            <h3>Future Scheduled (<?= count($futureScheduled) ?>)</h3>
            <?php if (empty($futureScheduled)): ?>
                <p>No future scheduled appointments.</p>
            <?php else: ?>
                <table>
                    <tr><th>Student</th><th>Type</th><th>Date</th><th>Action</th></tr>
                    <?php foreach (array_slice($futureScheduled, 0, 5) as $apt): ?>
                    <tr>
                        <td><?= htmlspecialchars($apt['student_id']) ?></td>
                        <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                        <td><?= htmlspecialchars($apt['date']) ?></td>
                        <td><a href="manage-appointment.php?id=<?= $apt['id'] ?>">Manage</a></td>
                    </tr>
                    <?php endforeach; ?>
                </table>
            <?php endif; ?>
        </div>
    </div>
    
    <div class="card" style="margin-top: 1.5rem;">
        <h3>Pending Requests (<?= count($pendingAppointments) ?>)</h3>
        <?php if (empty($pendingAppointments)): ?>
            <p>No pending appointments.</p>
        <?php else: ?>
            <table>
                <tr><th>Student</th><th>Type</th><th>Date</th><th>Status</th><th>Actions</th></tr>
                <?php foreach ($pendingAppointments as $apt): ?>
                <tr>
                    <td><?= htmlspecialchars($apt['student_id']) ?></td>
                    <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                    <td><?= htmlspecialchars($apt['date']) ?></td>
                    <td><span class="status-<?= str_replace(' ', '-', $apt['status']) ?>"><?= htmlspecialchars($apt['status']) ?></span></td>
                    <td>
                        <div class="actions">
                            <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-primary">Review</a>
                        </div>
                    </td>
                </tr>
                <?php endforeach; ?>
            </table>
        <?php endif; ?>
    </div>
    
    <?php if (!empty($rescheduleRequests)): ?>
    <div class="card" style="margin-top: 1.5rem;">
        <h3>Reschedule Requests (<?= count($rescheduleRequests) ?>)</h3>
        <table>
            <tr><th>Student</th><th>Type</th><th>Current Date</th><th>Requested Date</th><th>Reason</th><th>Actions</th></tr>
            <?php foreach ($rescheduleRequests as $apt): ?>
            <tr>
                <td><?= htmlspecialchars($apt['student_id']) ?></td>
                <td><?= htmlspecialchars($APPOINTMENT_TYPES[$apt['type']]['label'] ?? $apt['type']) ?></td>
                <td><?= htmlspecialchars($apt['date']) ?></td>
                <td><?= htmlspecialchars($apt['rescheduled_date']) ?></td>
                <td><?= htmlspecialchars($apt['reschedule_reason'] ?? 'N/A') ?></td>
                <td>
                    <div class="actions">
                        <a href="manage-appointment.php?id=<?= $apt['id'] ?>" class="btn btn-primary">Review</a>
                    </div>
                </td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
    <?php endif; ?>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
