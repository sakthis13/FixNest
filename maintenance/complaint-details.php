<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

// Maintenance authentication
if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "maintenance"
) {
    header("Location: ../index.php");
    exit();
}

$complaint_id = trim(
    $_GET["complaint_id"] ??
    $_GET["id"] ??
    ""
);

if ($complaint_id === "") {
    die("Invalid complaint ID.");
}

$message = "";
$message_type = "";


// Update maintenance status
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $maintenance_status =
        $_POST["maintenance_status"] ?? "Not Started";

    $maintenance_remarks =
        trim($_POST["maintenance_remarks"] ?? "");

    $allowed_statuses = [
        "Not Started",
        "In Progress",
        "Resolved"
    ];

    if (
        !in_array(
            $maintenance_status,
            $allowed_statuses,
            true
        )
    ) {
        $maintenance_status = "Not Started";
    }


    // Get current complaint
    $check_sql = "
        SELECT
            status,
            resolution_confirmation
        FROM complaints
        WHERE complaint_id = :complaint_id
          AND review_decision = 'Approved'
    ";

    $check_stmt = $pdo->prepare($check_sql);

    $check_stmt->execute([
        ":complaint_id" => $complaint_id
    ]);

    $current_complaint =
        $check_stmt->fetch(PDO::FETCH_ASSOC);


    if (!$current_complaint) {

        die(
            "Complaint not found or it has not been approved by the warden."
        );
    }


    $current_status =
        $current_complaint["status"] ?? "Pending";

    $current_confirmation =
        $current_complaint["resolution_confirmation"]
        ?? "Pending";


    // Do not change a complaint while waiting for warden confirmation
    if (
        $current_status === "Resolved" &&
        $current_confirmation === "Pending"
    ) {

        header(
            "Location: complaint-details.php?complaint_id="
            . urlencode($complaint_id)
            . "&waiting=1"
        );

        exit();
    }


    // Convert maintenance status to complaint status
    if ($maintenance_status === "Resolved") {

        $complaint_status = "Resolved";

        $resolution_confirmation = "Pending";

    } elseif ($maintenance_status === "In Progress") {

        $complaint_status = "In Progress";

        $resolution_confirmation = "Pending";

    } else {

        $complaint_status = "Pending";

        $resolution_confirmation = "Pending";
    }


    // Save maintenance update
    $sql = "
        UPDATE complaints

        SET
            maintenance_status = :maintenance_status,

            maintenance_remarks = :maintenance_remarks,

            maintenance_updated_at = CURRENT_TIMESTAMP,

            status = :status,

            resolution_confirmation = :resolution_confirmation,

            updated_at = CURRENT_TIMESTAMP

        WHERE complaint_id = :complaint_id

          AND review_decision = 'Approved'
    ";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([

        ":maintenance_status" =>
            $maintenance_status,

        ":maintenance_remarks" =>
            $maintenance_remarks !== ""
                ? $maintenance_remarks
                : null,

        ":status" =>
            $complaint_status,

        ":resolution_confirmation" =>
            $resolution_confirmation,

        ":complaint_id" =>
            $complaint_id
    ]);


    header(
        "Location: complaint-details.php?complaint_id="
        . urlencode($complaint_id)
        . "&saved=1"
    );

    exit();
}


// Fetch complaint
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

        c.resolution_confirmation,

        c.warden_remarks,

        c.maintenance_status,

        c.maintenance_remarks,

        c.maintenance_updated_at,

        c.created_at,

        c.updated_at

    FROM complaints c

    LEFT JOIN students s

        ON c.student_id = s.student_id

    WHERE c.complaint_id = :complaint_id

      AND c.review_decision = 'Approved'
";


$stmt = $pdo->prepare($sql);

$stmt->execute([

    ":complaint_id" =>
        $complaint_id

]);

$complaint =
    $stmt->fetch(PDO::FETCH_ASSOC);


if (!$complaint) {

    die(
        "Complaint not found or it has not been approved by the warden."
    );
}


$maintenance_status =
    $complaint["maintenance_status"]
    ?? "Not Started";

if ($maintenance_status === "") {

    $maintenance_status = "Not Started";
}


