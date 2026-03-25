<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_ADMIN);
require_once __DIR__ . '/../../includes/firebase-helper.php';

$pageTitle = 'Add Student';
$currentPage = 'add-student';

$error = $_SESSION['add_student_error'] ?? '';
$success = $_SESSION['add_student_success'] ?? '';
unset($_SESSION['add_student_error'], $_SESSION['add_student_success']);
?>

<?php include_once __DIR__ . '/../../includes/layout-admin.php'; ?>

<?php if ($error): ?>
    <div class="alert alert-danger">
        <span class="alert-icon">⚠️</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($error) ?></div>
        </div>
    </div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success">
        <span class="alert-icon">✅</span>
        <div class="alert-content">
            <div class="alert-message"><?= htmlspecialchars($success) ?></div>
        </div>
    </div>
<?php endif; ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Add New Student</h3>
            <p class="card-subtitle">Create a new student account for the system</p>
        </div>
    </div>
    
    <form action="../../actions/admin/add-student.php" method="POST">
        <div class="form-group">
            <label for="student_id" class="form-label required">Student ID</label>
            <input 
                type="text" 
                id="student_id" 
                name="student_id" 
                class="form-input font-mono" 
                pattern="\d{2}-\d{4}-\d{6}" 
                placeholder="00-0000-000000" 
                required
            >
            <p class="form-hint">Format: xx-xxxx-xxxxxx (numbers only)</p>
        </div>
        
        <div class="form-group">
            <label for="full_name" class="form-label required">Full Name</label>
            <input 
                type="text" 
                id="full_name" 
                name="full_name" 
                class="form-input" 
                placeholder="Juan dela Cruz" 
                required
            >
        </div>
        
        <div class="form-group">
            <label for="email" class="form-label required">Email Address</label>
            <input 
                type="email" 
                id="email" 
                name="email" 
                class="form-input" 
                pattern=".+@phinmaed\.com$" 
                placeholder="juan.cruz.ui@phinmaed.com" 
                required
            >
            <p class="form-hint">Must be a PHINMA Education email (*.ui@phinmaed.com)</p>
        </div>
        
        <div class="flex gap-4 mt-6">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Add Student</button>
        </div>
    </form>
</div>

<div class="card mt-6">
    <div class="card-header">
        <h3 class="card-title">Quick Reference</h3>
    </div>
    <div class="table-container">
        <table>
            <tr>
                <th>Default Password</th>
                <td><code class="font-mono">1</code> (student must change on first login)</td>
            </tr>
            <tr>
                <th>Login Format (Student ID)</th>
                <td><code class="font-mono">23-1234-567890</code></td>
            </tr>
            <tr>
                <th>Login Format (Email)</th>
                <td><code class="font-mono">juan.cruz.ui@phinmaed.com</code></td>
            </tr>
        </table>
    </div>
</div>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
