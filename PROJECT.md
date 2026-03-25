# ROLE

Senior Full-Stack DevOps Engineer (PHP + Firebase Specialist)

# CONTEXT

I am a 3rd-year IT student building a capstone project called "RegiTrack" (Appointment Tracking System).
The system must be simple, modular, and easy to understand for project defense.

# OBJECTIVE

Generate a clean, modular PHP-based appointment system using Firebase Realtime Database.
Focus on readability, maintainability, and beginner-friendly logic (no over-engineering).

# STACK

* Frontend: HTML5, CSS3
* Backend: PHP (procedural or simple OOP)
* Database: Firebase Realtime DB (REST API)
* Auth: PHP Sessions + Firebase UID

# DATA MODELS

Users:
{
student_id,
password (hashed),
is_first_login: bool,
role: "student" | "admin"
}

Appointments:
{
id,
student_id,
type,
date,
status,
rejection_reason,
rescheduled_date,
details: { }
}

# APPOINTMENT TYPES & INPUT STRUCTURE

IMPORTANT: Use a dynamic "details" object to store type-specific fields.

1. Transcript of Records (TOR)
   details: {
   contact_no,
   purpose,
   copy_quantity,
   message
   }

2. Diploma
   details: {
   year_graduated,
   message
   }

3. Request RF
   details: {
   contact_no,
   semester,
   school_year,
   message
   }

4. Certificate
   details: {
   contact_no,
   course,
   certification_type,
   purpose,
   copy_quantity
   }

# STATUS LOGIC (STRICT ORDER)

1. Scheduled (sorted by date ASC)
2. In Process
3. Pending

This sorting must be explicitly implemented in the PHP fetch loop.

# STUDENT FEATURES

* Create, edit, cancel appointments
* Reschedule ONLY if status = "Scheduled"
* Edit ONLY if status ≠ "Scheduled"
* View appointments sorted by STATUS LOGIC
* History section with:

  * Manual clear
  * Clear all
* First login:

  * Default password = student_id
  * Force password change if is_first_login == true
* Ability to change password

# ADMIN FEATURES

* Add students:

  * Format: xx-xxxx-xxxxxx (numbers only)
  * Default password = student_id (hashed)
* Manage appointment requests:

  * Accept: Pending → In Process
  * Reject:

    * Require reason (modal)
    * Move to history with rejection_reason
* Handle reschedule requests:

  * Accept → update date
  * Reject → no change
* Mark appointments:

  * Settled
  * No-Show
* View:

  * Today's scheduled appointments
  * Future scheduled appointments
* Admin history log of actions
* View full appointment details (including dynamic "details" field)

# SYSTEM REQUIREMENTS

* Password hashing (PHP password_hash)
* PHP session-based authentication
* Input validation (frontend + backend based on appointment type)
* Clean error handling

# SEED SCRIPT

Create seed.php to initialize:

* Admin account:

  * username: admin
  * password: 1 (hashed)

# OUTPUT REQUIREMENTS

1. File structure (e.g., /config, /actions, /views)
2. Firebase JSON schema
3. seed.php
4. Sample PHP code for:

   * Authentication
   * Appointment CRUD
   * Status sorting logic (explicit implementation)
5. Clean, readable code using:

   * require_once
   * meaningful variable names

# CONSTRAINTS

* No frameworks (no Laravel, etc.)
* Keep logic simple and explainable
* Avoid over-engineering
* Code must be understandable by a 3rd-year IT student

# EXECUTION RULE

Do NOT skip logic.
Ensure all features and workflows are fully implemented, especially:

* Status-based sorting
* Dynamic handling of appointment "details" per type
* Validation per appointment type

