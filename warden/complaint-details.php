
<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

// Warden authentication
if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: ../index.php");
    exit();
}

$warden_username = $_SESSION["username"] ?? "";
$warden_hostel = $_SESSION["hostel_type"] ?? "";

// Get hostel
if ($warden_hostel === "") {

    if ($warden_username === "girlswarden") {
        $warden_hostel = "Girls Hostel";
    } else {
        $warden_hostel = "Boys Hostel";
    }
}

$complaintId = trim(
    $_GET["complaint_id"] ??
    $_GET["id"] ??
    ""
);

$complaint = null;
$error = "";
$success = "";


// ============================================================
// HANDLE WARDEN ACTIONS
// ============================================================

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    $warden_remarks =
        trim($_POST["warden_remarks"] ?? "");


    // ========================================================
    // INITIAL COMPLAINT REVIEW
    // ========================================================

    if ($action === "review") {

        $review_status =
            $_POST["review_status"] ?? "pending";


        /*
         * WARDEN PRIORITY
         *
         * The database column is "severity".
         * We use it as the complaint priority.
         */

        $selected_priority =
            trim($_POST["priority"] ?? "");


        // Allow only valid priority values
        $allowed_priorities = [
            "High",
            "Medium",
            "Low"
        ];


        if (!in_array(
            $selected_priority,
            $allowed_priorities,
            true
        )) {

            $selected_priority = "Medium";
        }


        // Determine review decision and status

        if ($review_status === "approved") {

            $decision_text = "Approved";
            $new_status = "In Progress";

        } elseif ($review_status === "rejected") {

            $decision_text = "Rejected";
            $new_status = "Rejected";

        } else {

            $decision_text = "Pending Review";
            $new_status = "Pending";
        }


        if ($complaintId !== "") {

            /*
             * Update:
             *
             * 1. Warden review decision
             * 2. Complaint status
             * 3. Warden remarks
             * 4. Warden-selected priority
             * 5. Resolution confirmation
             * 6. Updated time
             */

            $update_sql = "
                UPDATE complaints

                SET
                    review_decision = :review_decision,

                    status = :status,

                    severity = :severity,

                    warden_remarks = :warden_remarks,

                    resolution_confirmation = 'Pending',

                    updated_at = CURRENT_TIMESTAMP

                WHERE complaint_id = :complaint_id

                  AND hostel = :hostel

                  AND (
                      review_decision IS NULL
                      OR review_decision = 'Pending Review'
                  )
            ";

            $update_stmt =
                $pdo->prepare($update_sql);


            $update_stmt->execute([

                ":review_decision" =>
                    $decision_text,

                ":status" =>
                    $new_status,

                ":severity" =>
                    $selected_priority,

                ":warden_remarks" =>
                    $warden_remarks !== ""
                        ? $warden_remarks
                        : null,

                ":complaint_id" =>
                    $complaintId,

                ":hostel" =>
                    $warden_hostel
            ]);


            header(
                "Location: complaint-details.php?complaint_id="
                . urlencode($complaintId)
                . "&saved=1"
            );

            exit();
        }
    }


    // ========================================================
    // CONFIRM MAINTENANCE RESOLUTION
    // ========================================================

    elseif ($action === "confirm_resolution") {

        $update_sql = "
            UPDATE complaints

            SET
                status = 'Resolved',

                resolution_confirmation = 'Confirmed',

                updated_at = CURRENT_TIMESTAMP

            WHERE complaint_id = :complaint_id

              AND hostel = :hostel

              AND review_decision = 'Approved'

              AND status = 'Resolved'

              AND resolution_confirmation = 'Pending'
        ";

        $update_stmt =
            $pdo->prepare($update_sql);


        $update_stmt->execute([

            ":complaint_id" =>
                $complaintId,

            ":hostel" =>
                $warden_hostel
        ]);


        header(
            "Location: complaint-details.php?complaint_id="
            . urlencode($complaintId)
            . "&confirmed=1"
        );

        exit();
    }


    // ========================================================
    // SEND COMPLAINT BACK TO MAINTENANCE
    // ========================================================

    elseif ($action === "reject_resolution") {

        $update_sql = "
            UPDATE complaints

            SET
                status = 'In Progress',

                resolution_confirmation = 'Rejected',

                warden_remarks = :warden_remarks,

                updated_at = CURRENT_TIMESTAMP

            WHERE complaint_id = :complaint_id

              AND hostel = :hostel

              AND review_decision = 'Approved'

              AND status = 'Resolved'

              AND resolution_confirmation = 'Pending'
        ";

        $update_stmt =
            $pdo->prepare($update_sql);


        $update_stmt->execute([

            ":warden_remarks" =>
                $warden_remarks !== ""
                    ? $warden_remarks
                    : null,

            ":complaint_id" =>
                $complaintId,

            ":hostel" =>
                $warden_hostel
        ]);


        header(
            "Location: complaint-details.php?complaint_id="
            . urlencode($complaintId)
            . "&returned=1"
        );

        exit();
    }
}


