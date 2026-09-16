<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";


/*
|--------------------------------------------------------------------------
| FETCH ALL COMPLAINTS
|--------------------------------------------------------------------------
*/

try {

    $sql = "
        SELECT
            c.complaint_id,
            c.student_id,
            s.student_name,

            c.hostel,
            c.location_type,
            c.room_number,
            c.floor_number,

            c.category,
            c.description,
            c.severity,
            c.severity_confidence,

            c.recurring,
            c.similar_count,

            c.status,
            c.review_decision,
            c.warden_remarks,

            c.worker_name,
            c.assigned_worker,
            c.maintenance_status,
            c.maintenance_remarks,
            c.maintenance_updated_at,

            c.created_at,
            c.updated_at

        FROM complaints c

        LEFT JOIN students s
            ON c.student_id = s.student_id

        ORDER BY c.created_at DESC
    ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    die(
        "Database Error: "
        . htmlspecialchars($e->getMessage())
    );

}


/*
|--------------------------------------------------------------------------
| STATISTICS
|--------------------------------------------------------------------------
*/

$total_complaints = count($complaints);

$pending = 0;
$approved = 0;
$rejected = 0;
$resolved = 0;


foreach ($complaints as $complaint) {

    $decision = $complaint["review_decision"] ?? "";
    $status = $complaint["status"] ?? "";

    if ($decision === "Approved") {
        $approved++;
    }

    if ($decision === "Rejected") {
        $rejected++;
    }

    if (
        $status === "Pending" ||
        $status === "Not Started"
    ) {
        $pending++;
    }

    if ($status === "Resolved") {
        $resolved++;
    }

}

?>


