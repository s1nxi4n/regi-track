# RegiTrack - Complete Codebase Architecture Guide

This document provides an exhaustive analysis of the RegiTrack system, from the highest-level architecture down to the smallest functions. Use this to understand every detail of how the system works.

---

## Table of Contents

1. [Project Overview](#1-project-overview)
2. [Directory & File Structure](#2-directory--file-structure)
3. [Module & Class Analysis](#3-module--class-analysis)
4. [Function & Method Analysis](#4-function--method-analysis)
5. [Data Flow & Dependencies](#5-data-flow--dependencies)
6. [Execution Flow](#6-execution-flow)

---

## 1. Project Overview

### 1.1 Technology Stack

| Layer | Technology |
|-------|-------------|
| **Backend** | PHP (pure, no frameworks) |
| **Database** | Firebase Realtime Database (REST API) |
| **Frontend** | HTML5, CSS3, Vanilla JavaScript |
| **Styling** | Custom CSS with CSS variables |
| **Icons** | Inline SVG |
| **Password Hashing** | PHP `password_hash()` / `password_verify()` |

### 1.2 Architecture Style

- **Monolithic** - All code in single PHP application
- **Layered** - Clear separation: Config → Includes → Actions → Views
- **MVC-lite** - Actions act as Controllers, Views as Templates, Firebase as Model

### 1.3 System Purpose

RegiTrack is a document appointment tracking system for PHINMA Education that allows students to request documents (TOR, Diploma, Request RF, Certificates) and enables administrators to manage these appointments.

---

## 2. Directory & File Structure

### 2.1 Root Directory Overview

```
/home/xian/Documents/final/
├── config/                      # System configuration
├── includes/                   # Shared resources & helpers
├── actions/                    # Form handlers (POST endpoints)
├── views/                      # Page templates (GET endpoints)
├── assets/                     # Static files (CSS, JS)
├── index.php                   # Root entry point
├── seed-admin.php              # Create admin account
├── seed-busy.php               # Create test data
└── README.md                   # This documentation
```

### 2.2 Config Module (`config/`)

| File | Purpose |
|------|---------|
| `constants.php` | System-wide constants (roles, statuses, appointment types, status ordering) |
| `firebase.php` | Firebase URL and authentication secret |

**Key Constants in `constants.php`:**

```php
// Roles
ROLE_ADMIN = 'admin'
ROLE_STUDENT = 'student'

// Appointment Statuses
STATUS_PENDING = 'Pending'
STATUS_SCHEDULED = 'Scheduled'
STATUS_SETTLED = 'Settled'
STATUS_NO_SHOW = 'No-Show'
STATUS_REJECTED = 'Rejected'
STATUS_CANCELLED = 'Cancelled'

// Status Display Order (for sorting)
STATUS_ORDER = [
    'Scheduled' => 1,
    'In Process' => 2,
    'Pending' => 3
]

// Appointment Types with Labels and Fields
$APPOINTMENT_TYPES = [
    'tor' => ['label' => 'Transcript of Records (TOR)', 'fields' => [...]],
    'diploma' => ['label' => 'Diploma', 'fields' => [...]],
    'request_rf' => ['label' => 'Request RF', 'fields' => [...]],
    'certificate' => ['label' => 'Certificate', 'fields' => [...]]
]
```

### 2.3 Includes Module (`includes/`)

| File | Purpose |
|------|---------|
| `icon.php` | SVG icon helper function |
| `firebase-helper.php` | All Firebase CRUD operations |
| `session.php` | Secure session management (timeout, fixation prevention, safe redirects) |
| `auth-check.php` | Simple wrapper for session.php functions |
| `layout-admin.php` | Admin layout template (sidebar, header) |
| `layout-student.php` | Student layout template (sidebar, header) |
| `layout-end.php` | Closing tags for layouts |
| `header.php` | Legacy header (unused in new UI) |
| `footer.php` | Legacy footer (unused in new UI) |

### 2.4 Actions Module (`actions/`)

**Auth Actions (`actions/auth/`):**
| File | HTTP Method | Purpose |
|------|-------------|---------|
| `login.php` | POST | Process login credentials, create session |
| `logout.php` | POST | Destroy session, redirect to login |
| `change-password.php` | POST | Validate and update password |

**Student Actions (`actions/student/`):**
| File | HTTP Method | Purpose |
|------|-------------|---------|
| `create-appointment.php` | POST | Create new appointment |
| `edit-appointment.php` | POST | Update existing appointment |
| `cancel-appointment.php` | POST | Cancel appointment with reason |
| `reschedule-appointment.php` | POST | Request date change |
| `clear-notifications.php` | POST | Delete all notifications |
| `clear-history.php` | POST | Remove items from history |

**Admin Actions (`actions/admin/`):**
| File | HTTP Method | Purpose |
|------|-------------|---------|
| `add-student.php` | POST | Create new student account |
| `accept-appointment.php` | POST | Accept and schedule appointment |
| `reject-appointment.php` | POST | Reject with reason |
| `accept-reschedule.php` | POST | Approve date change |
| `reject-reschedule.php` | POST | Deny date change |
| `admin-reschedule.php` | POST | Admin changes date |
| `mark-settled.php` | POST | Mark as completed |
| `mark-no-show.php` | POST | Mark as no-show |
| `cancel-appointment.php` | POST | Admin cancels appointment |
| `clear-logs.php` | POST | Clear activity logs |
| `schedule-appointment.php` | POST | Set schedule date |
| `reschedule-inprocess.php` | POST | Legacy action |

### 2.5 Views Module (`views/`)

**Login Views:**
| File | Purpose |
|------|---------|
| `login.php` | Student login page |
| `admin/login.php` | Admin login page |

**Shared Views:**
| File | Purpose |
|------|---------|
| `change-password.php` | Password change page (works for both roles) |

**Admin Views (`views/admin/`):**
| File | Purpose |
|------|---------|
| `dashboard.php` | Main admin dashboard with tabs |
| `add-student.php` | Add student form |
| `manage-appointment.php` | View/manage single appointment |
| `history.php` | Activity logs |

**Student Views (`views/student/`):**
| File | Purpose |
|------|---------|
| `dashboard.php` | Student dashboard with appointments |
| `create-appointment.php` | New appointment form |
| `edit-appointment.php` | Edit appointment form |
| `view-appointment.php` | View appointment details |
| `reschedule-appointment.php` | Request reschedule form |
| `notifications.php` | Notification list |
| `history.php` | Completed appointments |

### 2.6 Assets Module (`assets/`)

| File | Purpose |
|------|---------|
| `css/style.css` | Complete design system (all styles) |
| `js/main.js` | Dynamic form fields, modal handling |

### 2.7 Entry Points

| File | Purpose |
|------|---------|
| `index.php` | Root URL handler - redirects based on auth status |
| `views/login.php` | Student login entry |
| `views/admin/login.php` | Admin login entry |

---

## 3. Module & Class Analysis

### 3.1 Config Module

**Purpose:** Centralized system configuration

**Components:**
- `constants.php` - Defines all system constants
- `firebase.php` - Stores credentials

**Relationships:**
- Required by all other PHP files via `require_once`
- No dependencies on other modules

### 3.2 Database Module (`includes/firebase-helper.php`)

**Purpose:** All Firebase CRUD operations - central data layer

**Key Functions:**
```php
// User Operations
getUsers()                      // GET /users.json - Get all users
getUser($id)                    // GET /users/{id}.json - Get single user
getUserByEmail($email)          // Search users by email field
createUser($data)               // POST /users.json - Create new user
updateUser($id, $data)          // PATCH /users/{id}.json - Update user
deleteUser($id)                 // DELETE /users/{id}.json - Delete user

// Appointment Operations
getAppointments()               // GET /appointments.json - Get all appointments
getAppointment($id)            // GET /appointments/{id}.json - Get single
getAppointmentsByStudent($id)   // Filter by student_id - Get student's appointments
createAppointment($data)       // POST /appointments.json - Create appointment
updateAppointment($id, $data)  // PATCH /appointments/{id}.json - Update
deleteAppointment($id)          // DELETE /appointments/{id}.json - Delete

// Notification Operations
getNotifications($studentId)   // GET /notifications/{studentId}.json
createNotification($studentId, $data) // POST /notifications/{studentId}.json
markAllNotificationsRead($studentId) // PATCH all notifications to is_read: true

// Admin Log Operations
getAdminLogs()                  // GET /admin_logs.json
createAdminLog($data)           // POST /admin_logs.json
```

**Dependencies:**
- Firebase REST API via cURL
- Config module (for constants)

**Relationships:**
- Called by all action files for database operations
- Called by view files for data retrieval

### 3.3 Session Module (`includes/session.php`)

**Purpose:** Secure session management with timeout and fixation prevention

**Key Functions:**
```php
startSession()                  // Start PHP session with secure cookie settings
regenerateSession()             // Create new session ID (prevents hijacking)
destroySession()               // Completely wipe session
createUserSession($id, $role, $name)  // Create session after login
isLoggedIn()                   // Check if user_id is set
isSessionValid($timeout = 1800) // Check if session hasn't expired (30 min)
updateActivity()               // Update last_activity timestamp
requireLogin()                  // Redirect to login if not authenticated
requireRole($role)             // Redirect to own dashboard if wrong role
getSafeRedirect($default)      // Prevent open redirect attacks
setRedirect($url)              // Store redirect URL safely
```

**Session Structure:**
```php
$_SESSION = [
    'user_id' => 'admin' | '23-xxxx-xxxxxx',     // User identifier
    'student_id' => 'admin' | '23-xxxx-xxxxxx',  // Backward compatibility
    'role' => 'admin' | 'student',                // User role
    'full_name' => 'Administrator' | 'Juan Dela Cruz',  // Display name
    'login_time' => 1234567890,                   // When user logged in
    'last_activity' => 1234567890                  // Last activity timestamp
]
```

**Security Features:**
- Session timeout: 30 minutes of inactivity
- Session fixation prevention: ID regenerated on login/logout
- Secure cookies: HttpOnly, Lax same-site policy
- Open redirect protection: Only whitelisted redirect URLs allowed

**Dependencies:**
- PHP session functions
- Constants module (for roles)

**Relationships:**
- Used by auth-check.php for authentication
- Used by all protected view pages and action files

### 3.4 Auth-Check Module (`includes/auth-check.php`)

**Purpose:** Simple wrapper around session.php for easy use

**Key Functions:**
```php
requireAuth()       // Alias for requireLogin()
requireOnceRole($role)  // Alias for requireRole()
getCurrentUser()    // Get user info from session
```

**Note:** This file just wraps `session.php` for simpler syntax in view files.

---

### 3.5 Layout Modules (`includes/layout-*.php`)

**Purpose:** Reusable page templates with consistent sidebar navigation

**Admin Layout (`layout-admin.php`):**
- Full HTML document structure
- Admin sidebar with:
  - Overview → Dashboard
  - Appointments (collapsible) → Today's Schedule, Pending, Future, Reschedule
  - Management → Add Student, Activity Logs, Change Password
- User menu with avatar, name, role, logout button
- Top bar with page title and date
- Content area wrapper

**Student Layout (`layout-student.php`):**
- Full HTML document structure
- Student sidebar with:
  - Appointments → Dashboard, New Appointment, Notifications
  - Account → History, Change Password
- User menu with avatar, name, role, logout button
- Top bar with page title and date
- Content area wrapper

**Layout End (`layout-end.php`):**
- Closes main content div
- Closes app-layout div
- Includes JavaScript
- Closes body and html tags

### 3.6 Icon Module (`includes/icon.php`)

**Purpose:** SVG icon system for consistent UI

**Function:**
```php
icon($name, $size = 20)  // Returns SVG string
```

**Available Icons:**
- `clipboard` - Document/clipboard
- `dashboard` - Dashboard
- `calendar` - Calendar
- `calendar-check` - Calendar with check
- `clock` - Clock/waiting
- `refresh` - Refresh/reschedule
- `check` - Success/check
- `x` - Close/error
- `x-circle` - Error circle
- `ban` - Cancel
- `party` - Celebration
- `alert` - Warning
- `user` - Single user
- `users` - Multiple users
- `file-text` - Document
- `graduation` - Diploma
- `scroll` - History
- `lock` - Password
- `plus` - Add
- `bell` - Notification
- `trash` - Delete
- `info` - Information

---

## 4. Function & Method Analysis

### 4.1 Core Firebase Function

**Function:** `firebaseRequest($method, $path, $data = null)`

| Aspect | Details |
|--------|---------|
| **Inputs** | `$method` (GET/POST/PATCH/DELETE), `$path` (Firebase path), `$data` (array, optional) |
| **Process** | 1. Build Firebase REST URL with auth token, 2. Set cURL options, 3. Execute request, 4. Parse JSON response |
| **Output** | Decoded JSON or null on error |
| **Side Effects** | HTTP request to external Firebase API |
| **Error Handling** | Returns null on failure, doesn't throw exceptions |

**Internal Logic:**
```php
function firebaseRequest($method, $path, $data = null) {
    $url = FIREBASE_URL . $path . '?auth=' . FIREBASE_SECRET;
    
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    
    if ($data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }
    
    $response = curl_exec($ch);
    curl_close($ch);
    
    return json_decode($response, true);
}
```

### 4.2 Authentication Functions

**Function:** `isLoggedIn()`

| Aspect | Details |
|--------|---------|
| **Inputs** | None |
| **Process** | Start session, check if session variables exist |
| **Output** | Boolean (true if logged in) |
| **Side Effects** | Calls `sessionStart()` |

```php
function isLoggedIn() {
    sessionStart();
    return isset($_SESSION['student_id']) && isset($_SESSION['role']);
}
```

**Function:** `requireOnceRole($requiredRole)`

| Aspect | Details |
|--------|---------|
| **Inputs** | `$requiredRole` (ROLE_ADMIN or ROLE_STUDENT) |
| **Process** | Check logged in and role matches, redirect if not |
| **Output** | Void (exits if unauthorized) |
| **Side Effects** | May redirect to login page |

```php
function requireOnceRole($requiredRole) {
    if (!isLoggedIn() || $_SESSION['role'] !== $requiredRole) {
        header('Location: /views/login.php');
        exit;
    }
}
```

### 4.3 Session Functions (`includes/session.php`)

**Function:** `startSession()`

| Aspect | Details |
|--------|---------|
| **Inputs** | None |
| **Process** | Configure secure cookie params, start PHP session |
| **Output** | Session started |
| **Security** | Sets HttpOnly, Lax same-site, no lifetime (browser close) |

```php
function startSession() {
    session_set_cookie_params([
        'lifetime' => 0,
        'path' => '/',
        'domain' => '',
        'secure' => false,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    session_start();
}
```

**Function:** `createUserSession($userId, $role, $fullName)`

| Aspect | Details |
|--------|---------|
| **Inputs** | `$userId`, `$role`, `$fullName` |
| **Process** | Regenerate session ID, set session variables |
| **Output** | Session created |
| **Security** | Calls `regenerateSession()` to prevent fixation |

**Function:** `isSessionValid($timeout = 1800)`

| Aspect | Details |
|--------|---------|
| **Inputs** | `$timeout` in seconds (default 30 min) |
| **Process** | Check if last_activity exists and hasn't expired |
| **Output** | Boolean |
| **Side Effects** | None |

```php
function isSessionValid($timeout = 1800) {
    if (!isset($_SESSION['last_activity'])) {
        return false;
    }
    $elapsed = time() - $_SESSION['last_activity'];
    return $elapsed < $timeout;
}
```

**Function:** `getSafeRedirect($default)`

| Aspect | Details |
|--------|---------|
| **Inputs** | `$default` - fallback URL if redirect is invalid |
| **Process** | Validate and whitelist redirect URLs |
| **Output** | Safe redirect URL |
| **Security** | Prevents open redirect attacks |

Allowed redirects:
- `/views/student/dashboard.php`
- `/views/admin/dashboard.php`
- `/views/change-password.php`

---

### 4.4 Login Action (`actions/auth/login.php`)

**Process Flow:**

```
1. START
   │
   ├─► Check if admin_login flag set in POST
   │
   ├─► IF admin_login:
   │   │
   │   ├─► Validate username and password not empty
   │   │
   │   ├─► REJECT if email format (@) or student ID format (xx-xxxx-xxxxxx)
   │   │      (Error: "Student login not allowed here")
   │   │
   │   ├─► Get admin user from Firebase
   │   │
   │   ├─► Verify password with password_verify()
   │   │
   │   ├─► IF valid:
   │   │   ├─► Set session: student_id='admin', role='admin'
   │   │   ├─► Set full_name from user data
   │   │   └─► Redirect to /views/admin/dashboard.php
   │   │
   │   └─► ELSE:
   │          └─► Set error → redirect to /views/admin/login.php
   │
   └─► ELSE (student login):
       │
       ├─► Validate student_id/email and password not empty
       │
       ├─► REJECT if input is 'admin' (Error: "Use admin portal")
       │
       ├─► IF email format (contains @):
       │      ├─► Call getUserByEmail()
       │      └─► Get user data and student ID
       │
       ├─► ELSE (student ID format):
       │      └─► Call getUser(student_id)
       │
       ├─► Verify password with password_verify()
       │
       ├─► IF valid:
       │   ├─► IF is_first_login:
       │   │      └─► Redirect to /views/change-password.php
       │   │
       │   └─► ELSE:
       │          ├─► Set session with user data
       │          └─► Redirect to /views/student/dashboard.php
       │
       └─► ELSE:
              └─► Set error → redirect to /views/login.php
```

### 4.5 Create Appointment Action (`actions/student/create-appointment.php`)

**Process Flow:**

```
1. START (POST data: type, date, details[])
   │
   ├─► Validate type is set
   ├─► Validate date is set and >= today
   │
   ├─► SWITCH type:
   │   ├─► tor: require contact_no, purpose, copy_quantity
   │   ├─► diploma: require year_graduated
   │   ├─► request_rf: require contact_no, semester, school_year, purpose
   │   └─► certificate: require contact_no, course, certification_type, purpose, copy_quantity
   │
   ├─► Create appointment data array:
   │   {
   │     student_id: from session,
   │     type: from POST,
   │     date: from POST,
   │     status: 'Pending',
   │     details: from POST[details],
   │     created_at: current timestamp
   │   }
   │
   ├─► Call createAppointment(data)
   │
   ├─► IF success:
   │   └─► Redirect to /views/student/dashboard.php
   │
   └─► ELSE:
       └─► Set error → redirect to /views/student/create-appointment.php
```

### 4.6 Admin Accept Appointment (`actions/admin/accept-appointment.php`)

**Process Flow:**

```
1. START (POST data: id, scheduled_date)
   │
   ├─► Get appointment from Firebase
   │
   ├─► Validate appointment exists
   ├─► Validate scheduled_date >= today
   │
   ├─► Update appointment:
   │   {
   │     status: 'Scheduled',
   │     date: scheduled_date
   │   }
   │
   ├─► Create notification for student:
   │   {
   │     type: 'accepted',
   │     message: "Your appointment has been scheduled for scheduled_date",
   │     is_read: false,
   │     created_at: timestamp
   │   }
   │
   ├─► Create admin log:
   │   {
   │     action: 'Accepted Appointment',
   │     admin_id: from session,
   │     appointment_id: id,
   │     details: "Scheduled for scheduled_date",
   │     timestamp: timestamp
   │   }
   │
   ├─► Redirect to /views/admin/dashboard.php
```

### 4.7 Dynamic Form Fields (Student Create/Edit)

**JavaScript in `views/student/create-appointment.php` and `edit-appointment.php`:**

The form displays different fields based on appointment type selection:

```javascript
const fieldsConfig = {
    tor: `
        <div class="form-group">
            <label for="contact_no">Contact Number</label>
            <input type="tel" name="details[contact_no]" required>
        </div>
        <div class="form-group">
            <label for="purpose">Purpose</label>
            <input type="text" name="details[purpose]" required>
        </div>
        <div class="form-group">
            <label for="copy_quantity">Number of Copies</label>
            <input type="number" name="details[copy_quantity]" min="1" required>
        </div>
    `,
    diploma: `...`,
    request_rf: `...`,
    certificate: `...`
};

typeSelect.addEventListener('change', function() {
    if (fieldsConfig[this.value]) {
        dynamicFields.innerHTML = fieldsConfig[this.value];
    }
});
```

---

## 5. Data Flow & Dependencies

### 5.1 Session State Flow

```
┌─────────────────────────────────────────────────────────────────┐
│                      SESSION STATE                              │
├─────────────────────────────────────────────────────────────────┤
│ $_SESSION = {                                                  │
│     'user_id' => 'admin' | '23-xxxx-xxxxxx',                  │
│     'student_id' => 'admin' | '23-xxxx-xxxxxx', (backward compat)
│     'role' => 'admin' | 'student',                            │
│     'full_name' => 'Administrator' | 'Juan Dela Cruz',       │
│     'login_time' => 1234567890,                               │
│     'last_activity' => 1234567890                             │
│ }                                                              │
└─────────────────────────────────────────────────────────────────┘
                               │
          ┌────────────────────┼────────────────────┐
          ▼                    ▼                    ▼
     ┌──────────┐        ┌──────────┐         ┌──────────┐
     │ Session  │        │  Views  │         │ Actions  │
     │  (PHP)   │        │         │         │          │
     │          │        │         │         │ Process  │
     │ isLogged │        │ Layout  │         │ Form     │
     │In()      │        │ Choice  │         │ Data     │
     │          │        │         │         │          │
     │ require  │        │         │         │          │
     │Login()   │        │         │         │          │
     └──────────┘        └──────────┘         └──────────┘
```
┌─────────────────────────────────────────────────────────────────┐
│                      SESSION STATE                              │
├─────────────────────────────────────────────────────────────────┤
│ $_SESSION = {                                                  │
│     'student_id' => 'admin' | '23-xxxx-xxxxxx',               │
│     'role' => 'admin' | 'student',                            │
│     'full_name' => 'Administrator' | 'Juan Dela Cruz'         │
│ }                                                              │
└─────────────────────────────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         ▼                    ▼                    ▼
    ┌──────────┐        ┌──────────┐         ┌──────────┐
    │  Auth    │        │  Views  │         │ Actions  │
    │  Check   │        │         │         │          │
    │          │        │         │         │          │
    │ isLogged │        │ Layout  │         │ Process  │
    │In()      │        │ Choice  │         │ Form     │
    │          │        │         │         │ Data     │
    │ require  │        │         │         │          │
    │OnceRole()│        │         │         │          │
    └──────────┘        └──────────┘         └──────────┘
```

### 5.2 Request-Response Flow

```
┌─────────────┐     HTTP      ┌──────────────┐     HTTP       ┌─────────────┐
│   Browser   │ ───────────► │   PHP App    │ ────────────► │  Firebase  │
│             │    Request   │              │    Request    │  (Database)│
│             │              │              │               │             │
│  User       │              │  index.php  │               │             │
│  clicks     │              │      │      │               │             │
│  form       │              │      ▼      │               │             │
│             │              │  session.php│               │             │
│             │              │  (start,    │               │             │
│             │              │   validate) │               │             │
│             │              │      │      │               │             │
│             │              │      ▼      │               │             │
│             │              │  auth-      │               │             │
│             │              │  check.php  │               │             │
│             │              │      │      │               │             │
│             │              │      ▼      │               │             │
│             │              │  firebase-  │               │             │
│             │              │  helper.php  │               │             │
│             │              │      │      │               │             │
│             │              │      ▼      │               │             │
│             │              │  action.php  │               │             │
│             │              │      │      │               │             │
│             │              │      ▼      │               │             │
│             │              │  view.php   │               │             │
│             │◄──────────── │              │ ◄─────────── │             │
│             │   Response  │              │   Response   │             │
└─────────────┘              └──────────────┘              └─────────────┘
```

### 5.3 Database Interaction Flow

```
                    ┌─────────────────────────────────────────┐
                    │           Firebase Realtime DB           │
                    │                                         │
                    │  /                                       │
                    │  ├── users/                             │
                    │  │   ├── admin/                         │
                    │  │   │   ├── password                     │
                    │  │   │   ├── role                         │
                    │  │   │   ├── full_name                   │
                    │  │   │   └── is_first_login              │
                    │  │   └── 23-xxxx-xxxxxx/                 │
                    │  │       └── ...                         │
                    │  │                                       │
                    │  ├── appointments/                       │
                    │  │   └── -Nkxxxx/                       │
                    │  │       ├── student_id                  │
                    │  │       ├── type                       │
                    │  │       ├── date                        │
                    │  │       ├── status                      │
                    │  │       ├── details                     │
                    │  │       └── created_at                  │
                    │  │                                       │
                    │  ├── notifications/                      │
                    │  │   └── 23-xxxx-xxxxxx/                 │
                    │  │       └── -Nkxxxx/                    │
                    │  │           ├── type                    │
                    │  │           ├── message                 │
                    │  │           └── is_read                  │
                    │  │                                       │
                    │  └── admin_logs/                        │
                    │      └── -Nkxxxx/                        │
                    │          ├── action                      │
                    │          ├── admin_id                    │
                    │          └── timestamp                   │
                    │                                         │
                    └─────────────────────────────────────────┘
                              │
         ┌────────────────────┼────────────────────┐
         ▼                    ▼                    ▼
┌──────────────┐      ┌──────────────┐      ┌──────────────┐
│    GET       │      │    POST      │      │   PATCH      │
│   (Read)     │      │   (Create)   │      │   (Update)   │
│              │      │              │      │              │
│ getUsers()   │      │ createUser() │      │ updateUser() │
│ getUser()    │      │ createAppt() │      │ updateAppt() │
│ getAppts()   │      │ createNotif()│      │ markRead()   │
│ getNotifs()  │      │ createLog()  │      │              │
└──────────────┘      └──────────────┘      └──────────────┘
```

### 5.4 External Dependencies

| Dependency | Purpose | Connection |
|------------|---------|------------|
| Firebase Realtime Database | Primary data store | REST API via cURL |
| PHP Session | User state management | Native PHP |
| DM Sans Font | Typography | Google Fonts CDN |
| JetBrains Mono Font | Code/ID display | Google Fonts CDN |

### 5.5 Data Validation Chain

```
User Input (Form)
       │
       ▼
PHP Action (POST handler)
       │
       ├──► Input validation (isset, empty checks)
       │
       ├──► Type-specific validation (switch on type)
       │
       ├──► Data sanitization (htmlspecialchars on output)
       │
       ├──► Firebase helper function
       │
       └──► Firebase (stored)
```

---

## 6. Execution Flow

### 6.1 Application Startup (Root URL)

```
http://localhost:8000/
          │
          ▼
┌─────────────────────┐
│     index.php       │
│                     │
│ require constants   │
│ require session.php │
│ startSession()      │
│                     │
│ if loggedIn & valid│
│   redirect to own  │
│   dashboard        │
│                     │
│ if !loggedIn:       │
│   redirect /login  │
│                     │
│ (404 handled for   │
│  non-existing URLs)│
└─────────────────────┘
```

### 6.2 Student Login Flow

```
http://localhost:8000/views/login.php
          │
          ▼
┌─────────────────────┐
│    views/login.php  │
│                     │
│ require constants   │
│ require auth-check  │ → requires session.php
│ require icon        │
│                     │
│ if isLoggedIn():    │
│   redirect to      │
│   appropriate      │
│   dashboard        │
│                     │
│ Show login form     │
└─────────────────────┘
          │
          │ POST
          ▼
┌─────────────────────┐
│ actions/auth/login │
│                     │
│ startSession()      │ ← secure cookie settings
│                     │
│ Validate inputs     │
│                     │
│ Check admin flag    │
│                     │
├─► If admin_login:   │
│   Check not student│
│   ID/email format   │
│   Verify admin     │
│   createUserSession│ → regenerates ID
│   getSafeRedirect  │ → validates redirect
│   Redirect admin   │
│                     │
├─► Else (student):   │
│   Check not 'admin'│
│   Look up user     │
│   Verify password  │
│   Check first login│
│   createUserSession│ → regenerates ID
│   getSafeRedirect  │ → validates redirect
│   Redirect student │
└─────────────────────┘
```
http://localhost:8000/views/login.php
         │
         ▼
┌─────────────────────┐
│    views/login.php  │
│                     │
│ require constants   │
│ require auth-check  │
│ require icon        │
│                     │
│ if isLoggedIn():    │
│   redirect to      │
│   appropriate      │
│   dashboard        │
│                     │
│ Show login form     │
└─────────────────────┘
         │
         │ POST
         ▼
┌─────────────────────┐
│ actions/auth/login  │
│                     │
│ session_start()     │
│                     │
│ Validate inputs     │
│                     │
│ Check admin flag    │
│                     │
├─► If admin_login:   │
│   Check not student│
│   ID/email format   │
│   Verify admin     │
│   Set admin session│
│   Redirect admin   │
│                     │
├─► Else (student):   │
│   Check not 'admin'│
│   Look up user     │
│   Verify password  │
│   Check first login│
│   Set student      │
│   session          │
│   Redirect student │
└─────────────────────┘
```

### 6.3 Protected Page Access

```
http://localhost:8000/views/admin/dashboard.php
          │
          ▼
┌─────────────────────┐
│  admin/dashboard   │
│                     │
│ require constants   │
│ require auth-check  │ → requires session.php
│ requireOnceRole    │ → calls requireLogin()
│ (ROLE_ADMIN)       │
│                     │
│ if !isLoggedIn()   │
│   OR !validSession:│
│   destroySession() │
│   redirect /login  │
│                     │
│ updateActivity()   │ → refresh timeout
│                     │
│ Get data from      │
│ Firebase           │
│                     │
│ Include layout      │
│ Render page        │
└─────────────────────┘
```

### 6.4 Form Submission Flow

```
User fills form → Clicks submit
          │
          ▼
POST to action file
          │
          ▼
Action processes:
  1. startSession() - secure cookie settings
  2. requireLogin() - check auth + timeout
  3. Validate input
  4. Call Firebase helper
  5. Create/Update data
  6. Create notifications (if needed)
  7. Create admin log (if admin)
  8. Redirect to next page
          │
          ▼
Browser receives redirect
          │
          ▼
New page loads with updated data
```

### 6.5 Appointment Lifecycle States

```
┌─────────┐     accept      ┌────────────┐    pickup     ┌─────────┐
│ PENDING │ ────────────► │ SCHEDULED │ ────────────► │ SETTLED│
└─────────┘                └────────────┘               └─────────┘
    │                         │
    │ reject                 │ mark
    ▼                        ▼
┌──────────┐              ┌─────────┐
│ REJECTED │              │ NO-SHOW │
└──────────┘              └─────────┘
    │
    │ cancel (student)
    ▼
┌────────────┐
│ CANCELLED │
└────────────┘
    │
    │ reschedule request
    ▼
(reschedule fields added, stays Pending)
```

### 6.6 Asynchronous Operations

There are no asynchronous operations in this system:

- **No AJAX calls** - All form submissions are standard POST requests
- **No WebSockets** - Notifications are fetched on page load
- **No JavaScript fetch** - All data comes from PHP rendering

Notifications are created synchronously when admin actions occur:
```
Admin accepts → Update appointment → Create notification → Create log → Redirect
```

---

## Summary

This completes the exhaustive analysis of the RegiTrack codebase. The system is built with:

- **Clean architecture** - Clear separation of config, includes, actions, and views
- **Simple data flow** - PHP sessions → Firebase REST API
- **No frameworks** - Pure PHP demonstrating fundamental web development
- **Role-based access** - Separate flows for students and admins
- **Complete CRUD** - All database operations via Firebase helper
- **Secure sessions** - Session timeout (30 min), fixation prevention, open redirect protection

### Security Features

| Feature | Implementation |
|---------|---------------|
| Session timeout | 30 minutes of inactivity via `isSessionValid()` |
| Session fixation | ID regenerated on login/logout via `regenerateSession()` |
| Secure cookies | HttpOnly, Lax same-site policy via `session_set_cookie_params()` |
| Open redirect protection | Only whitelisted URLs allowed via `getSafeRedirect()` |
| Auth check | All pages use `requireLogin()` or `requireRole()` |

Use this document as a reference to understand any part of the system's implementation.