// ============================================================
// SUCCESS MESSAGES
// ============================================================

if (
    isset($_GET["saved"]) &&
    $_GET["saved"] === "1"
) {

    $success =
        "Warden review and priority saved successfully.";
}

if (
    isset($_GET["confirmed"]) &&
    $_GET["confirmed"] === "1"
) {

    $success =
        "Resolution confirmed. Complaint is now finally resolved.";
}

if (
    isset($_GET["returned"]) &&
    $_GET["returned"] === "1"
) {

    $success =
        "Complaint returned to maintenance for further work.";
}


// ============================================================
// FETCH COMPLAINT
// ============================================================

if ($complaintId === "") {

    $error =
        "Invalid complaint request.";

} else {

    $sql = "
        SELECT

            c.complaint_id,

            c.student_id,

            s.student_name,

            s.gender,

            s.department,

            s.email,

            s.phone,

            c.hostel,

            c.location_type,

            c.room_number,

            c.floor_number,

            c.category,

            c.description,

            c.severity,

            c.severity_confidence,

            c.review_decision,

            c.warden_remarks,

            c.recurring,

            c.similar_count,

            c.status,

            c.resolution_confirmation,

            c.maintenance_status,

            c.maintenance_remarks,

            c.maintenance_updated_at,

            c.created_at,

            c.updated_at

        FROM complaints c

        LEFT JOIN students s
            ON c.student_id = s.student_id

        WHERE c.complaint_id = :complaint_id

          AND c.hostel = :hostel
    ";


    $stmt = $pdo->prepare($sql);


    $stmt->execute([

        ":complaint_id" =>
            $complaintId,

        ":hostel" =>
            $warden_hostel
    ]);


    $complaint =
        $stmt->fetch(PDO::FETCH_ASSOC);


    if (!$complaint) {

        $error =
            "Complaint not found.";
    }
}


// ============================================================
// DEFAULT VALUES
// ============================================================

$status =
    $complaint["status"] ?? "";

$severity =
    $complaint["severity"] ?? "Medium";


/*
 * Make sure priority always has a valid value.
 */

if (!in_array(
    $severity,
    [
        "High",
        "Medium",
        "Low"
    ],
    true
)) {

    $severity = "Medium";
}


$review_decision =
    $complaint["review_decision"]
    ?? "Pending Review";

$resolution_confirmation =
    $complaint["resolution_confirmation"]
    ?? "Pending";

$maintenance_status =
    $complaint["maintenance_status"]
    ?? "Not Started";


// ============================================================
// STATUS DISPLAY
// ============================================================

$status_class =
    strtolower(
        str_replace(
            " ",
            "-",
            $status
        )
    );


$severity_class =
    strtolower($severity);


// ============================================================
// AI CONFIDENCE
// ============================================================

$confidence =
    isset(
        $complaint["severity_confidence"]
    )
    ? round(
        (float)$complaint["severity_confidence"]
        * 100
    )
    : 0;


// ============================================================
// LOCATION
// ============================================================

$location = "";

if (
    ($complaint["room_number"] ?? "")
    !== ""
) {

    $location =
        "Room "
        . $complaint["room_number"];
}

if (
    ($complaint["floor_number"] ?? "")
    !== ""
) {

    $location .=
        (
            $location !== ""
            ? " | "
            : ""
        )
        . "Floor "
        . $complaint["floor_number"];
}

if ($location === "") {

    $location =
        $complaint["location_type"]
        ?? "Not specified";
}


// ============================================================
// DATES
// ============================================================

$created_date = "";

if (!empty($complaint["created_at"])) {

    $created_date =
        date(
            "d F Y, h:i A",
            strtotime(
                $complaint["created_at"]
            )
        );
}


$updated_date = "";

