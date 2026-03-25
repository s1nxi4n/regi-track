# RegiTrack - Appointment Tracking System

A PHP + Firebase-based appointment tracking system for PHINMA Education.

---

## Overview

RegiTrack is a web-based system that allows students to request documents (TOR, Diploma, Request RF, Certificate) and enables administrators to manage these appointments through a centralized dashboard.

---

## Tech Stack

- **Backend:** PHP (procedural/simple OOP)
- **Database:** Firebase Realtime Database (REST API)
- **Frontend:** HTML5, CSS3, Vanilla JavaScript
- **No frameworks** - Pure PHP only

---

## File Structure

```
/home/xian/Documents/final/
├── config/
│   ├── constants.php      # Roles, statuses, appointment types
│   └── firebase.php       # Firebase URL and secret
├── includes/
│   ├── icon.php           # SVG icon system
│   ├── icon.php          # Firebase CRUD operations
│   ├── auth-check.php    # Session/role verification
│   ├── layout-admin.php  # Admin sidebar layout
│   ├── layout-student.php # Student sidebar layout
│   └── layout-end.php    # Close layout tags
├── actions/
│   ├── auth/             # login, logout, change-password
│   ├── student/           # create, edit, cancel, reschedule
│   └── admin/             # add-student, accept, reject, schedule, etc.
├── views/
│   ├── login.php         # Student login
│   ├── admin/login.php   # Admin login
│   ├── change-password.php
│   ├── student/          # dashboard, create, edit, view, notifications, history
│   └── admin/            # dashboard, add-student, manage-appointment, history
├── assets/
│   ├── css/style.css    # Design system (dark theme)
│   └── js/main.js        # Dynamic form fields, modals
├── index.php             # Root redirect
├── seed.php              # Creates admin account
└── busy-seed.php         # Creates test data
```

---

## User Roles

### 1. Student
- Create, edit, cancel, reschedule appointments
- View notifications
- View history
- Change password (first-time login)

### 2. Admin
- Add new students
- Accept/reject appointments
- Schedule appointments
- Reschedule/cancel appointments
- Mark as settled/no-show
- View activity logs
- Change password

---

## Appointment Flow

```
Student submits request (Pending)
        ↓
Admin reviews → Accept or Reject
        ↓
If Accepted → Admin schedules date (Scheduled)
        ↓
Student picks up → Admin marks as Settled
```

### Statuses
- **Pending** - Awaiting admin review
- **Scheduled** - Date confirmed, awaiting pickup
- **Settled** - Document picked up
- **No-Show** - Student didn't show up
- **Rejected** - Request denied
- **Cancelled** - Cancelled by student or admin

### Reschedule Flow
- Student requests reschedule → Status remains "Pending" with `rescheduled_date` and `reschedule_reason`
- Admin can Accept (updates date) or Reject (keeps original date)

---

## Login System

### Student Login
- **URL:** `/views/login.php`
- **Credentials:** Student ID (xx-xxxx-xxxxxx) or email (*.ui@phinmaed.com)
- **Default Password:** `1` (must change on first login)

### Admin Login
- **URL:** `/views/admin/login.php`
- **Username:** `admin`
- **Password:** Set via `seed.php`

### Validation
- Student login rejects admin credentials
- Admin login rejects student IDs/emails

---

## Features

### Student Features
1. **Dashboard** - View active appointments
2. **Create Appointment** - Dynamic form fields based on document type
3. **Edit Appointment** - Modify pending requests
4. **View Appointment** - Full details with status
5. **Reschedule** - Request date change (only for Scheduled)
6. **Cancel** - Cancel appointment (reason required for Scheduled)
7. **Notifications** - Real-time alerts for status changes
8. **History** - View completed/cancelled appointments
9. **Change Password** - Force change on first login

### Admin Features
1. **Dashboard** - Tab-based navigation (Today, Pending, Future, Reschedule)
2. **Stats Cards** - Clickable to filter sections
3. **Add Student** - Create new student accounts
4. **Manage Appointment** - Full action controls:
   - Accept & Schedule
   - Reject
   - Reschedule
   - Mark as Settled
   - Mark as No-Show
   - Cancel
5. **Activity Logs** - Track all admin actions
6. **Change Password**

---

## Database Schema (Firebase)

### Users
```json
{
  "student_id": "23-1234-567890",
  "password": "$2y$10$...",
  "role": "student",
  "full_name": "Juan Dela Cruz",
  "email": "juan.cruz.ui@phinmaed.com",
  "is_first_login": true/false
}
```

### Appointments
```json
{
  "student_id": "23-1234-567890",
  "type": "tor|diploma|request_rf|certificate",
  "date": "2026-03-27",
  "status": "Pending|Scheduled|Settled|No-Show|Rejected|Cancelled",
  "details": { ... },
  "rejection_reason": "",
  "cancellation_reason": "",
  "rescheduled_date": "",
  "reschedule_reason": "",
  "created_at": "timestamp"
}
```

### Notifications
```json
{
  "appointment_id": "...",
  "type": "accepted|rejected|reschedule_accepted|reschedule_rejected|cancelled|settled|no_show",
  "message": "...",
  "is_read": true/false,
  "created_at": "timestamp"
}
```

### Admin Logs
```json
{
  "action": "Accepted Appointment",
  "admin_id": "admin",
  "appointment_id": "...",
  "details": "...",
  "timestamp": "..."
}
```

---

## API Endpoints

Base URL: `https://regitrackdb-default-rtdb.asia-southeast1.firebasedatabase.app/`
Auth: `?auth=rEEWOVbG9kmWy0JHZkDXFWKTBcmsHbVQkoLXJJjm`

All operations go through `/includes/firebase-helper.php` functions:
- `getUsers()`, `getUser($id)`, `getUserByEmail($email)`
- `getAppointments()`, `getAppointment($id)`, `getAppointmentsByStudent($id)`
- `createAppointment()`, `updateAppointment()`, `deleteAppointment()`
- `getNotifications()`, `markAllNotificationsRead()`
- `getAdminLogs()`, `createAdminLog()`

---

## Running the System

```bash
cd /home/xian/Documents/final
php -S localhost:8000
```

Then visit:
- Student: `http://localhost:8000/views/login.php`
- Admin: `http://localhost:8000/views/admin/login.php`

### Test Data
Run `php busy-seed.php` to create test students with various appointment statuses.

### Admin Account
Run `php seed.php` to create admin (username: `admin`, password: `1`)

---

## Design System

### Colors (Dark Theme)
- Background: `#08080c`
- Surface: `#101018`
- Accent: `#7c5cff`
- Success: `#22c55e`
- Warning: `#f59e0b`
- Danger: `#ef4444`

### Typography
- Font: DM Sans
- Headings: Bold, various sizes

### Components
- Cards with headers
- Tables with hover states
- Status badges (color-coded)
- Modal dialogs
- Form inputs with focus states

---

## Security

- Passwords hashed with `password_hash()`
- Session-based authentication
- Role-based access control
- Input sanitization with `htmlspecialchars()`
- CSRF protection via session checks

---

## Notes for Project Defense

1. **Simple & Modular** - Each file has a single responsibility
2. **No Frameworks** - Pure PHP demonstrates understanding of fundamentals
3. **Firebase Integration** - Shows ability to work with REST APIs
4. **Clean Code** - Readable, explainable for 3rd-year students
5. **Error Handling** - Proper validation and user feedback

---

## Future Improvements (Optional)

- Email notifications
- PDF document generation
- SMS reminders
- Analytics dashboard
- Multi-admin support