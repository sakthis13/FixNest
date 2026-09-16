<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "maintenance") {
    header("Location: ../index.php");
    exit();
}


/*
|--------------------------------------------------------------------------
| Fetch Approved Complaints
|--------------------------------------------------------------------------
| Only complaints approved by the warden are shown to Maintenance.
|--------------------------------------------------------------------------
*/

$sql = "SELECT
            c.complaint_id,
            c.student_id,
            s.student_name,
            c.hostel,
            c.room_number,
            c.category,
            c.description,
            c.severity,
            c.status,
            c.review_decision,
            c.created_at
        FROM complaints c
        LEFT JOIN students s
            ON c.student_id = s.student_id
        WHERE c.review_decision = 'Approved'
        ORDER BY c.created_at DESC";

$stmt = $pdo->prepare($sql);
$stmt->execute();

$complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| Statistics
|--------------------------------------------------------------------------
*/

$total_complaints = count($complaints);

$not_started = 0;
$in_progress = 0;
$resolved = 0;

foreach ($complaints as $complaint) {

    if ($complaint["status"] === "Pending") {
        $not_started++;
    } elseif ($complaint["status"] === "In Progress") {
        $in_progress++;
    } elseif ($complaint["status"] === "Resolved") {
        $resolved++;
    }
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

        /* Navbar */

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

        /* Container */

        .container {
            max-width: 1400px;
            margin: auto;
            padding: 30px;
        }

        /* Header */

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

        /* Stats */

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

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.06);
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

        /* Card */

        .content-card {
            background: white;

            padding: 24px;

            border-radius: 12px;

            box-shadow:
                0 4px 15px rgba(0, 0, 0, 0.06);

            overflow-x: auto;
        }

        .content-card h2 {
            margin: 0 0 20px;

            font-size: 20px;
        }

        /* Table */

        table {
            width: 100%;

            min-width: 950px;

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

        /* Status */

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

        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        /* Priority */

        .priority-high {
            color: #dc2626;
            font-weight: bold;
        }

        .priority-medium {
            color: #d97706;
            font-weight: bold;
        }

        .priority-low {
            color: #16a34a;
            font-weight: bold;
        }

        /* Action */

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

        /* Empty */

        .empty-message {
            text-align: center;

            padding: 40px;

            color: #6b7280;

            font-size: 14px;
        }

        /* Info */

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

        /* Responsive */

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

    <h2>
        FixNest Maintenance
    </h2>

    <a
        class="logout"
        href="../index.php"
    >
        Logout
    </a>

</div>


<div class="container">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <h1>
            Maintenance Dashboard
        </h1>

        <p>
            View approved complaints and manage maintenance work.
        </p>

    </div>


    <!-- STATISTICS -->

    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-label">
                Approved Complaints
            </div>

            <div class="stat-value">
                <?= $total_complaints ?>
            </div>

            <div class="stat-note">
                Complaints received from warden
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Not Started
            </div>

            <div class="stat-value">
                <?= $not_started ?>
            </div>

            <div class="stat-note">
                Work not started
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                In Progress
            </div>

            <div class="stat-value">
                <?= $in_progress ?>
            </div>

            <div class="stat-note">
                Currently being handled
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Resolved
            </div>

            <div class="stat-value">
                <?= $resolved ?>
            </div>

            <div class="stat-note">
                Successfully completed
            </div>

        </div>


    </div>


    <!-- COMPLAINT TABLE -->

    <div class="content-card">

        <h2>
            Approved Complaints
        </h2>


        <?php if (count($complaints) === 0): ?>

            <div class="empty-message">

                No approved complaints are available.

            </div>

        <?php else: ?>


            <table>

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Student
                        </th>

                        <th>
                            Room
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Description
                        </th>

                        <th>
                            Priority
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($complaints as $complaint): ?>


                    <?php

                    $status = $complaint["status"] ?? "Pending";

                    $status_class = "not-started";

                    if ($status === "In Progress") {

                        $status_class = "progress";

                    } elseif ($status === "Resolved") {

                        $status_class = "resolved";

                    } elseif ($status === "Rejected") {

                        $status_class = "rejected";

                    }


                    $severity = $complaint["severity"] ?? "Low";

                    $priority_class = "priority-low";

                    if ($severity === "High") {

                        $priority_class = "priority-high";

                    } elseif ($severity === "Medium") {

                        $priority_class = "priority-medium";

                    }

                    ?>


                    <tr>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["complaint_id"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["student_name"]
                                ?? "Unknown"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["room_number"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["category"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["description"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <span class="<?= $priority_class ?>">

                                <?= htmlspecialchars($severity) ?>

                            </span>

                        </td>


                        <td>

                            <span class="status-badge <?= $status_class ?>">

                                <?= htmlspecialchars($status) ?>

                            </span>

                        </td>


                        <td>

                            <a
                                href="complaint-details.php?complaint_id=<?= urlencode($complaint["complaint_id"]) ?>"
                                class="action-btn"
                            >
                                View / Update
                            </a>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>

            </table>


        <?php endif; ?>


    </div>


    <div class="info-box">

        Only complaints approved by the warden are displayed here.
        The Maintenance Head can review the complaint and manually assign
        the physical maintenance worker.

    </div>


</div>


</body>

</html>