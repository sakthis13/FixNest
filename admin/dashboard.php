<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

/* -------------------------
   GET BASIC COUNTS
-------------------------- */

$student_count = 0;
$warden_count = 0;
$maintenance_count = 0;
$complaint_count = 0;

try {

    $stmt = $pdo->query("SELECT COUNT(*) FROM students");
    $student_count = $stmt->fetchColumn();

    /*
     * Change these table names later if your login
     * tables use different names.
     */
    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'warden'");
    $warden_count = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'maintenance'");
    $maintenance_count = $stmt->fetchColumn();

    $stmt = $pdo->query("SELECT COUNT(*) FROM complaints");
    $complaint_count = $stmt->fetchColumn();

} catch (PDOException $e) {

    /*
     * If your users table has not been created yet,
     * students/complaints counts can still be displayed.
     */
    $warden_count = 0;
    $maintenance_count = 0;
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Admin Dashboard | FixNest</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        /* NAVBAR */

        .navbar {
            height: 65px;
            background: #1e3a8a;
            color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .admin-label {
            font-size: 14px;
            opacity: 0.9;
            margin-right: 15px;
        }

        .logout {
            color: white;
            text-decoration: none;

            background: rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;
        }

        /* MAIN */

        .container {
            max-width: 1150px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 30px;
        }

        .page-header h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .page-header p {
            color: #64748b;
        }

        /* STATS */

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;

            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 23px;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        .stat-title {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 10px;
        }

        .stat-number {
            font-size: 30px;
            font-weight: bold;
            color: #1e3a8a;
        }

        /* MANAGEMENT */

        .card {
            background: white;
            padding: 25px;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.06);

            margin-bottom: 25px;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 20px;
        }

        .management-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 18px;
        }

        .management-card {
            border: 1px solid #e2e8f0;

            padding: 20px;

            border-radius: 10px;

            text-decoration: none;
            color: #1f2937;

            transition: 0.2s;
        }

        .management-card:hover {
            border-color: #2563eb;
            transform: translateY(-2px);
        }

        .management-card h3 {
            margin-bottom: 8px;
            color: #1e3a8a;
        }

        .management-card p {
            color: #64748b;
            font-size: 14px;
            line-height: 1.5;
        }

        .manage-btn {
            display: inline-block;

            margin-top: 15px;

            background: #2563eb;
            color: white;

            padding: 8px 13px;

            border-radius: 6px;

            font-size: 13px;
        }

        /* INFO */

        .info {
            background: #eff6ff;
            border-left: 4px solid #2563eb;

            padding: 18px;

            border-radius: 7px;

            color: #1e40af;

            line-height: 1.6;
        }

        /* RESPONSIVE */

        @media (max-width: 850px) {

            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .management-grid {
                grid-template-columns: 1fr;
            }

        }

        @media (max-width: 550px) {

            .stats {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 0 15px;
            }

            .admin-label {
                display: none;
            }

        }

    </style>

</head>


<body>

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>

    <div>

        <span class="admin-label">
            Administrator
        </span>

        <a href="../index.php" class="logout">
            Logout
        </a>

    </div>

</nav>


<div class="container">


    <!-- HEADER -->

    <div class="page-header">

        <h1>Admin Dashboard</h1>

        <p>
            Manage FixNest users, complaints and system data.
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-title">
                Total Students
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($student_count) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Total Wardens
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($warden_count) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Maintenance Users
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($maintenance_count) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Total Complaints
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($complaint_count) ?>
            </div>

        </div>


    </div>


    <!-- MANAGEMENT -->

    <div class="card">

        <h2>System Management</h2>


        <div class="management-grid">


            <a href="students.php"
               class="management-card">

                <h3>👨‍🎓 Students</h3>

                <p>
                    Add, edit and remove student records
                    and manage student information.
                </p>

                <span class="manage-btn">
                    Manage Students
                </span>

            </a>


            <a href="wardens.php"
               class="management-card">

                <h3>👤 Wardens</h3>

                <p>
                    Manage warden accounts and
                    hostel assignments.
                </p>

                <span class="manage-btn">
                    Manage Wardens
                </span>

            </a>


            <a href="maintenance.php"
               class="management-card">

                <h3>🔧 Maintenance</h3>

                <p>
                    Manage maintenance users and
                    worker information.
                </p>

                <span class="manage-btn">
                    Manage Maintenance
                </span>

         <a href="management.php"
   class="management-card">

    <h3>📊 Management</h3>

    <p>
        Manage management users and
        access to system analysis and
        information.
    </p>

    <span class="manage-btn">
        Manage Management
    </span>

</a>


        </div>

    </div>


    <!-- USER MANAGEMENT -->

    <div class="card">

        <h2>Account Management</h2>

        <div class="management-grid">


            <a href="users.php"
               class="management-card">

                <h3>🔐 User Accounts</h3>

                <p>
                    Manage usernames, passwords,
                    roles and account details.
                </p>

                <span class="manage-btn">
                    Manage Accounts
                </span>

            </a>


            <a href="complaints.php"
               class="management-card">

                <h3>📋 Complaints</h3>

                <p>
                    View and monitor all complaints
                    submitted through FixNest.
                </p>

                <span class="manage-btn">
                    View Complaints
                </span>

            </a>


        </div>

    </div>


    <!-- INFO -->

    <div class="info">

        <strong>Admin responsibility:</strong>

        The administrator manages system data and
        user accounts. Complaint decisions remain with
        the warden, while maintenance work is handled
        by the maintenance team.

    </div>


</div>

</body>

</html>