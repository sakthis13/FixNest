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
| MANAGEMENT COMPLAINT ANALYSIS
|--------------------------------------------------------------------------
|
| This page is intentionally different from the Management Dashboard.
|
| Dashboard:
|   - General overview
|   - Basic complaint counts
|   - Recent complaints
|
| Analysis:
|   - Hostel patterns
|   - Category patterns
|   - Severity distribution
|   - Recurring complaint patterns
|   - Preventive maintenance focus
|
*/


try {

    /*
    |--------------------------------------------------------------------------
    | HOSTEL ANALYSIS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            hostel,
            COUNT(*) AS total,
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) AS recurring,
            COUNT(*) FILTER (
                WHERE severity = 'High'
            ) AS high_severity,
            COUNT(*) FILTER (
                WHERE status = 'Resolved'
            ) AS resolved,
            COUNT(*) FILTER (
                WHERE status = 'Pending'
            ) AS pending,
            COUNT(*) FILTER (
                WHERE status = 'In Progress'
            ) AS in_progress
        FROM complaints
        GROUP BY hostel
        ORDER BY total DESC
    ");

    $hostel_analysis =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | CATEGORY ANALYSIS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            category,
            COUNT(*) AS total,
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) AS recurring,
            COUNT(*) FILTER (
                WHERE severity = 'High'
            ) AS high_severity,
            COUNT(*) FILTER (
                WHERE status = 'Resolved'
            ) AS resolved,
            COUNT(*) FILTER (
                WHERE status = 'Pending'
            ) AS pending,
            COUNT(*) FILTER (
                WHERE status = 'In Progress'
            ) AS in_progress
        FROM complaints
        GROUP BY category
        ORDER BY total DESC
    ");

    $category_analysis =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | SEVERITY ANALYSIS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            severity,
            COUNT(*) AS total
        FROM complaints
        GROUP BY severity
        ORDER BY
            CASE severity
                WHEN 'High' THEN 1
                WHEN 'Medium' THEN 2
                WHEN 'Low' THEN 3
                ELSE 4
            END
    ");

    $severity_analysis =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | STATUS BY CATEGORY
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            category,
            COUNT(*) FILTER (
                WHERE status = 'Pending'
            ) AS pending,
            COUNT(*) FILTER (
                WHERE status = 'In Progress'
            ) AS in_progress,
            COUNT(*) FILTER (
                WHERE status = 'Resolved'
            ) AS resolved
        FROM complaints
        GROUP BY category
        ORDER BY category
    ");

    $category_status =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | RECURRING COMPLAINTS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            complaint_id,
            hostel,
            category,
            description,
            severity,
            similar_count,
            room_number,
            status,
            maintenance_status,
            created_at
        FROM complaints
        WHERE recurring = TRUE
           OR COALESCE(similar_count, 0) > 0
        ORDER BY
            similar_count DESC,
            created_at DESC
    ");

    $recurring_list =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | TOP RECURRING CATEGORIES
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            category,
            COUNT(*) AS total,
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) AS recurring,
            COALESCE(
                SUM(
                    CASE
                        WHEN similar_count IS NOT NULL
                        THEN similar_count
                        ELSE 0
                    END
                ),
                0
            ) AS similar_total
        FROM complaints
        GROUP BY category
        HAVING
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) > 0
        ORDER BY recurring DESC, total DESC
    ");

    $recurring_categories =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


    /*
    |--------------------------------------------------------------------------
    | TOP RECURRING HOSTELS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            hostel,
            COUNT(*) AS total,
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) AS recurring
        FROM complaints
        GROUP BY hostel
        HAVING
            COUNT(*) FILTER (
                WHERE recurring = TRUE
            ) > 0
        ORDER BY recurring DESC, total DESC
    ");

    $recurring_hostels =
        $stmt->fetchAll(PDO::FETCH_ASSOC);


} catch (PDOException $e) {

    die(
        "Database Error: " .
        htmlspecialchars($e->getMessage())
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
    Complaint Analysis | FixNest Management
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
   BACK LINK
========================================================= */

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


/* =========================================================
   HEADER
========================================================= */

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

    line-height: 1.7;

}


/* =========================================================
   SECTION
========================================================= */

.section {

    margin-bottom: 28px;

}


