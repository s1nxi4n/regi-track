<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'Create Appointment - RegiTrack';

$error = $_SESSION['create_error'] ?? '';
unset($_SESSION['create_error']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Create New Appointment</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form action="../../actions/student/create-appointment.php" method="POST">
        <div class="form-group">
            <label for="appointment_type">Appointment Type</label>
            <select id="appointment_type" name="type" required>
                <option value="">Select Type</option>
                <?php foreach ($APPOINTMENT_TYPES as $key => $type): ?>
                    <option value="<?= $key ?>"><?= htmlspecialchars($type['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Preferred Date</label>
            <input type="date" id="date" name="date" required min="<?= date('Y-m-d') ?>">
        </div>
        
        <div id="dynamic_fields"></div>
        
        <button type="submit">Submit Appointment</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