$resolution_confirmation =
    $complaint["resolution_confirmation"]
    ?? "Pending";


$confidence = null;

if (
    $complaint["severity_confidence"]
    !== null
) {

    $confidence =
        round(
            (float)$complaint["severity_confidence"]
            * 100
        );
}


// Location
$location_parts = [];

if (!empty($complaint["room_number"])) {

    $location_parts[] =
        "Room "
        . $complaint["room_number"];
}

if (!empty($complaint["floor_number"])) {

    $location_parts[] =
        "Floor "
        . $complaint["floor_number"];
}

if (!empty($complaint["location_type"])) {

    $location_parts[] =
        $complaint["location_type"];
}

$location =
    !empty($location_parts)
    ? implode(" • ", $location_parts)
    : "Not specified";


// Status display
if (
    $maintenance_status === "Resolved" &&
    $resolution_confirmation === "Pending"
) {

    $status_class = "awaiting";

    $status_text = "Awaiting Warden Confirmation";

} elseif (
    $maintenance_status === "Resolved" &&
    $resolution_confirmation === "Confirmed"
) {

    $status_class = "resolved";

    $status_text = "Resolved";

} elseif (
    $maintenance_status === "In Progress"
) {

    $status_class = "in-progress";

    $status_text = "In Progress";

} else {

    $status_class = "not-started";

    $status_text = "Not Started";
}


// Severity
$severity =
    $complaint["severity"]
    ?? "Medium";

$severity_class =
    strtolower($severity);