if (!empty($complaint["updated_at"])) {

    $updated_date =
        date(
            "d F Y, h:i A",
            strtotime(
                $complaint["updated_at"]
            )
        );
}


$maintenance_updated_date = "";

if (
    !empty(
        $complaint["maintenance_updated_at"]
    )
) {

    $maintenance_updated_date =
        date(
            "d F Y, h:i A",
            strtotime(
                $complaint["maintenance_updated_at"]
            )
        );
}


// ============================================================
// WORKFLOW STATES
// ============================================================

$is_pending_review =
    (
        $review_decision === "Pending Review"
        ||
        $review_decision === ""
        ||
        $review_decision === null
    );


$is_maintenance_stage =
    (
        $review_decision === "Approved"
        &&
        $status === "In Progress"
    );


$is_waiting_confirmation =
    (
        $review_decision === "Approved"
        &&
        $status === "Resolved"
        &&
        $resolution_confirmation === "Pending"
    );


$is_final_resolved =
    (
        $review_decision === "Approved"
        &&
        $status === "Resolved"
        &&
        $resolution_confirmation === "Confirmed"
    );


$is_rejected =
    (
        $review_decision === "Rejected"
        &&
        $status === "Rejected"
    );

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
    Complaint Details - FixNest
</title>

<style>

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: Arial, Helvetica, sans-serif;
    background: #f3f4f6;
    color: #1f2937;
    line-height: 1.6;
}


/* Navbar */

.navbar {
    background: #2563eb;
    color: white;

    padding: 18px 35px;

    display: flex;

    justify-content: space-between;

    align-items: center;

    box-shadow:
        0 2px 8px rgba(0, 0, 0, 0.08);
}

.navbar h2 {
    font-size: 22px;
    font-weight: 700;
}

.back-link {
    color: white;
    text-decoration: none;

    font-size: 14px;
    font-weight: 600;
}

.back-link:hover {
    text-decoration: underline;
}


/* Container */

.container {
    width: min(1200px, 92%);

    margin: 35px auto;
}


/* Header */

.page-header {
    display: flex;

    justify-content: space-between;

    align-items: center;

    gap: 20px;

    margin-bottom: 25px;
}

.page-eyebrow {
    color: #2563eb;

    font-size: 13px;

    font-weight: 700;

    text-transform: uppercase;

    letter-spacing: 0.8px;

    margin-bottom: 4px;
}

.page-header h1 {
    font-size: 30px;

    color: #111827;

    margin-bottom: 5px;
}

.page-header p {
    color: #6b7280;

    font-size: 14px;
}


/* Buttons */

.btn {
    display: inline-block;

    padding: 10px 18px;

    border-radius: 8px;

    text-decoration: none;

    border: none;

    cursor: pointer;

    font-size: 14px;

    font-weight: 600;
}

.btn-primary {
    background: #2563eb;
    color: white;
}

.btn-primary:hover {
    background: #1d4ed8;
}

.btn-secondary {
    background: white;

    color: #374151;

    border: 1px solid #d1d5db;
}

.btn-secondary:hover {
    background: #f9fafb;
}

.btn-success {
    background: #16a34a;
    color: white;
}

.btn-success:hover {
    background: #15803d;
}

.btn-danger {
    background: #dc2626;
    color: white;
}

.btn-danger:hover {
    background: #b91c1c;
}


/* Messages */

.success-message {
    background: #dcfce7;

    border: 1px solid #bbf7d0;

    color: #166534;

    padding: 15px 18px;

    border-radius: 10px;

    margin-bottom: 20px;
}

.error-message {
    background: #fee2e2;

    border: 1px solid #fecaca;

    color: #991b1b;

    padding: 15px 18px;

    border-radius: 10px;

    margin-bottom: 20px;
}

.warning-message {
    background: #fef3c7;

    border: 1px solid #fde68a;

    color: #92400e;

    padding: 15px 18px;

    border-radius: 10px;

    margin-bottom: 20px;
}


/* Layout */

.details-layout {
    display: grid;

    grid-template-columns:
        minmax(0, 2fr)
        minmax(280px, 1fr);

    gap: 25px;

    align-items: start;
}

.details-main,
.details-side {
    min-width: 0;
}


/* Cards */

.content-card {
    background: white;

    border-radius: 14px;

    padding: 25px;

    margin-bottom: 25px;

    border: 1px solid #e5e7eb;

    box-shadow:
        0 4px 12px rgba(0, 0, 0, 0.04);
}

