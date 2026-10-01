<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

$warden_username = $_SESSION["username"] ?? "";
$warden_role = $_SESSION["role"] ?? "";

if ($warden_role !== "warden") {
    header("Location: ../index.php");
    exit();
}

$warden_hostel = $_SESSION["hostel_type"] ?? "";

if ($warden_hostel === "") {
    if ($warden_username === "girlswarden") {
        $warden_hostel = "Girls Hostel";
    } else {
        $warden_hostel = "Boys Hostel";
    }
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $complaint_id = trim($_POST["complaint_id"] ?? "");
    $new_severity = trim($_POST["priority"] ?? "");
    $new_status = trim($_POST["status"] ?? "");

    $allowed_severities = [
        "Low",
        "Medium",
        "High"
    ];

    $allowed_statuses = [
        "Pending",
        "In Progress",
        "Resolved",
        "Rejected"
    ];

    if (
        $complaint_id !== "" &&
        in_array($new_severity, $allowed_severities, true) &&
        in_array($new_status, $allowed_statuses, true)
    ) {

        $update_sql = "
            UPDATE complaints
            SET
                severity = :severity,
                status = :status,
                updated_at = CURRENT_TIMESTAMP
            WHERE complaint_id = :complaint_id
            AND hostel = :hostel
        ";

        $update_stmt = $pdo->prepare($update_sql);

        $update_stmt->execute([
            ":severity" => $new_severity,
            ":status" => $new_status,
            ":complaint_id" => $complaint_id,
            ":hostel" => $warden_hostel
        ]);
    }

    header("Location: dashboard.php");
    exit();
}

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
        c.status,
        c.review_decision,
        c.created_at
    FROM complaints c
    LEFT JOIN students s
        ON c.student_id = s.student_id
    WHERE c.hostel = :hostel
    ORDER BY c.created_at DESC
";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":hostel" => $warden_hostel
]);

$hostel_complaints = $stmt->fetchAll(PDO::FETCH_ASSOC);

$total_complaints = count($hostel_complaints);

$todo_count = 0;
$progress_count = 0;
$complete_count = 0;
$rejected_count = 0;

