(function() {
  'use strict';

  // ----- Modal System -----
  const Modal = {
    init: function() {
      document.querySelectorAll('.modal').forEach(modal => {
        modal.setAttribute('role', 'dialog');
        modal.setAttribute('aria-modal', 'true');
        modal.setAttribute('aria-hidden', 'true');
      });
      
      document.querySelectorAll('.close-modal, .modal-cancel').forEach(btn => {
        btn.addEventListener('click', this.closeClosestModal.bind(this));
      });
      
      document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && e.target.classList.contains('modal') && e.target.classList.contains('active')) {
          this.closeClosestModal.call(e.target, e);
        }
      }.bind(this));
      
      document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('click', function(e) {
          if (e.target === modal) {
            modal.classList.remove('active');
            modal.setAttribute('aria-hidden', 'true');
          }
        });
      });
    },
    
    open: function(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.add('active');
        modal.setAttribute('aria-hidden', 'false');
        const focusable = modal.querySelector('input, select, textarea, button');
        if (focusable) focusable.focus();
      }
    },
    
    close: function(modalId) {
      const modal = document.getElementById(modalId);
      if (modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
      }
    },
    
    closeClosestModal: function(e) {
      if (e) e.preventDefault();
      const modal = this.closest('.modal');
      if (modal) {
        modal.classList.remove('active');
        modal.setAttribute('aria-hidden', 'true');
      }
    }
  };

  // ----- Global Modal Functions (backward compatible) -----
  window.openModal = Modal.open.bind(Modal);
  window.closeModal = Modal.close.bind(Modal);

  // ----- Dynamic Form Fields -----
  const DynamicFields = {
    init: function() {
      const select = document.getElementById('appointment_type');
      const container = document.getElementById('dynamic_fields');
      
      if (!select || !container) return;
      
      const fieldsConfig = {
        tor: `
          <div class="form-group">
            <label for="contact_no">Contact Number</label>
            <input type="tel" id="contact_no" name="details[contact_no]" required>
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
            <input type="tel" id="contact_no" name="details[contact_no]" required>
          </div>
          <div class="form-group">
            <label for="semester">Semester</label>
            <select id="semester" name="details[semester]" required>
              <option value="">Select semester</option>
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
            <input type="tel" id="contact_no" name="details[contact_no]" required>
          </div>
          <div class="form-group">
            <label for="course">Course</label>
            <input type="text" id="course" name="details[course]" required>
          </div>
          <div class="form-group">
            <label for="certification_type">Certification Type</label>
            <select id="certification_type" name="details[certification_type]" required>
              <option value="">Select type</option>
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
      
      select.addEventListener('change', function() {
        const selectedType = this.value;
        if (fieldsConfig[selectedType]) {
          container.innerHTML = fieldsConfig[selectedType];
        } else {
          container.innerHTML = '';
        }
      });
    }
  };

  // ----- Form Submission Loading -----
  const FormLoading = {
    init: function() {
      document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', function(e) {
          const submitBtn = form.querySelector('button[type="submit"]');
          if (submitBtn && !submitBtn.disabled) {
            submitBtn.disabled = true;
            const originalText = submitBtn.textContent;
            submitBtn.textContent = 'Processing...';
            
            // Re-enable after 10 seconds as fallback
            setTimeout(() => {
              submitBtn.disabled = false;
              submitBtn.textContent = originalText;
            }, 10000);
          }
        });
      });
    }
  };

  // ----- Date Picker - Block Past Dates -----
  const DatePicker = {
    init: function() {
      const dateInputs = document.querySelectorAll('input[type="date"]');
      const today = new Date().toISOString().split('T')[0];
      
      dateInputs.forEach(input => {
        input.setAttribute('min', today);
      });
    }
  };

  // ----- Cancel Modal Helper -----
  window.openCancelModal = function(id, status) {
    document.getElementById('cancelAppointmentId').value = id;
    const reasonGroup = document.getElementById('cancelReasonGroup');
    const reasonInput = document.getElementById('reason');
    
    if (reasonGroup && reasonInput) {
      if (status === 'Scheduled') {
        reasonGroup.style.display = 'block';
        reasonInput.required = true;
      } else {
        reasonGroup.style.display = 'none';
        reasonInput.required = false;
        reasonInput.value = '';
      }
    }
    
    Modal.open('cancelModal');
  };

  // ----- Initialize All -----
  document.addEventListener('DOMContentLoaded', function() {
    Modal.init();
    DynamicFields.init();
    FormLoading.init();
    DatePicker.init();
  });

})();
