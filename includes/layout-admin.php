<?php 
require_once __DIR__ . '/icon.php';
$currentPage = $currentPage ?? 'dashboard';
$unreadCount = $unreadCount ?? 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'RegiTrack') ?></title>
    <link rel="stylesheet" href="/assets/css/style.css">
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%237c5cff' stroke-width='2'><path d='M16 4h2a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V6a2 2 0 0 1 2-2h2'/><rect x='8' y='2' width='8' height='4' rx='1' ry='1'/></svg>">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon"><?= icon('clipboard', 24) ?></div>
                    <span>RegiTrack</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Overview</div>
                    <a href="/views/admin/dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-link-icon"><?= icon('dashboard') ?></span>
                        Dashboard
                    </a>
                </div>
                
                <div class="nav-section collapsed" id="appointments-nav">
                    <div class="nav-section-title nav-section-toggle" onclick="this.parentElement.classList.toggle('collapsed'); localStorage.setItem('appointmentsNav', this.parentElement.classList.contains('collapsed') ? 'collapsed' : 'expanded');">
                        <span>Appointments</span>
                        <svg class="chevron" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </div>
                    <div class="nav-section-items">
                        <a href="/views/admin/dashboard.php?tab=today" class="nav-link <?= ($currentPage === 'dashboard' && ($_GET['tab'] ?? '') === 'today') ? 'active' : '' ?>">
                            <span class="nav-link-icon"><?= icon('calendar') ?></span>
                            Today's Schedule
                            <?php if (!empty($todayCount)): ?>
                                <span class="nav-badge"><?= $todayCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/views/admin/dashboard.php?tab=pending" class="nav-link <?= ($currentPage === 'dashboard' && ($_GET['tab'] ?? '') === 'pending') ? 'active' : '' ?>">
                            <span class="nav-link-icon"><?= icon('clock') ?></span>
                            Pending
                            <?php if (!empty($pendingCount)): ?>
                                <span class="nav-badge"><?= $pendingCount ?></span>
                            <?php endif; ?>
                        </a>
                        <a href="/views/admin/dashboard.php?tab=future" class="nav-link <?= ($currentPage === 'dashboard' && ($_GET['tab'] ?? '') === 'future') ? 'active' : '' ?>">
                            <span class="nav-link-icon"><?= icon('calendar-check') ?></span>
                            Future
                        </a>
                        <a href="/views/admin/dashboard.php?tab=reschedule" class="nav-link <?= ($currentPage === 'dashboard' && ($_GET['tab'] ?? '') === 'reschedule') ? 'active' : '' ?>">
                            <span class="nav-link-icon"><?= icon('refresh') ?></span>
                            Reschedule
                            <?php if (!empty($rescheduleCount)): ?>
                                <span class="nav-badge"><?= $rescheduleCount ?></span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">Management</div>
                    <a href="/views/admin/add-student.php" class="nav-link <?= $currentPage === 'add-student' ? 'active' : '' ?>">
                        <span class="nav-link-icon"><?= icon('user') ?></span>
                        Add Student
                    </a>
                    <a href="/views/admin/history.php" class="nav-link <?= $currentPage === 'history' ? 'active' : '' ?>">
                        <span class="nav-link-icon"><?= icon('scroll') ?></span>
                        Activity Logs
                    </a>
                    <a href="/views/change-password.php" class="nav-link <?= $currentPage === 'password' ? 'active' : '' ?>">
                        <span class="nav-link-icon"><?= icon('lock') ?></span>
                        Change Password
                    </a>
                </div>
            </nav>
            
            <div class="sidebar-footer">
                <div class="user-menu">
                    <div class="user-avatar"><?= strtoupper(substr($_SESSION['full_name'] ?? 'A', 0, 2)) ?></div>
                    <div class="user-info">
                        <div class="user-name"><?= htmlspecialchars($_SESSION['full_name'] ?? 'Admin') ?></div>
                        <div class="user-role">Administrator</div>
                    </div>
                    <a href="/actions/auth/logout.php" class="btn btn-ghost btn-sm" title="Logout">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4"/>
                            <polyline points="16 17 21 12 16 7"/>
                            <line x1="21" y1="12" x2="9" y2="12"/>
                        </svg>
                    </a>
                </div>
            </div>
        </aside>
        
        <main class="main-content">
            <div class="top-bar">
                <h1 class="page-title"><?= htmlspecialchars($pageTitle ?? 'Dashboard') ?></h1>
                <div class="top-bar-actions">
                    <span class="text-muted"><?= date('F d, Y') ?></span>
                </div>
            </div>
            
            <div class="page-content">