.card-heading-row {
    display: flex;

    justify-content: space-between;

    align-items: flex-start;

    gap: 20px;

    padding-bottom: 20px;

    border-bottom: 1px solid #e5e7eb;
}

.card-heading-row h2 {
    font-size: 22px;

    color: #111827;

    margin: 10px 0 3px;
}

.muted-text {
    color: #6b7280;

    font-size: 13px;
}


/* Status */

.status-badge {
    display: inline-block;

    padding: 5px 11px;

    border-radius: 20px;

    font-size: 12px;

    font-weight: 700;
}

.status-pending {
    background: #fef3c7;
    color: #92400e;
}

.status-in-progress {
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

.status-default {
    background: #e5e7eb;
    color: #374151;
}

.status-awaiting {
    background: #fef3c7;
    color: #92400e;
}


/* Priority */

.priority-label {
    padding: 8px 13px;

    border-radius: 8px;

    font-size: 13px;

    font-weight: 700;

    white-space: nowrap;
}

.priority-high {
    background: #fee2e2;
    color: #b91c1c;
}

.priority-medium {
    background: #fef3c7;
    color: #92400e;
}

.priority-low {
    background: #dcfce7;
    color: #166534;
}


/* Priority selector */

.priority-select {
    width: 100%;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    background: white;

    font-size: 14px;

    color: #1f2937;

    outline: none;

    cursor: pointer;
}

.priority-select:focus {
    border-color: #2563eb;

    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, 0.1);
}

.priority-help {
    color: #6b7280;

    font-size: 12px;

    margin-top: 6px;
}


/* Details */

.detail-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 18px;

    margin-top: 22px;
}

.detail-item {
    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 15px;
}

.detail-label {
    display: block;

    color: #6b7280;

    font-size: 12px;

    font-weight: 600;

    margin-bottom: 5px;
}

.detail-item strong {
    color: #111827;

    font-size: 14px;
}


/* Description */

.description-section {
    margin-top: 25px;
}

.description-section h3 {
    font-size: 17px;

    margin-bottom: 10px;

    color: #111827;
}

.description-box {
    background: #f9fafb;

    border: 1px solid #e5e7eb;

    border-radius: 10px;

    padding: 17px;

    color: #374151;

    font-size: 14px;

    white-space: pre-wrap;
}


/* Section heading */

.section-heading {
    margin-bottom: 22px;
}

.section-heading h2 {
    font-size: 20px;

    color: #111827;

    margin-bottom: 3px;
}

.section-heading p {
    color: #6b7280;

    font-size: 13px;
}


/* Forms */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 7px;

    font-size: 14px;

    font-weight: 600;

    color: #374151;
}

.form-control {
    width: 100%;

    padding: 11px 13px;

    border: 1px solid #d1d5db;

    border-radius: 8px;

    background: white;

    font-size: 14px;

    color: #1f2937;

    outline: none;
}

.form-control:focus {
    border-color: #2563eb;

    box-shadow:
        0 0 0 3px
        rgba(37, 99, 235, 0.1);
}

textarea.form-control {
    resize: vertical;

    min-height: 120px;
}

.action-buttons {
    display: flex;

    gap: 10px;

    margin-top: 5px;
}


/* Resolution box */

.confirmation-box {
    background: #fefce8;

    border: 1px solid #fde68a;

    border-left: 4px solid #f59e0b;

    padding: 18px;

    border-radius: 9px;

    margin-bottom: 20px;
}

.confirmation-box h3 {
    color: #92400e;

    font-size: 17px;

    margin-bottom: 7px;
}

.confirmation-box p {
    color: #78350f;

    font-size: 13px;

    line-height: 1.6;
}


/* Warden decision */

.decision-box {
    background: #eff6ff;

    border: 1px solid #bfdbfe;

    border-left: 4px solid #2563eb;

    padding: 16px;

    border-radius: 8px;

    margin-bottom: 20px;
}

.decision-box strong {
    color: #1e3a8a;
}


/* Timeline */

.content-card > h3 {
    font-size: 18px;

    margin-bottom: 20px;

    color: #111827;
}

.timeline {
    position: relative;

    padding-left: 25px;
}

.timeline::before {
    content: "";

    position: absolute;

    left: 6px;

    top: 5px;

    bottom: 5px;

    width: 2px;

    background: #d1d5db;
}

