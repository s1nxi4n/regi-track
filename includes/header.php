<?php 
$currentUser = getCurrentUser();
$pageTitle = $pageTitle ?? 'RegiTrack';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <div class="logo"><a href="/" style="color: #fff; text-decoration: none;">RegiTrack</a></div>
            <?php if (isLoggedIn()): ?>
            <div class="nav-links">
                <?php if ($currentUser['role'] === ROLE_ADMIN): ?>
                    <a href="/views/admin/dashboard.php">Dashboard</a>
                    <a href="/views/admin/add-student.php">Add Student</a>
                    <a href="/views/admin/history.php">Admin Logs</a>
                <?php else: ?>
                    <a href="/views/student/dashboard.php">Dashboard</a>
                    <a href="/views/student/create-appointment.php">New Appointment</a>
                    <a href="/views/student/history.php">My History</a>
                    <a href="/views/change-password.php">Change Password</a>
                <?php endif; ?>
                <a href="/actions/auth/logout.php">Logout (<?= htmlspecialchars($currentUser['student_id']) ?>)</a>
            </div>
            <?php endif; ?>
        </nav>
    </header>
    <main>
