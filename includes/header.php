<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$currentPage = basename($_SERVER['PHP_SELF']);
$currentFolder = basename(dirname($_SERVER['PHP_SELF']));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>
        <?php echo isset($pageTitle) ? $pageTitle : "FixNest"; ?> | FixNest
    </title>

    <link rel="stylesheet" href="/FixNestPHP/assets/style.css">
</head>
<body>

<div class="app-layout">

    <aside class="sidebar">
        <div class="brand">
            <div class="brand-logo">F</div>
            <div>
                <h2>FixNest</h2>
                <span>Hostel Management</span>
            </div>
        </div>

        <nav class="sidebar-nav">

            <?php if ($currentFolder === "student"): ?>

                <p class="nav-title">Student Menu</p>

                <a
                    href="/FixNestPHP/student/dashboard.php"
                    class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
                >
                    <span>🏠</span>
                    Dashboard
                </a>

                <a
                    href="/FixNestPHP/student/raise-complaint.php"
                    class="<?php echo $currentPage === 'raise-complaint.php' ? 'active' : ''; ?>"
                >
                    <span>📝</span>
                    Raise Complaint
                </a>

                <a
                    href="/FixNestPHP/student/my-complaints.php"
                    class="<?php echo $currentPage === 'my-complaints.php' ? 'active' : ''; ?>"
                >
                    <span>📂</span>
                    My Complaints
                </a>
                <a
                   href="/FixNestPHP/student/profile.php"
                   class="<?php echo $currentPage === 'profile.php' ? 'active' : ''; ?>"
                >
                   <span>👤</span>
                   My Profile
                </a>

            <?php elseif ($currentFolder === "admin"): ?>

                <p class="nav-title">Admin Menu</p>

                <a
                    href="/FixNestPHP/admin/dashboard.php"
                    class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
                >
                    <span>🏠</span>
                    Dashboard
                </a>

                <a
                    href="/FixNestPHP/admin/complaints.php"
                    class="<?php echo $currentPage === 'complaints.php' ? 'active' : ''; ?>"
                >
                    <span>📋</span>
                    Complaints
                </a>

                <a
                    href="/FixNestPHP/admin/reports.php"
                    class="<?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>"
                >
                    <span>📊</span>
                    Reports
                </a>

            <?php elseif ($currentFolder === "warden"): ?>

                <p class="nav-title">Warden Menu</p>

                <a
                    href="/FixNestPHP/warden/dashboard.php"
                    class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
                >
                    <span>🏠</span>
                    Dashboard
                </a>

                <a
                    href="/FixNestPHP/warden/complaints.php"
                    class="<?php echo $currentPage === 'complaints.php' ? 'active' : ''; ?>"
                >
                    <span>📋</span>
                    Complaints
                </a>

                <a
                    href="/FixNestPHP/warden/students.php"
                    class="<?php echo $currentPage === 'students.php' ? 'active' : ''; ?>"
                >
                    <span>👨‍🎓</span>
                    Students
                </a>

                <a
                    href="/FixNestPHP/warden/reports.php"
                    class="<?php echo $currentPage === 'reports.php' ? 'active' : ''; ?>"
                >
                    <span>📊</span>
                    Reports
                </a>

            <?php elseif ($currentFolder === "maintenance"): ?>

                <p class="nav-title">Maintenance Menu</p>

                <a
                    href="/FixNestPHP/maintenance/dashboard.php"
                    class="<?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>"
                >
                    <span>🏠</span>
                    Dashboard
                </a>

            <?php endif; ?>

        </nav>

        <div class="sidebar-bottom">
            <a href="/FixNestPHP/logout.php" class="logout-link">
                <span>🚪</span>
                Logout
            </a>
        </div>
    </aside>

    <main class="main-content">

        <header class="topbar">
            <div>
                <h3>
                    <?php echo isset($pageTitle) ? $pageTitle : "Dashboard"; ?>
                </h3>
                <p>FixNest Hostel Complaint Management System</p>
            </div>

            <div class="user-area">
                <div class="user-avatar">U</div>
                <div>
                    <strong>
                        <?php
                        echo isset($_SESSION['username'])
                            ? htmlspecialchars($_SESSION['username'])
                            : "Demo User";
                        ?>
                    </strong>
                    <small>
                        <?php echo ucfirst($currentFolder); ?>
                    </small>
                </div>
            </div>
        </header>

        <section class="content-area">