.timeline-item {
    position: relative;

    display: flex;

    gap: 13px;

    padding-bottom: 25px;
}

.timeline-item:last-child {
    padding-bottom: 0;
}

.timeline-dot {
    position: absolute;

    left: -25px;

    top: 4px;

    width: 14px;

    height: 14px;

    border-radius: 50%;

    background: #d1d5db;

    border: 3px solid white;

    box-shadow:
        0 0 0 1px #d1d5db;
}

.timeline-item.completed .timeline-dot {
    background: #22c55e;

    box-shadow:
        0 0 0 1px #22c55e;
}

.timeline-item.active .timeline-dot {
    background: #2563eb;

    box-shadow:
        0 0 0 1px #2563eb;
}

.timeline-item strong {
    display: block;

    font-size: 14px;

    color: #111827;
}

.timeline-item p {
    color: #6b7280;

    font-size: 12px;

    margin-top: 2px;
}


/* AI */

.ai-prediction-box {
    display: flex;

    align-items: center;

    gap: 12px;

    background: #eff6ff;

    border: 1px solid #dbeafe;

    border-radius: 10px;

    padding: 15px;

    margin-bottom: 18px;
}

.ai-icon {
    width: 38px;

    height: 38px;

    display: flex;

    justify-content: center;

    align-items: center;

    background: #2563eb;

    color: white;

    border-radius: 50%;

    font-size: 18px;
}

.ai-prediction-box strong {
    display: block;

    color: #1e3a8a;

    font-size: 13px;
}

.ai-prediction-box p {
    color: #1d4ed8;

    font-size: 15px;

    font-weight: 700;

    margin-top: 2px;
}

.prediction-row {
    display: flex;

    justify-content: space-between;

    padding: 11px 0;

    border-bottom: 1px solid #e5e7eb;

    font-size: 13px;
}

.prediction-row:last-of-type {
    border-bottom: none;
}

.prediction-row span {
    color: #6b7280;
}

.prediction-row strong {
    color: #111827;
}

.small-note {
    color: #9ca3af;

    font-size: 11px;

    margin-top: 12px;
}


/* Responsive */

@media (max-width: 850px) {

    .details-layout {
        grid-template-columns: 1fr;
    }

}

@media (max-width: 650px) {

    .navbar {
        padding: 15px 20px;
    }

    .navbar h2 {
        font-size: 18px;
    }

    .container {
        width: 94%;

        margin: 25px auto;
    }

    .page-header {
        flex-direction: column;

        align-items: flex-start;
    }

    .page-header h1 {
        font-size: 25px;
    }

    .card-heading-row {
        flex-direction: column;
    }

    .detail-grid {
        grid-template-columns: 1fr;
    }

    .content-card {
        padding: 18px;
    }

    .action-buttons {
        flex-direction: column;
    }

    .action-buttons .btn {
        width: 100%;

        text-align: center;
    }

}

</style>

</head>

<body>


<div class="navbar">

    <h2>
        FixNest Warden
    </h2>

    <a
        href="dashboard.php"
        class="back-link"
    >
        ← Back to Dashboard
    </a>

</div>


<div class="container">


<div class="page-header">

    <div>

        <p class="page-eyebrow">
            Warden Panel
        </p>

        <h1>
            Complaint Details
        </h1>

        <p>
            Review and monitor the complaint workflow.
        </p>

    </div>


    <a
        href="dashboard.php"
        class="btn btn-secondary"
    >
        ← Back to Complaints
    </a>

</div>


<?php if ($success !== ""): ?>

<div class="success-message">

    <?= htmlspecialchars($success) ?>

</div>

<?php endif; ?>


<?php if ($error !== ""): ?>

<div class="error-message">

    <?= htmlspecialchars($error) ?>

</div>

<?php else: ?>


<div class="details-layout">


<div class="details-main">


<!-- ========================================================
     COMPLAINT INFORMATION
========================================================= -->

<div class="content-card">


<div class="card-heading-row">


<div>


<?php

$display_status =
    $status !== ""
    ? $status
    : "Pending";

if ($is_waiting_confirmation) {

    $status_badge_class =
        "status-awaiting";

} elseif (
    in_array(
        $status_class,
        [
            "pending",
            "in-progress",
            "resolved",
            "rejected"
        ],
        true
    )
) {

    $status_badge_class =
        "status-" . $status_class;

} else {

    $status_badge_class =
        "status-default";
}