foreach ($hostel_complaints as $complaint) {

    if ($complaint["status"] === "Pending") {
        $todo_count++;
    }

    if ($complaint["status"] === "In Progress") {
        $progress_count++;
    }

    if ($complaint["status"] === "Resolved") {
        $complete_count++;
    }

    if ($complaint["status"] === "Rejected") {
        $rejected_count++;
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

<title>FixNest Warden Dashboard</title>

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

.navbar {
    height: 70px;
    background: #1d4ed8;
    color: white;

    display: flex;
    align-items: center;
    justify-content: space-between;

    padding: 0 30px;
}

.navbar-left {
    display: flex;
    align-items: center;
    gap: 15px;
}

.navbar h2 {
    font-size: 22px;
}

.hostel-label {
    font-size: 14px;
    opacity: 0.9;
    margin-top: 3px;
}

.navbar-actions {
    display: flex;
    align-items: center;
    gap: 10px;
}

.raise-btn {
    color: white;
    background: #16a34a;

    text-decoration: none;

    padding: 9px 16px;

    border-radius: 7px;

    font-weight: bold;
}

.raise-btn:hover {
    background: #15803d;
}

.logout {
    color: #1d4ed8;
    background: white;

    text-decoration: none;

    padding: 9px 16px;

    border-radius: 7px;

    font-weight: bold;
}

.logout:hover {
    background: #eff6ff;
}

.container {
    width: 92%;
    max-width: 1250px;
    margin: 30px auto;
}

.page-header {
    display: flex;
    justify-content: space-between;
    align-items: center;

    margin-bottom: 25px;
}

.page-header h1 {
    font-size: 28px;
    color: #111827;
}

.page-header p {
    color: #6b7280;
    margin-top: 5px;
}

.header-actions {
    display: flex;
    gap: 10px;
}

.header-btn {
    display: inline-block;

    padding: 10px 16px;

    border-radius: 7px;

    text-decoration: none;

    font-size: 14px;

    font-weight: bold;
}

.header-btn-green {
    background: #16a34a;
    color: white;
}

.header-btn-green:hover {
    background: #15803d;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);

    gap: 18px;

    margin-bottom: 30px;
}

.stat-card {
    background: white;

    border-radius: 12px;

    padding: 22px;

    box-shadow:
        0 3px 10px rgba(0, 0, 0, 0.06);
}

.stat-title {
    font-size: 14px;
    color: #6b7280;
    margin-bottom: 10px;
}

.stat-number {
    font-size: 30px;
    font-weight: bold;
    color: #1d4ed8;
}

.filter-box {
    background: white;

    padding: 18px;

    border-radius: 12px;

    margin-bottom: 20px;

    box-shadow:
        0 3px 10px rgba(0, 0, 0, 0.06);

    display: flex;
    gap: 15px;
    flex-wrap: wrap;
}

.filter-box select,
.filter-box input {
    padding: 10px 12px;

    border: 1px solid #d1d5db;

    border-radius: 7px;

    font-size: 14px;
}

.complaint-list {
    display: flex;
    flex-direction: column;
    gap: 18px;
}

.complaint-card {
    background: white;

    border-radius: 12px;

    padding: 22px;

    box-shadow:
        0 3px 10px rgba(0, 0, 0, 0.06);
}

.complaint-top {
    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 20px;

    margin-bottom: 15px;
}

.complaint-id {
    font-size: 14px;

    color: #6b7280;

    margin-bottom: 5px;
}

.complaint-title {
    font-size: 19px;

    font-weight: bold;

    color: #111827;
}

.complaint-description {
    margin-top: 10px;

    color: #4b5563;

    line-height: 1.5;
}

.status {
    display: inline-block;

    padding: 6px 11px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: bold;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-progress {
    background: #dbeafe;
    color: #1e40af;
}

.status-resolved {
    background: #dcfce7;
    color: #166534;
}

.status-rejected {
    background: #fee2e2;
    color: #991b1b;
}

.complaint-info {
    display: grid;

    grid-template-columns: repeat(4, 1fr);

    gap: 15px;

    margin: 18px 0;
}

.info-item {
    background: #f9fafb;

    padding: 12px;

    border-radius: 8px;
}

.info-label {
    font-size: 12px;

    color: #6b7280;

    margin-bottom: 5px;
}

.info-value {
    font-size: 14px;

    font-weight: 600;

    color: #111827;
}

.severity {
    display: inline-block;

    padding: 5px 10px;

    border-radius: 15px;

    font-size: 12px;

    font-weight: bold;
}

.severity-low {
    background: #dcfce7;
    color: #166534;
}

.severity-medium {
    background: #fef3c7;
    color: #92400e;
}

.severity-high {
    background: #fee2e2;
    color: #166534;
}

.complaint-footer {
    display: flex;

    justify-content: space-between;

    align-items: center;

    margin-top: 15px;

    padding-top: 15px;

    border-top: 1px solid #e5e7eb;
}

.created-date {
    color: #6b7280;

    font-size: 13px;
}

.view-btn {
    display: inline-block;

    background: #1d4ed8;

    color: white;

    text-decoration: none;

    padding: 9px 15px;

    border-radius: 7px;

    font-size: 13px;

    font-weight: bold;
}

.view-btn:hover {
    background: #1e40af;
}

.empty-state {
    background: white;

    padding: 50px;

    text-align: center;

    border-radius: 12px;

    color: #6b7280;
}

@media (max-width: 900px) {

    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }

    .complaint-info {
        grid-template-columns: repeat(2, 1fr);
    }

}

@media (max-width: 600px) {

    .navbar {
        height: auto;
        padding: 15px;

        gap: 15px;
    }

    .navbar-actions {
        flex-direction: column;
        align-items: stretch;
    }

    .navbar h2 {
        font-size: 18px;
    }

    .container {
        width: 94%;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;

        gap: 15px;
    }

    .header-actions {
        width: 100%;
    }

    .header-btn {
        flex: 1;
        text-align: center;
    }

    .stats-grid {
        grid-template-columns: 1fr;
    }

    .complaint-info {
        grid-template-columns: 1fr;
    }

    .complaint-top {
        flex-direction: column;
    }

    .complaint-footer {
        flex-direction: column;
        align-items: flex-start;

        gap: 12px;
    }

}

</style>

</head>

<body>

<div class="navbar">

    <div class="navbar-left">

        <div>

            <h2>
                FixNest Warden
            </h2>

            <div class="hostel-label">
                <?= htmlspecialchars($warden_hostel) ?>
            </div>

        </div>

    </div>

    <div class="navbar-actions">

        

        <a
            href="../index.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</div>

<div class="container">

    <div class="page-header">

        <div>

            <h1>
                Warden Dashboard
            </h1>

            <p>
                Review and monitor complaints from
                <?= htmlspecialchars($warden_hostel) ?>.
            </p>

        </div>

        

    </div>

    <div class="stats-grid">

        <div class="stat-card">

            <div class="stat-title">
                Total Complaints
            </div>

            <div class="stat-number">
                <?= $total_complaints ?>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-title">
                Pending Review
            </div>

            <div class="stat-number">
                <?= $todo_count ?>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-title">
                In Progress
            </div>

            <div class="stat-number">
                <?= $progress_count ?>
            </div>

        </div>

        <div class="stat-card">

            <div class="stat-title">
                Resolved
            </div>

            <div class="stat-number">
                <?= $complete_count ?>
            </div>

        </div>

    </div>

    <div class="filter-box">

        <select id="statusFilter">

            <option value="all">All Status</option>

            <option value="Pending">Pending</option>

            <option value="In Progress">In Progress</option>

            <option value="Resolved">Resolved</option>

            <option value="Rejected">Rejected</option>

        </select>

        <select id="severityFilter">

            <option value="all">All Severity</option>

            <option value="High">High</option>

            <option value="Medium">Medium</option>

            <option value="Low">Low</option>

        </select>

        <input
            type="text"
            id="searchInput"
            placeholder="Search complaints..."
        >

    </div>

    <div
        class="complaint-list"
        id="complaintList"
    >

        <?php if (empty($hostel_complaints)): ?>

            <div class="empty-state">

                <h3>
                    No complaints found
                </h3>

                <p>
                    There are currently no complaints
                    for this hostel.
                </p>

            </div>

        <?php else: ?>

            <?php foreach ($hostel_complaints as $complaint): ?>

                <?php

                if (!empty($complaint["room_number"])) {

                    $location =
                        $complaint["location_type"]
                        . " - Room "
                        . $complaint["room_number"];

                } elseif (!empty($complaint["floor_number"])) {

                    $location =
                        $complaint["location_type"]
                        . " - Floor "
                        . $complaint["floor_number"];

                } else {

                    $location =
                        $complaint["location_type"];
                }

                $status_class = "status-pending";

                if ($complaint["status"] === "In Progress") {
                    $status_class = "status-progress";
                } elseif ($complaint["status"] === "Resolved") {
                    $status_class = "status-resolved";
                } elseif ($complaint["status"] === "Rejected") {
                    $status_class = "status-rejected";
                }

                $severity_class = "severity-low";

                if ($complaint["severity"] === "Medium") {
                    $severity_class = "severity-medium";
                } elseif ($complaint["severity"] === "High") {
                    $severity_class = "severity-high";
                }

                $student_display =
                    $complaint["student_name"]
                    ?? "";

                if ($student_display === "") {
                    $student_display = "Warden Raised";
                }

                ?>

                <div
                    class="complaint-card"
                    data-status="<?= htmlspecialchars($complaint["status"]) ?>"
                    data-severity="<?= htmlspecialchars($complaint["severity"]) ?>"
                >

                    <div class="complaint-top">

                        <div>

                            <div class="complaint-id">

                                <?= htmlspecialchars(
                                    $complaint["complaint_id"]
                                ) ?>

                            </div>

                            <div class="complaint-title">

                                <?= htmlspecialchars(
                                    $complaint["category"]
                                ) ?>

                            </div>

                        </div>

                        <div>

                            <span
                                class="status <?= $status_class ?>"
                            >

                                <?= htmlspecialchars(
                                    $complaint["status"]
                                ) ?>

                            </span>

                        </div>

                    </div>

                    <div class="complaint-description">

                        <?= nl2br(
                            htmlspecialchars(
                                $complaint["description"]
                            )
                        ) ?>

                    </div>

                    <div class="complaint-info">

                        <div class="info-item">

                            <div class="info-label">
                                Raised By
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $student_display
                                ) ?>

                            </div>

                        </div>

                        <div class="info-item">

                            <div class="info-label">
                                Location
                            </div>

                            <div class="info-value">

                                <?= htmlspecialchars(
                                    $location
                                ) ?>

                            </div>

                        </div>

                        <div class="info-item">

                            <div class="info-label">
                                Severity
                            </div>

                            <div class="info-value">

                                <span
                                    class="severity <?= $severity_class ?>"
                                >

                                    <?= htmlspecialchars(
                                        $complaint["severity"]
                                    ) ?>

                                </span>

                            </div>

                        </div>

                        <div class="info-item">

                            <div class="info-label">
                                Review Decision
                            </div>

                            <div class="info-value">

                                <?php

                                if (
                                    empty(
                                        $complaint["review_decision"]
                                    )
                                ) {

                                    echo "Pending Review";

                                } else {

                                    echo htmlspecialchars(
                                        $complaint["review_decision"]
                                    );

                                }

                                ?>

                            </div>

                        </div>

                    </div>

                    <div class="complaint-footer">

                        <div class="created-date">

                            Submitted:

                            <?= htmlspecialchars(
                                date(
                                    "d M Y, h:i A",
                                    strtotime(
                                        $complaint["created_at"]
                                    )
                                )
                            ) ?>

                        </div>

                        <a
                            href="complaint-details.php?complaint_id=<?= urlencode(
                                $complaint["complaint_id"]
                            ) ?>"
                            class="view-btn"
                        >
                            View Details
                        </a>

                    </div>

                </div>

            <?php endforeach; ?>

        <?php endif; ?>

    </div>

