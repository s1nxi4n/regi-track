document.addEventListener('DOMContentLoaded', function() {
    
    const modals = document.querySelectorAll('.modal');
    const closeButtons = document.querySelectorAll('.close-modal, .modal-cancel');
    
    closeButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            const modal = this.closest('.modal');
            if (modal) {
                modal.classList.remove('active');
            }
        });
    });
    
    window.openModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.add('active');
        }
    };
    
    window.closeModal = function(modalId) {
        const modal = document.getElementById(modalId);
        if (modal) {
            modal.classList.remove('active');
        }
    };
    
    const appointmentTypeSelect = document.getElementById('appointment_type');
    const dynamicFields = document.getElementById('dynamic_fields');
    
    if (appointmentTypeSelect && dynamicFields) {
        const fieldsConfig = {
            tor: `
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="text" id="contact_no" name="details[contact_no]" required>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <input type="text" id="purpose" name="details[purpose]" required>
                </div>
                <div class="form-group">
                    <label for="copy_quantity">Number of Copies</label>
                    <input type="number" id="copy_quantity" name="details[copy_quantity]" min="1" value="1" required>
                </div>
                <div class="form-group">
                    <label for="message">Message (Optional)</label>
                    <textarea id="message" name="details[message]"></textarea>
                </div>
            `,
            diploma: `
                <div class="form-group">
                    <label for="year_graduated">Year Graduated</label>
                    <input type="number" id="year_graduated" name="details[year_graduated]" min="2000" max="2030" required>
                </div>
                <div class="form-group">
                    <label for="message">Message (Optional)</label>
                    <textarea id="message" name="details[message]"></textarea>
                </div>
            `,
            request_rf: `
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="text" id="contact_no" name="details[contact_no]" required>
                </div>
                <div class="form-group">
                    <label for="semester">Semester</label>
                    <select id="semester" name="details[semester]" required>
                        <option value="1st Semester">1st Semester</option>
                        <option value="2nd Semester">2nd Semester</option>
                        <option value="Summer">Summer</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="school_year">School Year</label>
                    <input type="text" id="school_year" name="details[school_year]" placeholder="e.g., 2024-2025" required>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <textarea id="purpose" name="details[purpose]" required></textarea>
                </div>
            `,
            certificate: `
                <div class="form-group">
                    <label for="contact_no">Contact Number</label>
                    <input type="text" id="contact_no" name="details[contact_no]" required>
                </div>
                <div class="form-group">
                    <label for="course">Course</label>
                    <input type="text" id="course" name="details[course]" required>
                </div>
                <div class="form-group">
                    <label for="certification_type">Certification Type</label>
                    <select id="certification_type" name="details[certification_type]" required>
                        <option value="Enrollment">Enrollment</option>
                        <option value="Graduation">Graduation</option>
                        <option value="Course Completion">Course Completion</option>
                        <option value="Other">Other</option>
                    </select>
                </div>
                <div class="form-group">
                    <label for="purpose">Purpose</label>
                    <input type="text" id="purpose" name="details[purpose]" required>
                </div>
                <div class="form-group">
                    <label for="copy_quantity">Number of Copies</label>
                    <input type="number" id="copy_quantity" name="details[copy_quantity]" min="1" value="1" required>
                </div>
            `
        };
        
        appointmentTypeSelect.addEventListener('change', function() {
            const selectedType = this.value;
            if (fieldsConfig[selectedType]) {
                dynamicFields.innerHTML = fieldsConfig[selectedType];
            } else {
                dynamicFields.innerHTML = '';
            }
        });
    }
    
    const forms = document.querySelectorAll('form');
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            const submitBtn = form.querySelector('button[type="submit"]');
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Processing...';
            }
        });
    });
});