<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Complaints | FixNest Admin</title>


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


        /* =========================
           NAVBAR
        ========================== */

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


        .nav-right {

            display: flex;

            align-items: center;

            gap: 15px;
        }


        .admin-label {

            font-size: 14px;

            opacity: 0.9;
        }


        .logout {

            color: white;

            text-decoration: none;

            background: rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;
        }


        .logout:hover {

            background: rgba(255,255,255,0.25);
        }


        /* =========================
           CONTAINER
        ========================== */

        .container {

            max-width: 1400px;

            margin: 35px auto;

            padding: 0 20px;
        }


        /* =========================
           BACK
        ========================== */

        .back {

            display: inline-block;

            margin-bottom: 20px;

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;
        }


        .back:hover {

            text-decoration: underline;
        }


        /* =========================
           HEADER
        ========================== */

        .page-header {

            margin-bottom: 30px;
        }


        .page-header h1 {

            font-size: 30px;

            margin-bottom: 8px;
        }


        .page-header p {

            color: #64748b;

            font-size: 14px;
        }


        /* =========================
           STATISTICS
        ========================== */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(4, minmax(0, 1fr));

            gap: 18px;

            margin-bottom: 25px;
        }


        .stat-card {

            background: white;

            padding: 22px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        .stat-title {

            color: #64748b;

            font-size: 13px;

            margin-bottom: 10px;
        }


        .stat-number {

            font-size: 28px;

            font-weight: bold;

            color: #1e3a8a;
        }


        /* =========================
           CARD
        ========================== */

        .card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);

            overflow-x: auto;
        }


        .card h2 {

            font-size: 20px;

            margin-bottom: 20px;
        }


        /* =========================
           TABLE
        ========================== */

        table {

            width: 100%;

            min-width: 1150px;

            border-collapse: collapse;
        }


        th,
        td {

            padding: 13px;

            text-align: left;

            border-bottom: 1px solid #e5e7eb;

            font-size: 13px;

            vertical-align: middle;
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

            background: #f8fafc;
        }


        /* =========================
           BADGES
        ========================== */

        .badge {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: bold;

            white-space: nowrap;
        }


        /* Severity */

        .severity-high {

            background: #fee2e2;

            color: #b91c1c;
        }


        .severity-medium {

            background: #fef3c7;

            color: #92400e;
        }


        .severity-low {

            background: #dcfce7;

            color: #166534;
        }


        /* Decision */

        .approved {

            background: #dcfce7;

            color: #166534;
        }


        .rejected {

            background: #fee2e2;

            color: #991b1b;
        }


        .pending {

            background: #fef3c7;

            color: #92400e;
        }


        /* Status */

        .status-progress {

            background: #dbeafe;

            color: #1d4ed8;
        }


        .status-resolved {

            background: #dcfce7;

            color: #166534;
        }


        .status-pending {

            background: #fef3c7;

            color: #92400e;
        }


        .status-other {

            background: #e5e7eb;

            color: #374151;
        }


        /* =========================
           ACTION BUTTON
        ========================== */

        .view-btn {

            display: inline-block;

            padding: 8px 12px;

            background: #2563eb;

            color: white;

            text-decoration: none;

            border-radius: 6px;

            font-size: 12px;

            font-weight: bold;

            white-space: nowrap;
        }


        .view-btn:hover {

            background: #1d4ed8;
        }


        /* =========================
           EMPTY
        ========================== */

        .empty {

            text-align: center;

            padding: 45px;

            color: #64748b;

            font-size: 14px;
        }


        /* =========================
           INFO
        ========================== */

        .info {

            margin-top: 20px;

            padding: 16px;

            background: #eff6ff;

            border-left: 4px solid #2563eb;

            border-radius: 7px;

            color: #1e40af;

            font-size: 13px;

            line-height: 1.6;
        }


        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 900px) {

            .stats {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }

        }


        @media (max-width: 600px) {

            .navbar {

                padding: 0 15px;
            }


            .admin-label {

                display: none;
            }


            .container {

                padding: 0 15px;
            }


            .stats {

                grid-template-columns: 1fr;
            }


            .page-header h1 {

                font-size: 25px;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================== -->

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>


    <div class="nav-right">

        <span class="admin-label">
            Administrator
        </span>


        <a
            href="../index.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</nav>



<div class="container">


    <!-- BACK -->

    <a
        href="dashboard.php"
        class="back"
    >
        ← Back to Dashboard
    </a>



    <!-- HEADER -->

    <div class="page-header">

        <h1>
            Complaints
        </h1>

        <p>
            View and monitor complaints submitted through FixNest.
        </p>

    </div>



    <!-- =========================
         STATISTICS
    ========================== -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-title">
                Total Complaints
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($total_complaints) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Approved
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($approved) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Pending
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($pending) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Resolved
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($resolved) ?>
            </div>

        </div>


    </div>



    <!-- =========================
         COMPLAINT TABLE
    ========================== -->

    <div class="card">


        <h2>
            All Complaints
        </h2>


        <?php if (empty($complaints)): ?>


            <div class="empty">

                No complaints have been submitted yet.

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
                            Hostel
                        </th>

                        <th>
                            Location
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Severity
                        </th>

                        <th>
                            Warden Decision
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($complaints as $complaint): ?>


                    <?php

                    /* Severity */

                    $severity =
                        $complaint["severity"]
                        ?? "Medium";

                    $severity_class =
                        "severity-medium";

                    if ($severity === "High") {

                        $severity_class =
                            "severity-high";

                    } elseif ($severity === "Low") {

                        $severity_class =
                            "severity-low";

                    }


                    /* Decision */

                    $decision =
                        $complaint["review_decision"]
                        ?? "Pending";

                    $decision_class =
                        "pending";

                    if ($decision === "Approved") {

                        $decision_class =
                            "approved";

                    } elseif ($decision === "Rejected") {

                        $decision_class =
                            "rejected";

                    }


                    /* Status */

                    $status =
                        $complaint["status"]
                        ?? "Pending";

                    $status_class =
                        "status-other";

                    if (
                        $status === "Pending" ||
                        $status === "Not Started"
                    ) {

                        $status_class =
                            "status-pending";

                    } elseif ($status === "In Progress") {

                        $status_class =
                            "status-progress";

                    } elseif ($status === "Resolved") {

                        $status_class =
                            "status-resolved";

                    }


                    /* Location */

                    $location_parts = [];


                    if (!empty($complaint["room_number"])) {

                        $location_parts[] =
                            "Room " .
                            $complaint["room_number"];

                    }


                    if (!empty($complaint["floor_number"])) {

                        $location_parts[] =
                            "Floor " .
                            $complaint["floor_number"];

                    }


                    if (!empty($complaint["location_type"])) {

                        $location_parts[] =
                            $complaint["location_type"];

                    }


                    $location =
                        !empty($location_parts)
                        ? implode(" • ", $location_parts)
                        : "-";

                    ?>


                    <tr>


                        <!-- ID -->

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $complaint["complaint_id"]
                                ) ?>

                            </strong>

                        </td>


                        <!-- STUDENT -->

                        <td>

                            <?= htmlspecialchars(
                                $complaint["student_name"]
                                ?? "Unknown"
                            ) ?>

                            <br>

                            <small style="color:#64748b;">

                                ID:
                                <?= htmlspecialchars(
                                    $complaint["student_id"]
                                ) ?>

                            </small>

                        </td>


                        <!-- HOSTEL -->

                        <td>

                            <?= htmlspecialchars(
                                $complaint["hostel"]
                                ?? "-"
                            ) ?>

                        </td>


                        <!-- LOCATION -->

                        <td>

                            <?= htmlspecialchars(
                                $location
                            ) ?>

                        </td>


                        <!-- CATEGORY -->

                        <td>

                            <?= htmlspecialchars(
                                $complaint["category"]
                                ?? "-"
                            ) ?>

                        </td>


                        <!-- SEVERITY -->

                        <td>

                            <span
                                class="badge
                                <?= htmlspecialchars(
                                    $severity_class
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $severity
                                ) ?>

                            </span>

                        </td>


                        <!-- DECISION -->

                        <td>

                            <span
                                class="badge
                                <?= htmlspecialchars(
                                    $decision_class
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $decision
                                ) ?>

                            </span>

                        </td>


                        <!-- STATUS -->

                        <td>

                            <span
                                class="badge
                                <?= htmlspecialchars(
                                    $status_class
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $status
                                ) ?>

                            </span>

                        </td>


                        <!-- CREATED -->

                        <td>

                            <?= htmlspecialchars(
                                $complaint["created_at"]
                                ?? "-"
                            ) ?>

                        </td>


                        <!-- ACTION -->

                        <td>

                            <a
                                href="complaint-details.php?complaint_id=<?= urlencode(
                                    $complaint["complaint_id"]
                                ) ?>"
                                class="view-btn"
                            >
                                View Details
                            </a>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        <?php endif; ?>


    </div>



    <!-- =========================
         INFO
    ========================== -->

    <div class="info">

        <strong>Admin monitoring:</strong>

        The administrator can view complaint information
        and monitor its progress. Warden decisions and
        maintenance updates are handled by their respective
        roles.

    </div>


</div>


</body>

</html>