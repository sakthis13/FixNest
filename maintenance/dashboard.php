<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "maintenance") {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maintenance Dashboard - FixNest</title>

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

        .navbar h2 {
            margin: 0;
            font-size: 22px;
        }

        .logout {
            background: white;
            color: #1d4ed8;
            padding: 9px 14px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            padding: 30px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .stat-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .stat-note {
            margin-top: 8px;
            color: #2563eb;
            font-size: 12px;
        }

        .content-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        .content-card h2 {
            margin: 0 0 20px;
            font-size: 20px;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        th {
            background: #dbeafe;
            color: #1e40af;
            white-space: nowrap;
        }

        td {
            color: #374151;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .not-started {
            background: #fef3c7;
            color: #92400e;
        }

        .progress {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .resolved {
            background: #dcfce7;
            color: #166534;
        }

        .priority-high {
            color: #dc2626;
            font-weight: bold;
        }

        .priority-medium {
            color: #d97706;
            font-weight: bold;
        }

        .action-btn {
            display: inline-block;
            border: none;
            border-radius: 6px;
            padding: 8px 12px;
            background: #1d4ed8;
            color: white;
            cursor: pointer;
            font-weight: bold;
            text-decoration: none;
            font-size: 13px;
        }

        .action-btn:hover {
            background: #1e40af;
        }

        .info-box {
            margin-top: 24px;
            padding: 16px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }

            .navbar {
                padding: 16px 18px;
            }

            .navbar h2 {
                font-size: 18px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h2>FixNest Maintenance</h2>
        <a class="logout" href="../index.php">Logout</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Maintenance Dashboard</h1>
            <p>View maintenance complaints and update work progress.</p>
        </div>

        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-label">Assigned Complaints</div>
                <div class="stat-value">8</div>
                <div class="stat-note">Total assigned to you</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Not Started</div>
                <div class="stat-value">3</div>
                <div class="stat-note">Work not started yet</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">In Progress</div>
                <div class="stat-value">3</div>
                <div class="stat-note">Currently working</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value">2</div>
                <div class="stat-note">Successfully resolved</div>
            </div>

        </div>

        <div class="content-card">

            <h2>Assigned Complaints</h2>

            <table>
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Student</th>
                        <th>Room</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>FX-1024</td>
                        <td>Arun Kumar</td>
                        <td>B-204</td>
                        <td>Plumbing</td>
                        <td>Water leakage in bathroom</td>
                        <td>
                            <span class="priority-high">High</span>
                        </td>
                        <td>
                            <span class="status-badge not-started">Not Started</span>
                        </td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1024"
                                class="action-btn"
                            >
                                Update
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>FX-1025</td>
                        <td>Priya S</td>
                        <td>A-112</td>
                        <td>Electrical</td>
                        <td>Tube light not working</td>
                        <td>
                            <span class="priority-medium">Medium</span>
                        </td>
                        <td>
                            <span class="status-badge progress">In Progress</span>
                        </td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1025"
                                class="action-btn"
                            >
                                Update
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>FX-1026</td>
                        <td>Vignesh R</td>
                        <td>C-306</td>
                        <td>Carpentry</td>
                        <td>Broken cupboard door</td>
                        <td>
                            <span class="priority-medium">Medium</span>
                        </td>
                        <td>
                            <span class="status-badge resolved">Resolved</span>
                        </td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1026"
                                class="action-btn"
                            >
                                Update
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>

        </div>

        <div class="info-box">
            This page currently uses sample complaint data.
            Real assignments and status updates will be connected to the backend API later.
        </div>

    </div>

</body>
</html>