?>


<span
    class="status-badge
    <?= htmlspecialchars(
        $status_badge_class
    ) ?>"
>

<?php

if ($is_waiting_confirmation) {

    echo "Awaiting Warden Confirmation";

} elseif ($is_final_resolved) {

    echo "Resolved";

} else {

    echo htmlspecialchars(
        $display_status
    );
}

?>

</span>


<h2>

<?= htmlspecialchars(
    $complaint["category"]
    ?? "Complaint"
) ?>

</h2>


<p class="muted-text">

Complaint ID:

<?= htmlspecialchars(
    $complaint["complaint_id"]
) ?>

</p>


</div>


<div
    class="priority-label
    <?php

    if ($severity_class === "high") {

        echo "priority-high";

    } elseif ($severity_class === "medium") {

        echo "priority-medium";

    } else {

        echo "priority-low";
    }

    ?>"
>

<?= htmlspecialchars($severity) ?>

Priority

</div>


</div>


<div class="detail-grid">


<div class="detail-item">

<span class="detail-label">
    Student Name
</span>

<strong>

<?= htmlspecialchars(
    $complaint["student_name"]
    ?? "Not available"
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Register Number
</span>

<strong>

<?= htmlspecialchars(
    $complaint["student_id"]
    ?? "Not available"
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Hostel
</span>

<strong>

<?= htmlspecialchars(
    $complaint["hostel"]
    ?? "Not available"
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Location
</span>

<strong>

<?= htmlspecialchars($location) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Complaint Category
</span>

<strong>

<?= htmlspecialchars(
    $complaint["category"]
    ?? "Not available"
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Submitted On
</span>

<strong>

<?= htmlspecialchars(
    $created_date
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Department
</span>

<strong>

<?= htmlspecialchars(
    $complaint["department"]
    ?? "Not available"
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Last Updated
</span>

<strong>

<?= htmlspecialchars(
    $updated_date
) ?>

</strong>

</div>


</div>


<div class="description-section">

<h3>
    Complaint Description
</h3>

<div class="description-box">

<?= htmlspecialchars(
    $complaint["description"]
    ?? "No description available."
) ?>

</div>

</div>


</div>


<!-- ========================================================
     INITIAL WARDEN REVIEW
========================================================= -->

<?php if ($is_pending_review): ?>

<div class="content-card">

<div class="section-heading">

<h2>
    Warden Review
</h2>

<p>
    Review the complaint, adjust its priority if necessary,
    and decide whether it should be forwarded to the
    maintenance department.
</p>

</div>


<form method="POST">


<input
    type="hidden"
    name="action"
    value="review"
>


<!-- ======================================================
     PRIORITY
======================================================= -->

<div class="form-group">

<label for="priority">

Complaint Priority

</label>


<select
    name="priority"
    id="priority"
    class="priority-select"
    required
>

<option
    value="High"
    <?= $severity === "High"
        ? "selected"
        : "" ?>
>
    High Priority
</option>


<option
    value="Medium"
    <?= $severity === "Medium"
        ? "selected"
        : "" ?>
>
    Medium Priority
</option>


<option
    value="Low"
    <?= $severity === "Low"
        ? "selected"
        : "" ?>
>
    Low Priority
</option>

</select>


<p class="priority-help">

AI suggested priority:
<strong>
    <?= htmlspecialchars($severity) ?>
</strong>

You may change it before approving the complaint.

</p>

</div>


<!-- ======================================================
     REVIEW DECISION
======================================================= -->

<div class="form-group">

<label for="review_status">

Review Decision

</label>


<select
    name="review_status"
    id="review_status"
    class="form-control"
>

<option value="pending">

Pending Review

</option>

<option value="approved">

Approve & Forward to Maintenance

</option>

<option value="rejected">

Reject Complaint

</option>

</select>

</div>


<!-- ======================================================
     WARDEN REMARKS
======================================================= -->

<div class="form-group">

<label for="warden_remarks">

Warden Remarks

</label>


<textarea
    name="warden_remarks"
    id="warden_remarks"
    class="form-control"
    rows="5"
    placeholder="Enter your review remarks..."
></textarea>

</div>


<div class="action-buttons">

<button
    type="submit"
    class="btn btn-primary"
>

Save Review

</button>


<a
    href="dashboard.php"
    class="btn btn-secondary"
>

Cancel

</a>

</div>


</form>

</div>

<?php endif; ?>


<!-- ========================================================
     REJECTED COMPLAINT
========================================================= -->

<?php if ($is_rejected): ?>

<div class="content-card">

<div class="section-heading">

<h2>
    Complaint Rejected
</h2>

<p>
    This complaint has been rejected by the warden.
    No maintenance action is required.
</p>

</div>


<div class="decision-box">

<strong>
    Warden Decision:
</strong>

Rejected

<br><br>

<strong>
    Priority:
</strong>

<?= htmlspecialchars($severity) ?>

<br><br>

<strong>
    Remarks:
</strong>

<br>

<?php if (
    !empty(
        $complaint["warden_remarks"]
    )
): ?>

<?= nl2br(
    htmlspecialchars(
        $complaint["warden_remarks"]
    )
) ?>

<?php else: ?>

No remarks provided.

<?php endif; ?>

</div>

</div>

<?php endif; ?>


<!-- ========================================================
     MAINTENANCE PROGRESS
========================================================= -->

<?php if (
    $review_decision === "Approved"
): ?>

<div class="content-card">

<div class="section-heading">

<h2>
    Maintenance Progress
</h2>

<p>
    Current progress reported by the Maintenance Head.
</p>

</div>


<div class="detail-grid">


<div class="detail-item">

<span class="detail-label">
    Maintenance Status
</span>

<strong>

<?= htmlspecialchars(
    $maintenance_status
) ?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Resolution Confirmation
</span>

<strong>

<?php

if (
    $resolution_confirmation === "Confirmed"
) {

    echo "Confirmed";

} elseif (
    $resolution_confirmation === "Rejected"
) {

    echo "Not Confirmed";

} else {

    echo "Pending";

}

?>

</strong>

</div>


<div class="detail-item">

<span class="detail-label">
    Current Priority
</span>

<strong>

<?= htmlspecialchars($severity) ?>

</strong>

</div>


<?php if (
    !empty(
        $complaint["maintenance_remarks"]
    )
): ?>

<div
    class="detail-item"
    style="grid-column: 1 / -1;"
>

<span class="detail-label">
    Maintenance Remarks
</span>

<strong>

<?= nl2br(
    htmlspecialchars(
        $complaint["maintenance_remarks"]
    )
) ?>

</strong>

</div>

<?php endif; ?>


<?php if (
    $maintenance_updated_date !== ""
): ?>

<div class="detail-item">

<span class="detail-label">
    Maintenance Updated
</span>

<strong>

<?= htmlspecialchars(
    $maintenance_updated_date
) ?>

</strong>

</div>

<?php endif; ?>


</div>

</div>

<?php endif; ?>


<!-- ========================================================
     RESOLUTION CONFIRMATION
========================================================= -->

<?php if ($is_waiting_confirmation): ?>

<div class="content-card">

<div class="confirmation-box">

<h3>
    Resolution Confirmation Required
</h3>

<p>

The Maintenance Head has marked this complaint
as resolved. Please verify the issue before giving
the final confirmation.

</p>

</div>


<form method="POST">


<div class="form-group">

<label for="warden_remarks">

Warden Remarks

</label>


<textarea
    name="warden_remarks"
    id="warden_remarks"
    class="form-control"
    rows="5"
    placeholder="Enter remarks about the resolution..."
></textarea>

</div>


<div class="action-buttons">


<button
    type="submit"
    name="action"
    value="confirm_resolution"
    class="btn btn-success"
>

Confirm Resolved

</button>


<button
    type="submit"
    name="action"
    value="reject_resolution"
    class="btn btn-danger"
>

Not Resolved

</button>


</div>


</form>

</div>

<?php endif; ?>


<!-- ========================================================
     FINAL RESOLVED
========================================================= -->

<?php if ($is_final_resolved): ?>

<div class="content-card">

<div class="confirmation-box">

<h3>
    Resolution Confirmed
</h3>

<p>

The Warden has confirmed that the maintenance
work has been completed successfully.

The complaint is now finally resolved.

</p>

</div>

</div>

<?php endif; ?>


</div>


<!-- ========================================================
     RIGHT SIDE
========================================================= -->

<div class="details-side">


<!-- ======================================================
     TIMELINE
======================================================= -->

<div class="content-card">

<h3>
    Complaint Timeline
</h3>


<div class="timeline">


<!-- Submitted -->

<div class="timeline-item completed">

<span class="timeline-dot"></span>

<div>

<strong>
    Complaint Submitted
</strong>

<p>

<?= htmlspecialchars(
    $created_date
) ?>

</p>

</div>

</div>


<!-- Warden Review -->

<div
    class="timeline-item
    <?php

    if (
        $review_decision === "Approved"
        ||
        $review_decision === "Rejected"
    ) {

        echo "completed";

    } else {

        echo "active";
    }

    ?>"
>

<span class="timeline-dot"></span>

<div>

<strong>
    Warden Review
</strong>

<p>

<?php

if ($review_decision === "Approved") {

    echo "Approved and forwarded to Maintenance.";

} elseif ($review_decision === "Rejected") {

    echo "Complaint rejected.";

} else {

    echo "Waiting for warden review.";
}

?>

</p>

</div>

</div>


<!-- Maintenance -->

<?php if (
    $review_decision === "Approved"
): ?>

<div
    class="timeline-item
    <?php

    if (
        $maintenance_status === "Resolved"
        ||
        $maintenance_status === "In Progress"
    ) {

        echo "completed";

    } else {

        echo "active";
    }

    ?>"
>

<span class="timeline-dot"></span>

<div>

<strong>
    Maintenance
</strong>

<p>

<?php

if (
    $maintenance_status === "Resolved"
) {

    echo "Maintenance marked the complaint as resolved.";

} elseif (
    $maintenance_status === "In Progress"
) {

    echo "Maintenance work is in progress.";

} else {

    echo "Waiting for maintenance work to start.";
}

?>

</p>

</div>

</div>

<?php endif; ?>


<!-- Resolution confirmation -->

<?php if (
    $review_decision === "Approved"
): ?>

<div
    class="timeline-item
    <?php

    if ($is_final_resolved) {

        echo "completed";

    } elseif ($is_waiting_confirmation) {

        echo "active";
    }

    ?>"
>

<span class="timeline-dot"></span>

<div>

<strong>
    Warden Confirmation
</strong>

<p>

<?php

if ($is_final_resolved) {

    echo "Resolution confirmed by Warden.";

} elseif ($is_waiting_confirmation) {

    echo "Waiting for Warden confirmation.";

} elseif (
    $resolution_confirmation === "Rejected"
) {

    echo "Resolution was not confirmed. Sent back to Maintenance.";

} else {

    echo "Waiting for maintenance resolution.";
}

?>

</p>

</div>

</div>

<?php endif; ?>


<!-- Final status -->

<div
    class="timeline-item
    <?php

    if ($is_final_resolved) {

        echo "completed";

    } elseif ($is_rejected) {

        echo "completed";

    }

    ?>"
>

<span class="timeline-dot"></span>

<div>

<strong>
    Final Status
</strong>

<p>

<?php

if ($is_final_resolved) {

    echo "Complaint finally resolved.";

} elseif ($is_rejected) {

    echo "Complaint process ended because it was rejected.";

} elseif ($is_waiting_confirmation) {

    echo "Waiting for Warden confirmation.";

} else {

    echo "Complaint is still being processed.";

}

?>

</p>

</div>

</div>


</div>

</div>


<!-- ======================================================
     AI ANALYSIS
======================================================= -->

<div class="content-card">

<h3>
    AI Analysis
</h3>


<div class="ai-prediction-box">

<span class="ai-icon">
    ✦
</span>

<div>

<strong>
    Current Complaint Priority
</strong>

<p>

<?= htmlspecialchars(
    $severity !== ""
        ? $severity
        : "Not available"
) ?>

</p>

</div>

</div>


<div class="prediction-row">

<span>
    AI Confidence
</span>

<strong>

<?= $confidence ?>%

</strong>

</div>


<div class="prediction-row">

<span>
    Recurring Complaint
</span>

<strong>

<?php

echo !empty(
    $complaint["recurring"]
)
    ? "Yes"
    : "No";

?>

</strong>

</div>


<div class="prediction-row">

<span>
    Similar Complaints
</span>

<strong>

<?= (int)(
    $complaint["similar_count"]
    ?? 0
) ?>

</strong>

</div>


<p class="small-note">

The initial priority is generated by the FixNest AI
module and can be adjusted by the Warden during review.

</p>

</div>


</div>


</div>


<?php endif; ?>


</div>

</body>

</html>

