<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../config/constants.php';

$pageTitle = 'Add Student - RegiTrack';

$error = $_SESSION['add_student_error'] ?? '';
$success = $_SESSION['add_student_success'] ?? '';
unset($_SESSION['add_student_error'], $_SESSION['add_student_success']);

include_once __DIR__ . '/../../includes/header.php';
?>

<div class="container">
    <h1>Add Student</h1>
    
    <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    
    <?php if ($success): ?>
        <div class="success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>
    
    <form action="../../actions/admin/add-student.php" method="POST">
        <div class="form-group">
            <label for="student_id">Student ID (Format: xx-xxxx-xxxxxx)</label>
            <input type="text" id="student_id" name="student_id" 
                   pattern="\d{2}-\d{4}-\d{6}" 
                   placeholder="00-0000-000000" required>
            <small>Format: xx-xxxx-xxxxxx (numbers only)</small>
        </div>
        
        <button type="submit">Add Student</button>
        <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include_once __DIR__ . '/../../includes/footer.php'; ?>
