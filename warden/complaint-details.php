<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

/*
|--------------------------------------------------------------------------
| Check Warden Login
|--------------------------------------------------------------------------
*/

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: ../index.php");
    exit();
}

$warden_username = $_SESSION["username"] ?? "";
$warden_hostel = $_SESSION["hostel_type"] ?? "";

/*
|--------------------------------------------------------------------------
| Determine Warden Hostel
|--------------------------------------------------------------------------
*/

if ($warden_hostel === "") {

    if ($warden_username === "girlswarden") {
        $warden_hostel = "Girls Hostel";
    } else {
        $warden_hostel = "Boys Hostel";
    }

}

/*
|--------------------------------------------------------------------------
| Get Complaint ID
|--------------------------------------------------------------------------
*/

$complaintId = trim(
    $_GET["complaint_id"] ??
    $_GET["id"] ??
    ""
);

/*
|--------------------------------------------------------------------------
| Fetch Complaint from PostgreSQL
|--------------------------------------------------------------------------
*/

$complaint = null;
$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $review_status = $_POST["review_status"] ?? "";
    $warden_remarks = trim($_POST["warden_remarks"] ?? "");

    /*
    |--------------------------------------------------------------------------
    | Convert Warden Decision to Database Status
    |--------------------------------------------------------------------------
    */

    if ($review_status === "approved") {
        $new_status = "In Progress";
    } elseif ($review_status === "rejected") {
        $new_status = "Rejected";
    } else {
        $new_status = "Pending";
    }


    /*
    |--------------------------------------------------------------------------
    | Update Complaint
    |--------------------------------------------------------------------------
    */

    if ($complaintId !== "") {

        
                 $update_sql = "UPDATE complaints
               SET review_decision = :review_decision,
                   status = :status,
                   warden_remarks = :warden_remarks,
                   updated_at = CURRENT_TIMESTAMP
               WHERE complaint_id = :complaint_id
                 AND hostel = :hostel";

        $update_stmt = $pdo->prepare($update_sql);

     $decision_text = "Pending Review";

if ($review_status === "approved") {
    $decision_text = "Approved";
} elseif ($review_status === "rejected") {
    $decision_text = "Rejected";
}

$update_stmt->execute([
    ":review_decision" => $decision_text,
    ":status" => $new_status,
    ":warden_remarks" => $warden_remarks,
    ":complaint_id" => $complaintId,
    ":hostel" => $warden_hostel
]);

        /*
        |--------------------------------------------------------------------------
        | Redirect to Avoid Duplicate Form Submission
        |--------------------------------------------------------------------------
        */

        header(
            "Location: complaint-details.php?complaint_id="
            . urlencode($complaintId)
            . "&saved=1"
        );

        exit();
    }
}


/*
|--------------------------------------------------------------------------
| Success Message
|--------------------------------------------------------------------------
*/

if (isset($_GET["saved"]) && $_GET["saved"] === "1") {
    $success = "Warden review saved successfully.";
}

if ($complaintId === "") {

    $error = "Invalid complaint request.";

} else {

    $sql = "SELECT
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
                c.recurring,
                c.similar_count,
                c.status,
                c.created_at,
                c.updated_at
            FROM complaints c
            LEFT JOIN students s
                ON c.student_id = s.student_id
            WHERE c.complaint_id = :complaint_id
              AND c.hostel = :hostel";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":complaint_id" => $complaintId,
        ":hostel" => $warden_hostel
    ]);

    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complaint) {
        $error = "Complaint not found.";
    }
}

/*
|--------------------------------------------------------------------------
| Helper Values
|--------------------------------------------------------------------------
*/

$status = $complaint["status"] ?? "";
$severity = $complaint["severity"] ?? "";

$status_class = strtolower(str_replace(" ", "-", $status));
$severity_class = strtolower($severity);

$confidence = isset($complaint["severity_confidence"])
    ? round((float)$complaint["severity_confidence"] * 100)
    : 0;

$location = "";

if (($complaint["room_number"] ?? "") !== "") {
    $location = $complaint["room_number"];
}

if (($complaint["floor_number"] ?? "") !== "") {
    $location .= ($location !== "" ? " | " : "") . $complaint["floor_number"];
}

if ($location === "") {
    $location = $complaint["location_type"] ?? "Not specified";
}

$created_date = "";

if (!empty($complaint["created_at"])) {
    $created_date = date(
        "d F Y, h:i A",
        strtotime($complaint["created_at"])
    );
}

$updated_date = "";

