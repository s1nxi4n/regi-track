<?php
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
requireOnceRole(ROLE_STUDENT);
require_once __DIR__ . '/../../includes/firebase-helper.php';
require_once __DIR__ . '/../../includes/icon.php';

$pageTitle = 'New Appointment';
$currentPage = 'create';
$studentId = $_SESSION['student_id'];
$unreadCount = getUnreadNotificationCount($studentId);

$error = $_SESSION['create_error'] ?? '';
unset($_SESSION['create_error']);

$today = date('Y-m-d');
?>

<?php include_once __DIR__ . '/../../includes/layout-student.php'; ?>

<div class="card">
    <div class="card-header">
        <div>
            <h3 class="card-title">Create New Appointment</h3>
            <p class="card-subtitle">Fill in the details below to request a document</p>
        </div>
    </div>
    
    <?php if ($error): ?>
        <div class="alert alert-danger mb-6">
            <?= icon('alert') ?>
            <div class="alert-content">
                <div class="alert-message"><?= htmlspecialchars($error) ?></div>
            </div>
        </div>
    <?php endif; ?>
    
    <form action="../../actions/student/create-appointment.php" method="POST" id="appointmentForm">
        <div class="form-group">
            <label for="type" class="form-label required">Document Type</label>
            <select id="type" name="type" class="form-select" required>
                <option value="">Select document type...</option>
                <option value="tor">Transcript of Records (TOR)</option>
                <option value="diploma">Diploma</option>
                <option value="request_rf">Request RF</option>
                <option value="certificate">Certificate</option>
            </select>
        </div>
        
        <div class="form-group">
            <label for="date" class="form-label required">Preferred Date</label>
            <input 
                type="date" 
                id="date" 
                name="date" 
                class="form-input" 
                min="<?= $today ?>"
                required
            >
            <p class="form-hint">Select your preferred pickup date</p>
        </div>
        
        <div id="dynamic_fields">
            <!-- Dynamic fields based on document type -->
        </div>
        
        <div class="flex gap-4 mt-6">
            <a href="dashboard.php" class="btn btn-secondary">Cancel</a>
            <button type="submit" class="btn btn-primary">Submit Appointment</button>
        </div>
    </form>
</div>

<script>
const fieldsConfig = {
    tor: `
        <div class="form-group">
            <label for="contact_no" class="form-label required">Contact Number</label>
            <input type="tel" id="contact_no" name="details[contact_no]" class="form-input" placeholder="09123456789" required>
        </div>
        <div class="form-group">
            <label for="purpose" class="form-label required">Purpose</label>
            <input type="text" id="purpose" name="details[purpose]" class="form-input" placeholder="e.g., Job application, Further studies" required>
        </div>
        <div class="form-group">
            <label for="copy_quantity" class="form-label required">Number of Copies</label>
            <input type="number" id="copy_quantity" name="details[copy_quantity]" class="form-input" min="1" value="1" required>
        </div>
        <div class="form-group">
            <label for="message" class="form-label">Additional Message (Optional)</label>
            <textarea id="message" name="details[message]" class="form-textarea" placeholder="Any special instructions..."></textarea>
        </div>
    `,
    diploma: `
        <div class="form-group">
            <label for="year_graduated" class="form-label required">Year Graduated</label>
            <input type="number" id="year_graduated" name="details[year_graduated]" class="form-input" min="2000" max="2030" placeholder="e.g., 2024" required>
        </div>
        <div class="form-group">
            <label for="message" class="form-label">Additional Message (Optional)</label>
            <textarea id="message" name="details[message]" class="form-textarea" placeholder="Any special instructions..."></textarea>
        </div>
    `,
    request_rf: `
        <div class="form-group">
            <label for="contact_no" class="form-label required">Contact Number</label>
            <input type="tel" id="contact_no" name="details[contact_no]" class="form-input" placeholder="09123456789" required>
        </div>
        <div class="form-group">
            <label for="semester" class="form-label required">Semester</label>
            <select id="semester" name="details[semester]" class="form-select" required>
                <option value="">Select semester...</option>
                <option value="1st Semester">1st Semester</option>
                <option value="2nd Semester">2nd Semester</option>
                <option value="Summer">Summer</option>
            </select>
        </div>
        <div class="form-group">
            <label for="school_year" class="form-label required">School Year</label>
            <input type="text" id="school_year" name="details[school_year]" class="form-input" placeholder="e.g., 2024-2025" required>
        </div>
        <div class="form-group">
            <label for="purpose" class="form-label required">Purpose</label>
            <textarea id="purpose" name="details[purpose]" class="form-textarea" placeholder="Explain the purpose..." required></textarea>
        </div>
    `,
    certificate: `
        <div class="form-group">
            <label for="contact_no" class="form-label required">Contact Number</label>
            <input type="tel" id="contact_no" name="details[contact_no]" class="form-input" placeholder="09123456789" required>
        </div>
        <div class="form-group">
            <label for="course" class="form-label required">Course</label>
            <input type="text" id="course" name="details[course]" class="form-input" placeholder="e.g., BS Computer Science" required>
        </div>
        <div class="form-group">
            <label for="certification_type" class="form-label required">Certification Type</label>
            <select id="certification_type" name="details[certification_type]" class="form-select" required>
                <option value="">Select type...</option>
                <option value="Enrollment">Enrollment</option>
                <option value="Graduation">Graduation</option>
                <option value="Course Completion">Course Completion</option>
                <option value="Other">Other</option>
            </select>
        </div>
        <div class="form-group">
            <label for="purpose" class="form-label required">Purpose</label>
            <input type="text" id="purpose" name="details[purpose]" class="form-input" placeholder="e.g., Job application" required>
        </div>
        <div class="form-group">
            <label for="copy_quantity" class="form-label required">Number of Copies</label>
            <input type="number" id="copy_quantity" name="details[copy_quantity]" class="form-input" min="1" value="1" required>
        </div>
    `
};

const typeSelect = document.getElementById('type');
const dynamicFields = document.getElementById('dynamic_fields');

if (typeSelect && dynamicFields) {
    typeSelect.addEventListener('change', function() {
        const selectedType = this.value;
        if (fieldsConfig[selectedType]) {
            dynamicFields.innerHTML = fieldsConfig[selectedType];
        } else {
            dynamicFields.innerHTML = '';
        }
    });
}
</script>

<?php include_once __DIR__ . '/../../includes/layout-end.php'; ?>
