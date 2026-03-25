# RegiTrack - Agent Guidelines

RegiTrack is a PHP + Firebase appointment tracking system built for a 3rd-year IT capstone project. This file provides guidelines for agents working on this codebase.

## Project Overview

- **Stack**: PHP (procedural/simple OOP), HTML5, CSS3, Firebase Realtime Database (REST API)
- **Architecture**: MVC-lite pattern with `/config`, `/actions`, `/views`, `/includes`, `/assets`
- **No frameworks** - Pure PHP, no Laravel, Composer, or dependencies
- **Purpose**: Simple, modular, beginner-friendly code for project defense

---

## 1. Build / Lint / Test Commands

### PHP Syntax Checking
```bash
# Check single file
php -l /path/to/file.php

# Check all PHP files
find /home/xian/Documents/final -name "*.php" -exec php -l {} \;
```

### Running the Application
```bash
cd /home/xian/Documents/final
php -S localhost:8000
```

Then visit `http://localhost:8000/views/login.php`

### Seed Database
```bash
php /home/xian/Documents/final/seed.php
```

### Testing Firebase Connection
```bash
php -r "require_once '/home/xian/Documents/final/includes/firebase-helper.php'; echo 'OK';"
```

---

## 2. Code Style Guidelines

### File Structure
```
/config           - Configuration files (firebase.php, constants.php)
/actions          - POST handlers (no HTML output)
/views            - Display pages (HTML + PHP)
/includes         - Reusable components (header, footer, auth, firebase-helper)
/assets           - CSS, JS, images
```

### PHP Conventions

#### Include Order (MANDATORY)
1. `config/constants.php` - MUST be included FIRST to define constants
2. `includes/auth-check.php` - Session and auth functions
3. `includes/firebase-helper.php` - Database operations
4. Local requires

```php
<?php
// CORRECT ORDER
require_once __DIR__ . '/../../config/constants.php';
require_once __DIR__ . '/../../includes/auth-check.php';
require_onceOnceRole(ROLE_ADMIN);  // Uses constants
require_once __DIR__ . '/../../includes/firebase-helper.php';
```

#### Naming Conventions
- **Files**: lowercase with hyphens (e.g., `create-appointment.php`)
- **Functions**: camelCase (e.g., `getAppointments()`, `requireAuth()`)
- **Constants**: UPPER_CASE with underscores (e.g., `ROLE_ADMIN`, `STATUS_PENDING`)
- **Variables**: camelCase (e.g., `$studentId`, `$appointmentData`)
- **Classes**: Not used in this project (procedural PHP)

#### Variable Naming
- Use meaningful names: `$studentId` NOT `$sid`
- Use singular for single items: `$appointment` NOT `$appointments`
- Use plural for arrays: `$appointments` NOT `$appointmentArray`

#### Functions
```php
// Good
function getAppointmentsByStudent($studentId) {
    $appointments = getAppointments();
    $result = [];
    foreach ($appointments as $id => $appointment) {
        if ($appointment['student_id'] === $studentId) {
            $result[$id] = $appointment;
        }
    }
    return $result;
}
```

### HTML/PHP Templates

#### Include Paths
- Use absolute paths from root: `/views/admin/dashboard.php`
- NOT relative: `../views/admin/dashboard.php`

```php
// CORRECT
<a href="/views/admin/dashboard.php">Dashboard</a>
<link rel="stylesheet" href="/assets/css/style.css">

// WRONG
<a href="../views/admin/dashboard.php">
<link rel="stylesheet" href="../assets/css/style.css">
```

#### Short Tags
- Use `<?php ?>` NOT short tags `<? ?>`
- Use `<?=` for output (safe with htmlspecialchars)

```php
// Good
<?php if ($error): ?>
    <div class="error"><?= htmlspecialchars($error) ?></div>
<?php endif; ?>
```

### CSS Guidelines
- Use CSS classes for styling, NOT inline styles
- Follow BEM-lite naming: `.status-Scheduled`, `.btn-primary`
- Keep it simple - no SCSS, no Tailwind

### JavaScript
- Vanilla JS only (no jQuery, no frameworks)
- Place in `/assets/js/main.js`
- Use `DOMContentLoaded` for event listeners

---

## 3. Firebase Integration

### Data Models

#### Users
```json
{
  "student_id": "23-1234-567890",
  "password": "$2y$10$...",
  "is_first_login": true,
  "role": "student"
}
```

#### Appointments
```json
{
  "student_id": "23-1234-567890",
  "type": "tor",
  "date": "2026-04-01",
  "status": "Pending",
  "details": {
    "contact_no": "09123456789",
    "purpose": "Job application",
    "copy_quantity": "2"
  },
  "rejection_reason": "",
  "rescheduled_date": "",
  "created_at": "2026-03-25 14:00:00"
}
```

### Status Order (STRICT)
1. Scheduled (1)
2. In Process (2)
3. Pending (3)

Always sort by this order first, then by date ASC.

### Firebase Helper Functions
All Firebase operations are in `/includes/firebase-helper.php`:
- `firebaseRequest($path, $method, $data)` - Low-level HTTP
- `getUsers()`, `getUser($id)`, `createUser()`, `updateUser()`
- `getAppointments()`, `getAppointment($id)`, `createAppointment()`, `updateAppointment()`, `deleteAppointment()`
- `logAdminAction()` - For admin audit logs

---

## 4. Error Handling

### User Errors (Display and Redirect)
```php
$_SESSION['error_message'] = 'Invalid input';
header('Location: /views/page.php');
exit;
```

### Critical Errors (Log and Exit)
```php
if (!$appointment) {
    header('Location: /views/admin/dashboard.php');
    exit;
}
```

### Input Validation
- Always validate form inputs server-side
- Use `trim()` for strings
- Use `?? ''` for optional parameters

---

## 5. Security

### Password Hashing
- ALWAYS use `password_hash()` and `password_verify()`
- NEVER store plain-text passwords

### XSS Prevention
- Always escape output: `htmlspecialchars($variable)`
- Use `<?= htmlspecialchars($var) ?>` in templates

### SQL Injection
- N/A (using Firebase REST API, not SQL)

---

## 6. Testing Checklist

Before marking any feature complete:
1. Run `php -l` on all modified files - NO syntax errors
2. Test login flow (admin and student)
3. Test appointment creation with all types (TOR, Diploma, Request RF, Certificate)
4. Test status sorting (Scheduled → In Process → Pending)
5. Test admin actions (accept, reject, mark settled/no-show)
6. Verify Firebase data structure is correct

---

## 7. Important Gotchas

### Include Order
Constants MUST be included before any file that uses them:
```php
// WRONG - will cause "Undefined constant" error
require_once __DIR__ . '/../../includes/auth-check.php';
require_onceOnceRole(ROLE_ADMIN);  // FAILS - constants not loaded
```

### Role Check
Always use the constant, not the string:
```php
// Good
requireOnceRole(ROLE_ADMIN);

// Bad
requireOnceRole('admin');
```

### Path Separators
Use forward slashes `/` for all paths (works on Windows too).

---

## Quick Reference

| Task | Command |
|------|---------|
| Check PHP syntax | `php -l file.php` |
| Run server | `php -S localhost:8000` |
| Seed database | `php seed.php` |
| Test Firebase | `php -r "require_once 'includes/firebase-helper.php'; getUsers();"` |
