<?php

session_start();

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "management"
) {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";


/*
|--------------------------------------------------------------------------
| MANAGEMENT NAME
|--------------------------------------------------------------------------
*/

$management_name =
    $_SESSION["management_name"]
    ?? $_SESSION["username"]
    ?? "Management";


/*
|--------------------------------------------------------------------------
| DEFAULT VALUES
|--------------------------------------------------------------------------
*/

$total_complaints = 0;
$pending_complaints = 0;
$approved_complaints = 0;
$rejected_complaints = 0;
$not_started = 0;
$in_progress = 0;
$resolved = 0;
$recurring_complaints = 0;
$high_severity_open = 0;


/*
|--------------------------------------------------------------------------
| TOTAL COMPLAINTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM complaints
");

$total_complaints = (int) $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| COMPLAINT STATUS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        status,
        COUNT(*) AS total
    FROM complaints
    GROUP BY status
");

$status_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($status_rows as $row) {

    $status = trim((string) ($row["status"] ?? ""));
    $count = (int) $row["total"];

    if ($status === "Pending") {

        $pending_complaints = $count;

    } elseif ($status === "In Progress") {

        $in_progress = $count;

    } elseif ($status === "Resolved") {

        $resolved = $count;
    }
}


/*
|--------------------------------------------------------------------------
| WARDEN REVIEW
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        review_decision,
        COUNT(*) AS total
    FROM complaints
    WHERE review_decision IS NOT NULL
    GROUP BY review_decision
");

$review_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($review_rows as $row) {

    $decision = trim((string) ($row["review_decision"] ?? ""));
    $count = (int) $row["total"];

    if ($decision === "Approved") {

        $approved_complaints = $count;

    } elseif ($decision === "Rejected") {

        $rejected_complaints = $count;
    }
}


/*
|--------------------------------------------------------------------------
| MAINTENANCE STATUS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        maintenance_status,
        COUNT(*) AS total
    FROM complaints
    WHERE review_decision = 'Approved'
    GROUP BY maintenance_status
");

$maintenance_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($maintenance_rows as $row) {

    $maintenance_status =
        trim((string) ($row["maintenance_status"] ?? ""));

    $count = (int) $row["total"];

    if ($maintenance_status === "Not Started") {

        $not_started = $count;

    } elseif ($maintenance_status === "In Progress") {

        $in_progress = $count;

    } elseif ($maintenance_status === "Resolved") {

        $resolved = $count;
    }
}


/*
|--------------------------------------------------------------------------
| RECURRING COMPLAINTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM complaints
    WHERE recurring = TRUE
");

$recurring_complaints = (int) $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| HIGH SEVERITY UNRESOLVED COMPLAINTS
|--------------------------------------------------------------------------
|
| High severity complaints that are not resolved.
|
*/

$stmt = $pdo->query("
    SELECT COUNT(*)
    FROM complaints
    WHERE LOWER(COALESCE(severity, '')) = 'high'
      AND LOWER(COALESCE(status, '')) <> 'resolved'
");

$high_severity_open = (int) $stmt->fetchColumn();


/*
|--------------------------------------------------------------------------
| PREVENTIVE ATTENTION
|--------------------------------------------------------------------------
|
| Show the most repeated complaint patterns.
|
*/

$stmt = $pdo->query("
    SELECT
        complaint_id,
        hostel,
        room_number,
        category,
        severity,
        recurring,
        similar_count,
        maintenance_status,
        created_at
    FROM complaints
    WHERE recurring = TRUE
       OR COALESCE(similar_count, 0) > 0
    ORDER BY
        COALESCE(similar_count, 0) DESC,
        created_at DESC
    LIMIT 5
");

$preventive_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| HIGH PRIORITY COMPLAINTS
|--------------------------------------------------------------------------
|
| High severity unresolved complaints.
|
*/

$stmt = $pdo->query("
    SELECT
        complaint_id,
        hostel,
        room_number,
        category,
        severity,
        status,
        review_decision,
        maintenance_status,
        recurring,
        created_at
    FROM complaints
    WHERE LOWER(COALESCE(severity, '')) = 'high'
      AND LOWER(COALESCE(status, '')) <> 'resolved'
    ORDER BY created_at DESC
    LIMIT 5
");

$priority_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| RECENT COMPLAINTS
|--------------------------------------------------------------------------
*/

$stmt = $pdo->query("
    SELECT
        complaint_id,
        hostel,
        room_number,
        category,
        severity,
        status,
        review_decision,
        recurring,
        created_at
    FROM complaints
    ORDER BY created_at DESC
    LIMIT 8
");

$recent_rows = $stmt->fetchAll(PDO::FETCH_ASSOC);


/*
|--------------------------------------------------------------------------
| HELPER FUNCTIONS
|--------------------------------------------------------------------------
*/

function badgeClass($value)
{
    $value = strtolower(trim((string) $value));

    $value = str_replace(" ", "-", $value);

    return preg_replace(
        '/[^a-z0-9\-]/',
        '',
        $value
    );
}


function isRecurring($value)
{
    return (
        $value === true ||
        $value === "t" ||
        $value === 1 ||
        $value === "1"
    );
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

<title>
    Management Dashboard | FixNest
</title>


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

    line-height: 1.5;
}


/* =========================================================
   NAVBAR
========================================================= */

.navbar {

    min-height: 65px;

    background: #172554;

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


.management-label {

    font-size: 14px;

    opacity: 0.9;
}


.logout {

    color: white;

    text-decoration: none;

    background: rgba(255,255,255,0.15);

    padding: 9px 15px;

    border-radius: 7px;

    transition: 0.2s;
}


.logout:hover {

    background: rgba(255,255,255,0.25);
}


/* =========================================================
   CONTAINER
========================================================= */

.container {

    max-width: 1400px;

    margin: 35px auto;

    padding: 0 20px;
}


/* =========================================================
   HEADER
========================================================= */

.page-header {

    margin-bottom: 30px;
}


.page-header h1 {

    font-size: 30px;

    margin-bottom: 8px;

    color: #172554;
}


.page-header p {

    color: #64748b;

    font-size: 14px;
}


/* =========================================================
   SECTION
========================================================= */

.section {

    margin-bottom: 30px;
}


.section-title {

    font-size: 21px;

    margin-bottom: 16px;

    color: #172554;
}


/* =========================================================
   STATISTICS
========================================================= */

.stats-grid {

    display: grid;

    grid-template-columns:
        repeat(4, minmax(0, 1fr));

    gap: 18px;
}


.stat-card {

    background: white;

    padding: 22px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.06);

    border-top: 4px solid #2563eb;
}


.stat-label {

    color: #64748b;

    font-size: 13px;

    margin-bottom: 8px;
}


.stat-value {

    font-size: 30px;

    font-weight: bold;

    color: #172554;
}


.stat-note {

    margin-top: 7px;

    color: #64748b;

    font-size: 12px;
}


/* =========================================================
   ATTENTION GRID
========================================================= */

.attention-grid {

    display: grid;

    grid-template-columns:
        repeat(3, minmax(0, 1fr));

    gap: 20px;
}


.attention-card {

    background: white;

    padding: 22px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.06);
}


.attention-icon {

    font-size: 28px;

    margin-bottom: 12px;
}


.attention-card h3 {

    font-size: 18px;

    margin-bottom: 7px;

    color: #172554;
}


.attention-card p {

    color: #64748b;

    font-size: 13px;

    margin-bottom: 15px;
}


.attention-number {

    font-size: 27px;

    font-weight: bold;

    color: #1d4ed8;
}


/* =========================================================
   WARNING COLORS
========================================================= */

.warning-card {

    border-left: 5px solid #f97316;
}


.danger-card {

    border-left: 5px solid #dc2626;
}


.info-card {

    border-left: 5px solid #2563eb;
}


/* =========================================================
   PREVENTIVE BOX
========================================================= */

.preventive-box {

    background: #fff7ed;

    border-left: 5px solid #f97316;

    padding: 22px;

    border-radius: 10px;

    margin-bottom: 18px;
}


.preventive-box h3 {

    color: #9a3412;

    margin-bottom: 8px;
}


.preventive-box p {

    color: #7c2d12;

    font-size: 14px;

    line-height: 1.6;
}


/* =========================================================
   TABLE CARD
========================================================= */

.table-card {

    background: white;

    padding: 24px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.06);

    overflow-x: auto;
}


.table-header {

    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 15px;

    margin-bottom: 20px;
}


.table-header h2 {

    font-size: 20px;

    color: #172554;
}


/* =========================================================
   ANALYSIS BUTTON
========================================================= */

.analysis-button {

    display: inline-block;

    padding: 10px 16px;

    background: #2563eb;

    color: white;

    text-decoration: none;

    border-radius: 8px;

    font-size: 13px;

    font-weight: bold;

    transition: 0.2s;
}


.analysis-button:hover {

    background: #1d4ed8;

    transform: translateY(-1px);
}


/* =========================================================
   TABLE
========================================================= */

table {

    width: 100%;

    min-width: 900px;

    border-collapse: collapse;
}


th,
td {

    padding: 13px;

    text-align: left;

    border-bottom:
        1px solid #e5e7eb;

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

    background: #f8fafc;
}


/* =========================================================
   BADGES
========================================================= */

.badge {

    display: inline-block;

    padding: 5px 9px;

    border-radius: 15px;

    font-size: 11px;

    font-weight: bold;
}


.pending {

    background: #fef3c7;

    color: #92400e;
}


.approved {

    background: #dcfce7;

    color: #166534;
}


.rejected {

    background: #fee2e2;

    color: #991b1b;
}


.in-progress {

    background: #dbeafe;

    color: #1d4ed8;
}


.resolved {

    background: #dcfce7;

    color: #166534;
}


.not-started {

    background: #fef3c7;

    color: #92400e;
}


.high {

    background: #fee2e2;

    color: #b91c1c;
}


.medium {

    background: #fef3c7;

    color: #92400e;
}


.low {

    background: #dcfce7;

    color: #166534;
}


.recurring {

    background: #ffedd5;

    color: #9a3412;
}


/* =========================================================
   EMPTY
========================================================= */

.empty {

    padding: 30px;

    text-align: center;

    color: #64748b;

    font-size: 14px;
}


/* =========================================================
   INFO
========================================================= */

.info-box {

    background: #eff6ff;

    border-left: 4px solid #2563eb;

    padding: 17px;

    border-radius: 7px;

    color: #1e40af;

    font-size: 13px;

    line-height: 1.6;
}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 1050px) {

    .stats-grid {

        grid-template-columns:
            repeat(2, minmax(0, 1fr));

    }

    .attention-grid {

        grid-template-columns: 1fr;

    }

}


@media (max-width: 600px) {

    .navbar {

        padding: 0 15px;

    }

    .management-label {

        display: none;

    }

    .container {

        padding: 0 15px;

    }

    .stats-grid {

        grid-template-columns: 1fr;

    }

    .page-header h1 {

        font-size: 25px;

    }

    .table-header {

        align-items: flex-start;

        flex-direction: column;

    }

}

</style>

</head>


<body>


<!-- =========================================================
     NAVBAR
========================================================= -->

<nav class="navbar">

    <div class="logo">
        FixNest Management
    </div>


    <div class="nav-right">

        <span class="management-label">

            <?= htmlspecialchars($management_name) ?>

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


<!-- =========================================================
     HEADER
========================================================= -->

<div class="page-header">

    <h1>
        Management Dashboard
    </h1>

    <p>
        Executive overview of hostel complaints,
        maintenance progress and issues requiring attention.
    </p>

</div>



<!-- =========================================================
     EXECUTIVE STATISTICS
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Complaint Overview
    </h2>


    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-label">
                Total Complaints
            </div>

            <div class="stat-value">
                <?= $total_complaints ?>
            </div>

            <div class="stat-note">
                All recorded complaints
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Pending
            </div>

            <div class="stat-value">
                <?= $pending_complaints ?>
            </div>

            <div class="stat-note">
                Awaiting review or action
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
                Maintenance currently active
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
                Maintenance completed
            </div>

        </div>


    </div>

</div>



<!-- =========================================================
     MANAGEMENT ATTENTION
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Attention Required
    </h2>


    <div class="attention-grid">


        <!-- HIGH SEVERITY -->

        <div class="attention-card danger-card">

            <div class="attention-icon">
                🚨
            </div>

            <h3>
                High Severity Unresolved
            </h3>

            <p>
                High-severity complaints that have not
                yet reached a resolved status.
            </p>

            <div class="attention-number">
                <?= $high_severity_open ?>
            </div>

        </div>



        <!-- NOT STARTED -->

        <div class="attention-card warning-card">

            <div class="attention-icon">
                ⏳
            </div>

            <h3>
                Approved but Not Started
            </h3>

            <p>
                Complaints approved by the warden but
                maintenance has not started yet.
            </p>

            <div class="attention-number">
                <?= $not_started ?>
            </div>

        </div>



        <!-- RECURRING -->

        <div class="attention-card info-card">

            <div class="attention-icon">
                🔁
            </div>

            <h3>
                Recurring Complaints
            </h3>

            <p>
                Repeated issues that may require
                preventive inspection or corrective action.
            </p>

            <div class="attention-number">
                <?= $recurring_complaints ?>
            </div>

        </div>


    </div>

</div>



<!-- =========================================================
     REVIEW SUMMARY
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Review & Maintenance Summary
    </h2>


    <div class="stats-grid">


        <div class="stat-card">

            <div class="stat-label">
                Warden Approved
            </div>

            <div class="stat-value">
                <?= $approved_complaints ?>
            </div>

            <div class="stat-note">
                Complaints approved for maintenance
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Warden Rejected
            </div>

            <div class="stat-value">
                <?= $rejected_complaints ?>
            </div>

            <div class="stat-note">
                Complaints rejected during review
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-label">
                Maintenance In Progress
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
                Maintenance Resolved
            </div>

            <div class="stat-value">
                <?= $resolved ?>
            </div>

            <div class="stat-note">
                Completed maintenance work
            </div>

        </div>


    </div>

</div>



<!-- =========================================================
     PREVENTIVE ATTENTION
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Preventive Attention
    </h2>


    <div class="preventive-box">

        <h3>
            🔧 Repeated Issues
        </h3>

        <p>
            Repeated or recurring complaints may indicate
            an underlying maintenance problem. Management
            can review these patterns and consider preventive
            inspection, repair or other corrective action
            before the same issue continues to occur.
        </p>

    </div>


    <div class="table-card">

        <div class="table-header">

            <h2>
                Top Repeated Issues
            </h2>


            <a
                href="complaints-analysis.php"
                class="analysis-button"
            >
                📊 View Full Analysis
            </a>

        </div>


        <?php if (empty($preventive_rows)): ?>

            <div class="empty">

                No recurring or repeated complaints
                have been recorded.

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Hostel
                        </th>

                        <th>
                            Room
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Severity
                        </th>

                        <th>
                            Similar Count
                        </th>

                        <th>
                            Maintenance
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($preventive_rows as $row): ?>

                    <tr>

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row["complaint_id"]
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["hostel"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["room_number"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["category"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?php

                            $severity =
                                strtolower(
                                    trim(
                                        (string)
                                        ($row["severity"] ?? "")
                                    )
                                );

                            ?>

                            <span
                                class="badge <?= htmlspecialchars(
                                    badgeClass($severity)
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $row["severity"] ?? "-"
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <strong>

                                <?= (int)(
                                    $row["similar_count"] ?? 0
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?php

                            $maintenance_status =
                                $row["maintenance_status"]
                                ?? "Not Started";

                            ?>

                            <span
                                class="badge <?= htmlspecialchars(
                                    badgeClass(
                                        $maintenance_status
                                    )
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $maintenance_status
                                ) ?>

                            </span>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</div>



<!-- =========================================================
     HIGH PRIORITY COMPLAINTS
========================================================= -->

<div class="section">

    <div class="table-card">

        <div class="table-header">

            <h2>
                High Priority Attention
            </h2>

        </div>


        <?php if (empty($priority_rows)): ?>

            <div class="empty">

                No unresolved high-severity complaints
                require attention.

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Hostel
                        </th>

                        <th>
                            Room
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Severity
                        </th>

                        <th>
                            Review
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Created
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($priority_rows as $row): ?>

                    <tr>

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row["complaint_id"]
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["hostel"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["room_number"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["category"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <span class="badge high">
                                High
                            </span>

                        </td>


                        <td>

                            <?php if (
                                !empty(
                                    $row["review_decision"]
                                )
                            ): ?>

                                <span
                                    class="badge <?= htmlspecialchars(
                                        badgeClass(
                                            $row[
                                                "review_decision"
                                            ]
                                        )
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        $row[
                                            "review_decision"
                                        ]
                                    ) ?>

                                </span>

                            <?php else: ?>

                                -

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php

                            $status =
                                $row["status"]
                                ?? "-";

                            ?>

                            <span
                                class="badge <?= htmlspecialchars(
                                    badgeClass($status)
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $status
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["created_at"] ?? "-"
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</div>



<!-- =========================================================
     RECENT COMPLAINTS
========================================================= -->

<div class="section">

    <div class="table-card">

        <div class="table-header">

            <h2>
                Recent Complaints
            </h2>


            <a
                href="complaints-analysis.php"
                class="analysis-button"
            >
                📊 Complaint Analysis
            </a>

        </div>


        <?php if (empty($recent_rows)): ?>

            <div class="empty">

                No complaints have been recorded.

            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Complaint ID
                        </th>

                        <th>
                            Hostel
                        </th>

                        <th>
                            Room
                        </th>

                        <th>
                            Category
                        </th>

                        <th>
                            Severity
                        </th>

                        <th>
                            Review
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Recurring
                        </th>

                        <th>
                            Created
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($recent_rows as $row): ?>

                    <tr>

                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $row["complaint_id"]
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["hostel"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["room_number"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["category"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?php

                            $severity =
                                $row["severity"] ?? "";

                            ?>

                            <span
                                class="badge <?= htmlspecialchars(
                                    badgeClass($severity)
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $severity ?: "-"
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?php if (
                                !empty(
                                    $row["review_decision"]
                                )
                            ): ?>

                                <span
                                    class="badge <?= htmlspecialchars(
                                        badgeClass(
                                            $row[
                                                "review_decision"
                                            ]
                                        )
                                    ) ?>"
                                >

                                    <?= htmlspecialchars(
                                        $row[
                                            "review_decision"
                                        ]
                                    ) ?>

                                </span>

                            <?php else: ?>

                                -

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php

                            $status =
                                $row["status"] ?? "";

                            ?>

                            <span
                                class="badge <?= htmlspecialchars(
                                    badgeClass($status)
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $status ?: "-"
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?php if (
                                isRecurring(
                                    $row["recurring"] ?? false
                                )
                            ): ?>

                                <span class="badge recurring">
                                    Yes
                                </span>

                            <?php else: ?>

                                No

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $row["created_at"] ?? "-"
                            ) ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</div>



<!-- =========================================================
     MANAGEMENT INFORMATION
========================================================= -->

<div class="info-box">

    <strong>Management Information:</strong>

    This dashboard provides a high-level operational view
    of complaint activity. Recurring complaints and unresolved
    high-severity issues are highlighted for management attention.
    Use the Complaint Analysis page for detailed trends,
    category patterns, hostel comparisons, severity analysis
    and other complaint statistics.

</div>


</div>

</body>

</html>