.section-title {

    font-size: 21px;

    margin-bottom: 8px;

}


.section-description {

    color: #64748b;

    font-size: 13px;

    line-height: 1.6;

    margin-bottom: 18px;

}


/* =========================================================
   ANALYSIS GRID
========================================================= */

.analysis-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 20px;

}


.analysis-card {

    background: white;

    padding: 24px;

    border-radius: 12px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.06);

}


.analysis-card h3 {

    font-size: 18px;

    margin-bottom: 18px;

}


.analysis-item {

    display: flex;

    align-items: center;

    justify-content: space-between;

    padding: 13px 0;

    border-bottom:
        1px solid #e5e7eb;

}


.analysis-item:last-child {

    border-bottom: none;

}


.analysis-name {

    font-size: 14px;

}


.analysis-count {

    background: #dbeafe;

    color: #1d4ed8;

    padding: 5px 10px;

    border-radius: 15px;

    font-size: 12px;

    font-weight: bold;

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


.table-card h2 {

    font-size: 20px;

    margin-bottom: 8px;

}


.table-description {

    color: #64748b;

    font-size: 13px;

    line-height: 1.6;

    margin-bottom: 20px;

}


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


.badge-high {

    background: #fee2e2;

    color: #b91c1c;

}


.badge-medium {

    background: #fef3c7;

    color: #92400e;

}


.badge-low {

    background: #dcfce7;

    color: #166534;

}


.badge-recurring {

    background: #ede9fe;

    color: #6d28d9;

}


.badge-pending {

    background: #fef3c7;

    color: #92400e;

}


.badge-progress {

    background: #dbeafe;

    color: #1d4ed8;

}


.badge-resolved {

    background: #dcfce7;

    color: #166534;

}


.badge-neutral {

    background: #f1f5f9;

    color: #475569;

}


/* =========================================================
   INSIGHT
========================================================= */

.insight-grid {

    display: grid;

    grid-template-columns:
        repeat(2, minmax(0, 1fr));

    gap: 18px;

}


.insight-box {

    background: #eff6ff;

    border-left:
        5px solid #2563eb;

    padding: 18px;

    border-radius: 9px;

}


.insight-box.warning {

    background: #fff7ed;

    border-left-color: #f97316;

}


.insight-box.danger {

    background: #fef2f2;

    border-left-color: #dc2626;

}


.insight-title {

    font-weight: bold;

    margin-bottom: 8px;

}


.insight-text {

    color: #475569;

    font-size: 13px;

    line-height: 1.7;

}


/* =========================================================
   DESCRIPTION
========================================================= */

.description-cell {

    max-width: 320px;

    line-height: 1.5;

    color: #475569;

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
   INFO BOX
========================================================= */

.info-box {

    background: #eff6ff;

    border-left:
        4px solid #2563eb;

    padding: 16px;

    border-radius: 7px;

    color: #1e40af;

    font-size: 13px;

    line-height: 1.7;

}


/* =========================================================
   RESPONSIVE
========================================================= */

@media (max-width: 950px) {

    .analysis-grid {

        grid-template-columns: 1fr;

    }

    .insight-grid {

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

    .page-header h1 {

        font-size: 25px;

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
            <?= htmlspecialchars(
                $_SESSION["management_name"]
                ?? $_SESSION["username"]
                ?? "Management"
            ) ?>
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
     BACK
========================================================= -->

<a
    href="dashboard.php"
    class="back"
>
    ← Back to Management Dashboard
</a>



<!-- =========================================================
     HEADER
========================================================= -->

<div class="page-header">

    <h1>
        Complaint Analysis
    </h1>

    <p>
        Detailed analysis of complaint patterns across
        hostels, categories, severity levels and recurring
        maintenance issues.
    </p>

</div>



<!-- =========================================================
     PATTERN OVERVIEW
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Complaint Patterns
    </h2>

    <p class="section-description">
        This section identifies where complaints are
        concentrated and which categories or locations
        contain repeated issues.
    </p>


    <div class="analysis-grid">


        <!-- HOSTEL PATTERN -->

        <div class="analysis-card">

            <h3>
                Complaint Distribution by Hostel
            </h3>


            <?php if (empty($hostel_analysis)): ?>

                <div class="empty">
                    No hostel data available.
                </div>

            <?php else: ?>

                <?php foreach ($hostel_analysis as $hostel): ?>

                    <div class="analysis-item">

                        <span class="analysis-name">

                            <?= htmlspecialchars(
                                $hostel["hostel"] ?? "Unknown"
                            ) ?>

                        </span>


                        <span class="analysis-count">

                            <?= (int)$hostel["total"] ?>

                            complaints

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>



        <!-- CATEGORY PATTERN -->

        <div class="analysis-card">

            <h3>
                Complaint Distribution by Category
            </h3>


            <?php if (empty($category_analysis)): ?>

                <div class="empty">
                    No category data available.
                </div>

            <?php else: ?>

                <?php foreach ($category_analysis as $category): ?>

                    <div class="analysis-item">

                        <span class="analysis-name">

                            <?= htmlspecialchars(
                                $category["category"]
                                ?? "Unknown"
                            ) ?>

                        </span>


                        <span class="analysis-count">

                            <?= (int)$category["total"] ?>

                            complaints

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


    </div>

</div>



<!-- =========================================================
     HOSTEL DETAILED ANALYSIS
========================================================= -->

<div class="section">

    <div class="table-card">

        <h2>
            Hostel-wise Complaint Analysis
        </h2>

        <p class="table-description">
            Comparison of complaint volume, recurring issues,
            high-severity complaints and resolution activity
            across hostels.
        </p>


        <?php if (empty($hostel_analysis)): ?>

            <div class="empty">
                No hostel analysis available.
            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Hostel
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Recurring
                        </th>

                        <th>
                            High Severity
                        </th>

                        <th>
                            Pending
                        </th>

                        <th>
                            In Progress
                        </th>

                        <th>
                            Resolved
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($hostel_analysis as $hostel): ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $hostel["hostel"]
                                    ?? "Unknown"
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= (int)$hostel["total"] ?>

                        </td>


                        <td>

                            <?php if (
                                (int)$hostel["recurring"] > 0
                            ): ?>

                                <span class="badge badge-recurring">

                                    <?= (int)$hostel["recurring"] ?>

                                </span>

                            <?php else: ?>

                                0

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                (int)$hostel["high_severity"] > 0
                            ): ?>

                                <span class="badge badge-high">

                                    <?= (int)$hostel["high_severity"] ?>

                                </span>

                            <?php else: ?>

                                0

                            <?php endif; ?>

                        </td>


                        <td>

                            <span class="badge badge-pending">

                                <?= (int)$hostel["pending"] ?>

                            </span>

                        </td>


                        <td>

                            <span class="badge badge-progress">

                                <?= (int)$hostel["in_progress"] ?>

                            </span>

                        </td>


                        <td>

                            <span class="badge badge-resolved">

                                <?= (int)$hostel["resolved"] ?>

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
     CATEGORY DETAILED ANALYSIS
========================================================= -->

<div class="section">

    <div class="table-card">

        <h2>
            Category-wise Complaint Analysis
        </h2>

        <p class="table-description">
            Analysis of complaint categories and their
            recurring, severity and resolution patterns.
        </p>


        <?php if (empty($category_analysis)): ?>

            <div class="empty">
                No category analysis available.
            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Category
                        </th>

                        <th>
                            Total
                        </th>

                        <th>
                            Recurring
                        </th>

                        <th>
                            High Severity
                        </th>

                        <th>
                            Pending
                        </th>

                        <th>
                            In Progress
                        </th>

                        <th>
                            Resolved
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($category_analysis as $category): ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $category["category"]
                                    ?? "Unknown"
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= (int)$category["total"] ?>

                        </td>


                        <td>

                            <?php if (
                                (int)$category["recurring"] > 0
                            ): ?>

                                <span class="badge badge-recurring">

                                    <?= (int)$category["recurring"] ?>

                                </span>

                            <?php else: ?>

                                0

                            <?php endif; ?>

                        </td>


                        <td>

                            <?php if (
                                (int)$category["high_severity"] > 0
                            ): ?>

                                <span class="badge badge-high">

                                    <?= (int)$category["high_severity"] ?>

                                </span>

                            <?php else: ?>

                                0

                            <?php endif; ?>

                        </td>


                        <td>

                            <span class="badge badge-pending">

                                <?= (int)$category["pending"] ?>

                            </span>

                        </td>


                        <td>

                            <span class="badge badge-progress">

                                <?= (int)$category["in_progress"] ?>

                            </span>

                        </td>


                        <td>

                            <span class="badge badge-resolved">

                                <?= (int)$category["resolved"] ?>

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
     SEVERITY ANALYSIS
========================================================= -->

<div class="section">

    <div class="table-card">

        <h2>
            Severity Distribution
        </h2>

        <p class="table-description">
            Distribution of complaints according to their
            recorded severity level.
        </p>


        <?php if (empty($severity_analysis)): ?>

            <div class="empty">
                No severity data available.
            </div>

        <?php else: ?>

            <table>

                <thead>

                    <tr>

                        <th>
                            Severity
                        </th>

                        <th>
                            Number of Complaints
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $severity_analysis as $severity
                ): ?>

                    <?php

                    $severity_name =
                        $severity["severity"]
                        ?? "Unknown";

                    $severity_class =
                        strtolower(
                            $severity_name
                        );

                    ?>

                    <tr>

                        <td>

                            <?php if (
                                in_array(
                                    $severity_class,
                                    [
                                        "high",
                                        "medium",
                                        "low"
                                    ],
                                    true
                                )
                            ): ?>

                                <span
                                    class="badge badge-<?=
                                        htmlspecialchars(
                                            $severity_class
                                        )
                                    ?>"
                                >
                                    <?= htmlspecialchars(
                                        $severity_name
                                    ) ?>
                                </span>

                            <?php else: ?>

                                <span class="badge badge-neutral">
                                    <?= htmlspecialchars(
                                        $severity_name
                                    ) ?>
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <?= (int)$severity["total"] ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>

        <?php endif; ?>

    </div>

</div>



<!-- =========================================================
     RECURRING PATTERN ANALYSIS
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Recurring Complaint Patterns
    </h2>

    <p class="section-description">
        These patterns highlight categories and hostels
        where repeated complaints have been recorded.
    </p>


    <div class="analysis-grid">


        <!-- RECURRING CATEGORIES -->

        <div class="analysis-card">

            <h3>
                Recurring Categories
            </h3>


            <?php if (empty($recurring_categories)): ?>

                <div class="empty">
                    No recurring category patterns found.
                </div>

            <?php else: ?>

                <?php foreach (
                    $recurring_categories as $category
                ): ?>

                    <div class="analysis-item">

                        <span class="analysis-name">

                            <?= htmlspecialchars(
                                $category["category"]
                                ?? "Unknown"
                            ) ?>

                        </span>


                        <span class="analysis-count">

                            <?= (int)$category["recurring"] ?>

                            recurring

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>



        <!-- RECURRING HOSTELS -->

        <div class="analysis-card">

            <h3>
                Recurring Issues by Hostel
            </h3>


            <?php if (empty($recurring_hostels)): ?>

                <div class="empty">
                    No recurring hostel patterns found.
                </div>

            <?php else: ?>

                <?php foreach (
                    $recurring_hostels as $hostel
                ): ?>

                    <div class="analysis-item">

                        <span class="analysis-name">

                            <?= htmlspecialchars(
                                $hostel["hostel"]
                                ?? "Unknown"
                            ) ?>

                        </span>


                        <span class="analysis-count">

                            <?= (int)$hostel["recurring"] ?>

                            recurring

                        </span>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </div>


    </div>

</div>



<!-- =========================================================
     RECURRING COMPLAINT DETAILS
========================================================= -->

<div class="section">

    <div class="table-card">

        <h2>
            Repeated Complaint Details
        </h2>

        <p class="table-description">
            Individual complaints that are marked as recurring
            or have similar complaints associated with them.
        </p>


        <?php if (empty($recurring_list)): ?>

            <div class="empty">
                No recurring or repeated complaints found.
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
                            Description
                        </th>

                        <th>
                            Severity
                        </th>

                        <th>
                            Similar
                        </th>

                        <th>
                            Status
                        </th>

                        <th>
                            Maintenance
                        </th>

                        <th>
                            Created
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach (
                    $recurring_list as $complaint
                ): ?>

                    <?php

                    $severity =
                        $complaint["severity"]
                        ?? "Unknown";

                    $severity_class =
                        strtolower($severity);


                    $status =
                        $complaint["status"]
                        ?? "Pending";


                    $status_class =
                        "badge-pending";


                    if ($status === "In Progress") {

                        $status_class =
                            "badge-progress";

                    } elseif ($status === "Resolved") {

                        $status_class =
                            "badge-resolved";

                    }

                    ?>

                    <tr>

                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $complaint["complaint_id"]
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["hostel"]
                                ?? "-"
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


                        <td class="description-cell">

                            <?= htmlspecialchars(
                                $complaint["description"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?php if (
                                in_array(
                                    $severity_class,
                                    [
                                        "high",
                                        "medium",
                                        "low"
                                    ],
                                    true
                                )
                            ): ?>

                                <span
                                    class="badge badge-<?=
                                        htmlspecialchars(
                                            $severity_class
                                        )
                                    ?>"
                                >
                                    <?= htmlspecialchars(
                                        $severity
                                    ) ?>
                                </span>

                            <?php else: ?>

                                <span class="badge badge-neutral">
                                    <?= htmlspecialchars(
                                        $severity
                                    ) ?>
                                </span>

                            <?php endif; ?>

                        </td>


                        <td>

                            <span class="badge badge-recurring">

                                <?= (int)(
                                    $complaint["similar_count"]
                                    ?? 0
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <span
                                class="badge <?= $status_class ?>"
                            >
                                <?= htmlspecialchars(
                                    $status
                                ) ?>
                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint[
                                    "maintenance_status"
                                ]
                                ?? "Not Started"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $complaint["created_at"]
                                ?? "-"
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
     PREVENTIVE MAINTENANCE
========================================================= -->

<div class="section">

    <h2 class="section-title">
        Preventive Maintenance Focus
    </h2>

    <p class="section-description">
        Categories and locations with recurring complaint
        patterns are listed here for management review.
    </p>


    <div class="insight-grid">


        <?php

        $preventive_found = false;


        foreach (
            $recurring_categories as $category
        ):

            if (
                (int)$category["recurring"] > 0
            ):

                $preventive_found = true;

        ?>

            <div class="insight-box warning">

                <div class="insight-title">

                    <?= htmlspecialchars(
                        $category["category"]
                        ?? "Unknown"
                    ) ?>

                </div>


                <div class="insight-text">

                    This category contains

                    <strong>
                        <?= (int)$category["total"] ?>
                    </strong>

                    total complaint(s), including

                    <strong>
                        <?= (int)$category["recurring"] ?>
                    </strong>

                    recurring complaint(s).

                    Management can review the related
                    complaints and determine whether
                    preventive inspection or maintenance
                    is appropriate.

                </div>

            </div>

        <?php

            endif;

        endforeach;


        foreach (
            $recurring_hostels as $hostel
        ):

            if (
                (int)$hostel["recurring"] > 0
            ):

                $preventive_found = true;

        ?>

            <div class="insight-box">

                <div class="insight-title">

                    Hostel:
                    <?= htmlspecialchars(
                        $hostel["hostel"]
                        ?? "Unknown"
                    ) ?>

                </div>


                <div class="insight-text">

                    This hostel has

                    <strong>
                        <?= (int)$hostel["recurring"] ?>
                    </strong>

                    recurring complaint(s).

                    Management can review the affected
                    rooms and complaint categories for
                    possible repeated maintenance issues.

                </div>

            </div>

        <?php

            endif;

        endforeach;


        if (!$preventive_found):

        ?>

            <div class="insight-box">

                <div class="insight-title">
                    No recurring pattern currently identified
                </div>


                <div class="insight-text">

                    There are currently no recurring complaint
                    patterns available for preventive maintenance
                    review.

                </div>

            </div>

        <?php endif; ?>

    </div>

</div>



<!-- =========================================================
     INFORMATION
========================================================= -->

<div class="info-box">

    <strong>
        Analysis Information:
    </strong>

    This page is intended for detailed complaint analysis.
    The Management Dashboard provides the overall operational
    overview, while this page focuses on complaint distribution,
    category and hostel patterns, severity, recurring issues
    and possible areas for preventive maintenance review.

</div>


</div>


</body>

</html>