// Can maintenance edit?
$waiting_for_confirmation =
    (
        $maintenance_status === "Resolved" &&
        $resolution_confirmation === "Pending"
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
    Complaint Details | FixNest Maintenance
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


/* Navbar */

.navbar {
    height: 65px;
    background: #2563eb;
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

.logout {
    color: white;
    text-decoration: none;

    background: rgba(255,255,255,0.15);

    padding: 9px 15px;

    border-radius: 7px;
}


/* Container */

.container {
    max-width: 1100px;
    margin: 30px auto;
    padding: 0 20px;
}


/* Back */

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


/* Header */

.page-header {
    display: flex;

    justify-content: space-between;
    align-items: center;

    gap: 20px;

    margin-bottom: 25px;
}

.page-header h1 {
    font-size: 28px;
    margin-bottom: 7px;
}

.complaint-id {
    color: #64748b;
    font-size: 14px;
}


/* Status */

.status {
    padding: 10px 16px;

    border-radius: 20px;

    font-weight: bold;

    white-space: nowrap;

    font-size: 13px;
}

.not-started {
    background: #fef3c7;
    color: #92400e;
}

.in-progress {
    background: #dbeafe;
    color: #1d4ed8;
}

.awaiting {
    background: #fef3c7;
    color: #92400e;
}

.resolved {
    background: #dcfce7;
    color: #166534;
}


/* Messages */

.success {
    background: #dcfce7;
    color: #166534;

    padding: 13px 16px;

    border-radius: 7px;

    margin-bottom: 20px;

    border: 1px solid #bbf7d0;
}

.warning {
    background: #fef3c7;
    color: #92400e;

    padding: 13px 16px;

    border-radius: 7px;

    margin-bottom: 20px;

    border: 1px solid #fde68a;
}


/* Card */

.card {
    background: white;

    border-radius: 12px;

    padding: 25px;

    margin-bottom: 20px;

    box-shadow:
        0 3px 12px rgba(0,0,0,0.06);
}

.card h2 {
    font-size: 19px;
    margin-bottom: 20px;
}


/* Details */

.details-grid {
    display: grid;

    grid-template-columns:
        repeat(2, 1fr);

    gap: 18px;
}

.detail {
    background: #f8fafc;

    padding: 15px;

    border-radius: 8px;

    border: 1px solid #e5e7eb;
}

.label {
    display: block;

    font-size: 12px;

    color: #64748b;

    margin-bottom: 6px;

    text-transform: uppercase;

    font-weight: bold;
}

.value {
    font-size: 15px;
    font-weight: 600;
}


/* Description */

.description {
    background: #f8fafc;

    padding: 18px;

    border-radius: 8px;

    line-height: 1.6;

    border: 1px solid #e5e7eb;
}


/* Severity */

.severity {
    display: inline-block;

    padding: 6px 12px;

    border-radius: 15px;

    font-size: 13px;

    font-weight: bold;
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


/* Warden */

.warden-box {
    background: #eff6ff;

    border-left: 4px solid #2563eb;

    padding: 18px;

    border-radius: 7px;

    line-height: 1.6;
}

.warden-decision {
    font-weight: bold;
    margin-bottom: 10px;
}


/* Form */

.form-group {
    margin-bottom: 20px;
}

.form-group label {
    display: block;

    margin-bottom: 8px;

    font-weight: bold;
}

select,
textarea {
    width: 100%;

    padding: 12px;

    border: 1px solid #cbd5e1;

    border-radius: 7px;

    font-size: 15px;

    font-family: inherit;

    background: white;
}

select:focus,
textarea:focus {
    outline: none;

    border-color: #2563eb;

    box-shadow:
        0 0 0 3px
        rgba(37,99,235,0.1);
}

textarea {
    min-height: 130px;
    resize: vertical;
}


/* Button */

.save-btn {
    background: #2563eb;

    color: white;

    border: none;

    border-radius: 7px;

    padding: 12px 22px;

    font-size: 15px;

    font-weight: bold;

    cursor: pointer;
}

.save-btn:hover {
    background: #1d4ed8;
}

.save-btn:disabled {
    background: #94a3b8;
    cursor: not-allowed;
}


/* Info */

.info-box {
    background: #eff6ff;

    border: 1px solid #bfdbfe;

    border-left: 4px solid #2563eb;

    color: #1e40af;

    padding: 15px;

    border-radius: 7px;

    font-size: 13px;

    line-height: 1.6;
}


/* Responsive */

@media (max-width: 700px) {

    .details-grid {
        grid-template-columns: 1fr;
    }

    .page-header {
        flex-direction: column;
        align-items: flex-start;
    }

    .navbar {
        padding: 0 15px;
    }

    .container {
        padding: 0 12px;
    }

}

</style>

</head>


<body>


<nav class="navbar">

    <div class="logo">
        FixNest Maintenance
    </div>

    <a
        href="../index.php"
        class="logout"
    >
        Logout
    </a>

</nav>


<div class="container">


<a
    href="dashboard.php"
    class="back"
>
    ← Back to Dashboard
</a>


<?php if (isset($_GET["saved"])): ?>

<div class="success">

    Maintenance update saved successfully.

    <?php if (
        $maintenance_status === "Resolved"
    ): ?>

        The complaint is now waiting for
        warden confirmation.

    <?php endif; ?>

</div>

<?php endif; ?>


<?php if (isset($_GET["waiting"])): ?>

<div class="warning">

    This complaint is waiting for warden confirmation.
    Maintenance cannot update it until the warden responds.

</div>

<?php endif; ?>


<!-- Header -->

<div class="page-header">

    <div>

        <h1>
            Complaint Details
        </h1>

        <div class="complaint-id">

            Complaint ID:

            <?= htmlspecialchars(
                $complaint["complaint_id"]
            ) ?>

        </div>

    </div>


    <div
        class="status <?= htmlspecialchars(
            $status_class
        ) ?>"
    >

        <?= htmlspecialchars(
            $status_text
        ) ?>

    </div>

</div>


<!-- Student -->

<div class="card">

<h2>
    Student Information
</h2>


<div class="details-grid">


<div class="detail">

<span class="label">
    Student Name
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["student_name"]
    ?? "Unknown"
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Student ID
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["student_id"]
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Hostel
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["hostel"]
    ?? "Not specified"
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Location
</span>

<span class="value">

<?= htmlspecialchars(
    $location
) ?>

</span>

</div>


</div>

</div>


<!-- Complaint -->

<div class="card">

<h2>
    Complaint Information
</h2>


<div class="details-grid">


<div class="detail">

<span class="label">
    Category
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["category"]
    ?? "-"
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Severity
</span>

<span
    class="severity <?= htmlspecialchars(
        $severity_class
    ) ?>"
>

<?= htmlspecialchars(
    $severity
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Recurring
</span>

<span class="value">

<?= !empty(
    $complaint["recurring"]
)
    ? "Yes"
    : "No"
?>

</span>

</div>


<div class="detail">

<span class="label">
    Similar Complaints
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["similar_count"]
    ?? "0"
) ?>

</span>

</div>


<?php if ($confidence !== null): ?>

<div class="detail">

<span class="label">
    AI Confidence
</span>

<span class="value">

<?= $confidence ?>%

</span>

</div>

<?php endif; ?>


<div class="detail">

<span class="label">
    Complaint Status
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["status"]
    ?? "Pending"
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Resolution Confirmation
</span>

<span class="value">

<?php

if (
    $resolution_confirmation === "Confirmed"
) {

    echo "Confirmed";

} elseif (
    $resolution_confirmation === "Rejected"
) {

    echo "Rejected - Sent Back to Maintenance";

} else {

    echo "Pending";

}

?>

</span>

</div>


</div>


<br>


<span class="label">
    Description
</span>


<div class="description">

<?= nl2br(
    htmlspecialchars(
        $complaint["description"]
    )
) ?>

</div>

</div>


<!-- Warden Review -->

<div class="card">

<h2>
    Warden Review
</h2>


<div class="warden-box">


<div class="warden-decision">

Decision:

<?= htmlspecialchars(
    $complaint["review_decision"]
    ?? "Approved"
) ?>

</div>


<strong>
    Warden Remarks:
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

<span style="color:#64748b;">

    No remarks provided.

</span>

<?php endif; ?>


</div>

</div>


<!-- Maintenance Update -->

<div class="card">

<h2>
    Maintenance Work Update
</h2>


<?php if ($waiting_for_confirmation): ?>

<div class="warning">

    Maintenance has marked this complaint as resolved.
    It is now waiting for the warden to confirm the resolution.

</div>

<?php else: ?>


<form method="POST">


<div class="form-group">

<label
    for="maintenance_status"
>
    Work Status
</label>


<select
    name="maintenance_status"
    id="maintenance_status"
    required
>


<option
    value="Not Started"

    <?= $maintenance_status === "Not Started"
        ? "selected"
        : "" ?>
>

    Not Started

</option>


<option
    value="In Progress"

    <?= $maintenance_status === "In Progress"
        ? "selected"
        : "" ?>
>

    In Progress

</option>


<option
    value="Resolved"

    <?= $maintenance_status === "Resolved"
        ? "selected"
        : "" ?>
>

    Resolved

</option>


</select>

</div>


<div class="form-group">

<label
    for="maintenance_remarks"
>
    Maintenance Remarks
</label>


<textarea
    name="maintenance_remarks"
    id="maintenance_remarks"
    placeholder="Enter details about the maintenance work..."
><?= htmlspecialchars(
    $complaint["maintenance_remarks"]
    ?? ""
) ?></textarea>

</div>


<button
    type="submit"
    class="save-btn"
>

    Save Maintenance Update

</button>


</form>


<?php endif; ?>

</div>


<!-- Timeline -->

<div class="card">

<h2>
    Timeline
</h2>


<div class="details-grid">


<div class="detail">

<span class="label">
    Complaint Created
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["created_at"]
) ?>

</span>

</div>


<div class="detail">

<span class="label">
    Last Updated
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["updated_at"]
) ?>

</span>

</div>


<?php if (
    !empty(
        $complaint["maintenance_updated_at"]
    )
): ?>

<div class="detail">

<span class="label">
    Maintenance Updated
</span>

<span class="value">

<?= htmlspecialchars(
    $complaint["maintenance_updated_at"]
) ?>

</span>

</div>

<?php endif; ?>


</div>

</div>


<div class="info-box">

<strong>
    Maintenance Head:
</strong>

Update the work status after reviewing the complaint.

Use <strong>In Progress</strong> when maintenance work has started.

Use <strong>Resolved</strong> only after the physical issue has been fixed.

After selecting <strong>Resolved</strong>, the complaint is sent to the
warden for final confirmation.

</div>


</div>

</body>

</html>