</div>

<script>

const statusFilter =
    document.getElementById("statusFilter");

const severityFilter =
    document.getElementById("severityFilter");

const searchInput =
    document.getElementById("searchInput");

const complaintCards =
    document.querySelectorAll(".complaint-card");

function filterComplaints() {

    const selectedStatus =
        statusFilter.value;

    const selectedSeverity =
        severityFilter.value;

    const searchText =
        searchInput.value
            .toLowerCase()
            .trim();

    complaintCards.forEach(function(card) {

        const cardStatus =
            card.dataset.status;

        const cardSeverity =
            card.dataset.severity;

        const cardText =
            card.innerText.toLowerCase();

        const statusMatches =
            selectedStatus === "all"
            || cardStatus === selectedStatus;

        const severityMatches =
            selectedSeverity === "all"
            || cardSeverity === selectedSeverity;

        const searchMatches =
            searchText === ""
            || cardText.includes(searchText);

        card.style.display =
            statusMatches &&
            severityMatches &&
            searchMatches
                ? ""
                : "none";

    });

}

statusFilter.addEventListener(
    "change",
    filterComplaints
);

severityFilter.addEventListener(
    "change",
    filterComplaints
);

searchInput.addEventListener(
    "input",
    filterComplaints
);

</script>

</body>
</html>