<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'Edit Appointment - RegiTrack';

$id = $_GET['id'] ?? '';
$appointment = getAppointment($id);

if (!$appointment || $appointment['student_id'] !== $_SESSION['student_id']) {
    header('Location: dashboard.php');
    exit;
}

if ($appointment['status'] === STATUS_SCHEDULED) {
    header('Location: dashboard.php');
    exit;
}

$error = $_SESSION['edit_error'] ?? '';
unset($_SESSION['edit_error']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Edit Appointment</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <form action="../../actions/student/edit-appointment.php" method="POST">
        <input type="hidden" name="id" value="<?= htmlspecialchars($id) ?>">
        
        <div class="form-group">
            <label for="appointment_type">Appointment Type</label>
            <select id="appointment_type" name="type" required>
                <?php foreach ($APPOINTMENT_TYPES as $key => $type): ?>
                    <option value="<?= $key ?>" <?= $key === $appointment['type'] ? 'selected' : '' ?>><?= htmlspecialchars($type['label']) ?></option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date">Preferred Date</label>
            <input type="date" id="date" name="date" value="<?= htmlspecialchars($appointment['date']) ?>" required min="<?= date('Y-m-d') ?>">
        </div>
        
        <div id="dynamic_fields">
            <?php
            $typeFields = $APPOINTMENT_TYPES[$appointment['type']]['fields'] ?? [];
            foreach ($typeFields as $field):
            ?>
            <div class="form-group">
                <label for="<?= $field ?>"><?= htmlspecialchars(ucwords(str_replace('_', ' ', $field))) ?></label>
                <input type="text" id="<?= $field ?>" name="details[<?= $field ?>]" 
                       value="<?= htmlspecialchars($appointment['details'][$field] ?? '') ?>" 
                       <?= in_array($field, ['copy_quantity', 'year_graduated']) ? 'type="number"' : 'type="text"' ?>>
            </div>
            <?php endforeach; ?>
        </div>
        
        <button type="submit">Update Appointment</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
