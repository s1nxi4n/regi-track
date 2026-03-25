<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Reschedule Appointment - RegiTrack';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: dashboard.php');
    exit;
}

if ($appointment['status'] !== STATUS_SCHEDULED) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['reschedule_error'] ?? '';
unset($_SESSION['reschedule_error']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Reschedule Appointment</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <div class="card">
        <p><strong>Current Date:</strong> <?= htmlspecialchars($appointment['date']) ?></p>
        <p><strong>Status:</strong> <?= htmlspecialchars($appointment['status']) ?></p>
    </div>
    
    <form action="../../actions/student/reschedule-appointment.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        
        <div class="form-group">
            <label for="new_date">New Preferred Date</label>
            <input type="date" id="new_date" name="new_date" required min="<?= date('Y-m-d') ?>">
        </div>
        
        <div class="form-group">
            <label for="reason">Reason for Reschedule</label>
            <textarea id="reason" name="reason" required></textarea>
        </div>
        
        <button type="submit">Request Reschedule</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
