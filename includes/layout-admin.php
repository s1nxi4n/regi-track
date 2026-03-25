<?php 
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
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><text y='.9em' font-size='90'>📋</text></svg>">
</head>
<body>
    <div class="app-layout">
        <aside class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <div class="sidebar-logo-icon">📋</div>
                    <span>RegiTrack</span>
                </div>
            </div>
            
            <nav class="sidebar-nav">
                <div class="nav-section">
                    <div class="nav-section-title">Main</div>
                    <a href="/views/admin/dashboard.php" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                        <span class="nav-link-icon">📊</span>
                        Dashboard
                    </a>
                    <a href="/views/admin/add-student.php" class="nav-link <?= $currentPage === 'add-student' ? 'active' : '' ?>">
                        <span class="nav-link-icon">👤</span>
                        Add Student
                    </a>
                </div>
                
                <div class="nav-section">
                    <div class="nav-section-title">System</div>
                    <a href="/views/admin/history.php" class="nav-link <?= $currentPage === 'history' ? 'active' : '' ?>">
                        <span class="nav-link-icon">📜</span>
                        Activity Logs
                    </a>
                    <a href="/views/change-password.php" class="nav-link <?= $currentPage === 'password' ? 'active' : '' ?>">
                        <span class="nav-link-icon">🔐</span>
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
