<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION["username"] ?? "Student";
$hostel_type = $_SESSION["hostel_type"] ?? "Hostel";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - FixNest</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .navbar {
            background: #1d4ed8;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .welcome {
            font-size: 14px;
        }

        .logout {
            background: white;
            color: #1d4ed8;
            padding: 9px 15px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .container {
            max-width: 1150px;
            margin: auto;
            padding: 35px 20px;
        }

        .page-header {
            margin-bottom: 28px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 15px;
        }

        .hostel-badge {
            display: inline-block;
            margin-top: 16px;
            padding: 10px 16px;
            background: #dbeafe;
            color: #1e40af;
            border-radius: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            border-radius: 14px;
            padding: 24px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }

        .stat-card h3 {
            margin: 0 0 12px;
            color: #6b7280;
            font-size: 14px;
            font-weight: normal;
        }

        .stat-card .number {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .section-title {
            margin: 0 0 18px;
            font-size: 21px;
        }

        .quick-actions {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
            margin-bottom: 30px;
        }

        .action-card {
            background: white;
            padding: 25px;
            border-radius: 14px;
            text-decoration: none;
            color: #111827;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
            transition: 0.2s;
        }

        .action-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 7px 22px rgba(0, 0, 0, 0.09);
        }

        .action-icon {
            width: 48px;
            height: 48px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #dbeafe;
            color: #1d4ed8;
            border-radius: 12px;
            font-size: 23px;
            margin-bottom: 16px;
        }

        .action-card h3 {
            margin: 0 0 8px;
            font-size: 17px;
        }

        .action-card p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }

        .notice {
            background: white;
            padding: 24px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.05);
        }

        .notice h2 {
            margin: 0 0 12px;
            font-size: 20px;
        }

        .notice p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.7;
        }

        @media (max-width: 800px) {
            .stats-grid,
            .quick-actions {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 16px 18px;
            }

            .brand {
                font-size: 19px;
            }

            .nav-right {
                gap: 10px;
            }

            .welcome {
                display: none;
            }

            .page-header h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="brand">FixNest</div>

        <div class="nav-right">
            <div class="welcome">
                Welcome, <?php echo htmlspecialchars($username); ?>
            </div>

            <a href="../index.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Student Dashboard</h1>

            <p>
                Manage your hostel maintenance complaints easily.
            </p>

            <div class="hostel-badge">
                Hostel: <?php echo htmlspecialchars($hostel_type); ?>
            </div>
        </div>

        <div class="stats-grid">

            <div class="stat-card">
                <h3>Total Complaints</h3>
                <div class="number">0</div>
            </div>

            <div class="stat-card">
                <h3>Pending Complaints</h3>
                <div class="number">0</div>
            </div>

            <div class="stat-card">
                <h3>Resolved Complaints</h3>
                <div class="number">0</div>
            </div>

        </div>

        <h2 class="section-title">Quick Actions</h2>

        <div class="quick-actions">

            <a href="raise-complaint.php" class="action-card">
                <div class="action-icon">📝</div>

                <h3>Raise Complaint</h3>

                <p>
                    Submit a new maintenance complaint to the hostel team.
                </p>
            </a>

            <a href="my-complaints.php" class="action-card">
                <div class="action-icon">📋</div>

                <h3>My Complaints</h3>

                <p>
                    View your submitted complaints and their status.
                </p>
            </a>

            <a href="profile.php" class="action-card">
                <div class="action-icon">👤</div>

                <h3>My Profile</h3>

                <p>
                    View your student information and account details.
                </p>
            </a>

        </div>

        <div class="notice">
            <h2>Need Maintenance Support?</h2>

            <p>
                If you notice any issue in your room or hostel common area,
                raise a complaint with the correct location and category.
                The concerned team will review and resolve it.
            </p>
        </div>

    </div>

</body>
</html>