if (!empty($complaint["updated_at"])) {
    $updated_date = date(
        "d F Y, h:i A",
        strtotime($complaint["updated_at"])
    );
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Complaint Details - FixNest</title>

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

        /* ---------------------------------------------------------
           Navbar
        --------------------------------------------------------- */

        .navbar {
            background: #2563eb;
            color: white;
            padding: 18px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.08);
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

        /* ---------------------------------------------------------
           Main Container
        --------------------------------------------------------- */

        .container {
            width: min(1200px, 92%);
            margin: 35px auto;
        }

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

        /* ---------------------------------------------------------
           Buttons
        --------------------------------------------------------- */

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

        /* ---------------------------------------------------------
           Error
        --------------------------------------------------------- */

        .error-message {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;
            padding: 15px 18px;
            border-radius: 10px;
            margin-bottom: 20px;
        }

        /* ---------------------------------------------------------
           Layout
        --------------------------------------------------------- */

        .details-layout {
            display: grid;
            grid-template-columns: minmax(0, 2fr) minmax(280px, 1fr);
            gap: 25px;
            align-items: start;
        }

        .details-main,
        .details-side {
            min-width: 0;
        }

        /* ---------------------------------------------------------
           Cards
        --------------------------------------------------------- */

        .content-card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            margin-bottom: 25px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.04);
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

        /* ---------------------------------------------------------
           Status Badges
        --------------------------------------------------------- */

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

        /* ---------------------------------------------------------
           Priority
        --------------------------------------------------------- */

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

        /* ---------------------------------------------------------
           Details Grid
        --------------------------------------------------------- */

        .detail-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
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

        /* ---------------------------------------------------------
           Description
        --------------------------------------------------------- */

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

        /* ---------------------------------------------------------
           Section Heading
        --------------------------------------------------------- */

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

        /* ---------------------------------------------------------
           Forms
        --------------------------------------------------------- */

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
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.1);
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

        /* ---------------------------------------------------------
           Timeline
        --------------------------------------------------------- */

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
            box-shadow: 0 0 0 1px #d1d5db;
        }

        .timeline-item.completed .timeline-dot {
            background: #22c55e;
            box-shadow: 0 0 0 1px #22c55e;
        }

        .timeline-item.active .timeline-dot {
            background: #2563eb;
            box-shadow: 0 0 0 1px #2563eb;
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

        /* ---------------------------------------------------------
           AI Prediction
        --------------------------------------------------------- */

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

        .high-text {
            color: #dc2626 !important;
        }

        .medium-text {
            color: #d97706 !important;
        }

        .low-text {
            color: #16a34a !important;
        }

        .small-note {
            color: #9ca3af;
            font-size: 11px;
            margin-top: 12px;
        }

        /* ---------------------------------------------------------
           Responsive
        --------------------------------------------------------- */

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

<!-- =========================================================
     NAVBAR
========================================================= -->

<div class="navbar">

    <h2>FixNest Warden</h2>

    <a href="dashboard.php" class="back-link">
        ← Back to Dashboard
    </a>

</div>


<div class="container">

    <!-- =====================================================
         PAGE HEADER
    ====================================================== -->

    <div class="page-header">

        <div>

            <p class="page-eyebrow">
                Warden Panel
            </p>

            <h1>
                Complaint Details
            </h1>

            <p>
                Review the complaint submitted by the student.
            </p>

        </div>

        <a href="dashboard.php" class="btn btn-secondary">
            ← Back to Complaints
        </a>

    </div>


    <!-- =====================================================
         ERROR
    ====================================================== -->
    <?php if ($success !== ""): ?>

    <div class="success-message">
        <?php echo htmlspecialchars($success); ?>
    </div>

<?php endif; ?>
    <?php if ($error !== ""): ?>

        <div class="error-message">
            <?php echo htmlspecialchars($error); ?>
        </div>

    <?php else: ?>


        <!-- =================================================
             MAIN LAYOUT
        ================================================== -->

        <div class="details-layout">


            <!-- =============================================
                 LEFT SIDE
            ============================================== -->

            <div class="details-main">


                <!-- =========================================
                     COMPLAINT INFORMATION
                ========================================== -->

                <div class="content-card">

                    <div class="card-heading-row">

                        <div>

                            <?php

                            $display_status = $status !== ""
                                ? $status
                                : "Unknown";

                            $status_badge_class =
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
                                ? "status-" . $status_class
                                : "status-default";

                            ?>

                            <span class="status-badge <?php echo $status_badge_class; ?>">

                                <?php echo htmlspecialchars($display_status); ?>

                            </span>


                            <h2>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["category"] ?? "Complaint"
                                );
                                ?>

                            </h2>


                            <p class="muted-text">

                                Complaint ID:
                                <?php
                                echo htmlspecialchars(
                                    $complaint["complaint_id"]
                                );
                                ?>

                            </p>

                        </div>


                        <div class="priority-label
                            <?php
                            if ($severity_class === "high") {
                                echo "priority-high";
                            } elseif ($severity_class === "medium") {
                                echo "priority-medium";
                            } else {
                                echo "priority-low";
                            }
                            ?>
                        ">

                            <?php echo htmlspecialchars($severity); ?>
                            Priority

                        </div>

                    </div>


                    <!-- =====================================
                         DETAILS GRID
                    ====================================== -->

                    <div class="detail-grid">


                        <div class="detail-item">

                            <span class="detail-label">
                                Student Name
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["student_name"]
                                    ?? "Not available"
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Register Number
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["student_id"]
                                    ?? "Not available"
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Hostel
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["hostel"]
                                    ?? "Not available"
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Location
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars($location);
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Complaint Category
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["category"]
                                    ?? "Not available"
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Submitted On
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars($created_date);
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Department
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars(
                                    $complaint["department"]
                                    ?? "Not available"
                                );
                                ?>

                            </strong>

                        </div>


                        <div class="detail-item">

                            <span class="detail-label">
                                Last Updated
                            </span>

                            <strong>

                                <?php
                                echo htmlspecialchars($updated_date);
                                ?>

                            </strong>

                        </div>

                    </div>


                    <!-- =====================================
                         DESCRIPTION
                    ====================================== -->

                    <div class="description-section">

                        <h3>
                            Complaint Description
                        </h3>

                        <div class="description-box">

                            <?php
                            echo htmlspecialchars(
                                $complaint["description"]
                                ?? "No description available."
                            );
                            ?>

                        </div>

                    </div>

                </div>


                <!-- =========================================
                     WARDEN ACTION
                ========================================== -->

                <div class="content-card">

    <div class="section-heading">

        <div>
            <h2>Warden Review</h2>

            <p>
                Review the complaint and decide whether it should be
                forwarded to the maintenance department.
            </p>
        </div>

    </div>


    <form method="POST">

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


            </div>


            <!-- =============================================
                 RIGHT SIDE
            ============================================== -->

            <div class="details-side">


                <!-- =========================================
                     TIMELINE
                ========================================== -->

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
                                    <?php
                                    echo htmlspecialchars($created_date);
                                    ?>
                                </p>

                            </div>

                        </div>


                        <!-- Current Status -->

                        <div class="timeline-item active">

                            <span class="timeline-dot"></span>

                            <div>

                                <strong>
                                    Current Status
                                </strong>

                                <p>
                                    <?php
                                    echo htmlspecialchars(
                                        $status !== ""
                                            ? $status
                                            : "Pending"
                                    );
                                    ?>
                                </p>

                            </div>

                        </div>


                        <!-- Maintenance -->

                        <div class="timeline-item">

                            <span class="timeline-dot"></span>

                            <div>

                                <strong>
                                    Maintenance
                                </strong>

                                <p>
                                    Assignment tracking will be added later.
                                </p>

                            </div>

                        </div>


                        <!-- Resolution -->

                        <div class="timeline-item">

                            <span class="timeline-dot"></span>

                            <div>

                                <strong>
                                    Complaint Resolved
                                </strong>

                                <p>

                                    <?php

                                    if ($status === "Resolved") {
                                        echo "Complaint resolved.";
                                    } else {
                                        echo "Not completed.";
                                    }

                                    ?>

                                </p>

                            </div>

                        </div>


                    </div>

                </div>


                <!-- =========================================
                     AI INFORMATION
                ========================================== -->

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
                                Severity Prediction
                            </strong>

                            <p>

                                <?php
                                echo htmlspecialchars(
                                    $severity !== ""
                                        ? $severity
                                        : "Not available"
                                );
                                ?>

                            </p>

                        </div>

                    </div>


                    <div class="prediction-row">

                        <span>
                            Confidence
                        </span>

                        <strong>

                            <?php
                            echo $confidence;
                            ?>%

                        </strong>

                    </div>


                    <div class="prediction-row">

                        <span>
                            Recurring Complaint
                        </span>

                        <strong>

                            <?php

                            echo !empty($complaint["recurring"])
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

                            <?php
                            echo (int)(
                                $complaint["similar_count"]
                                ?? 0
                            );
                            ?>

                        </strong>

                    </div>


                    <p class="small-note">

                        Severity and similarity information is generated
                        by the FixNest AI module.

                    </p>

                </div>


            </div>


        </div>


    <?php endif; ?>

</div>

</body>

</html>