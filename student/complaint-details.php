<?php
session_start();

require_once __DIR__ . "/../includes/db.php";

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

$student_id = $_SESSION["student_id"] ?? "";
$complaint_id = trim($_GET["complaint_id"] ?? "");

$complaint = null;
$error = "";

if ($student_id === "" || $complaint_id === "") {

    $error = "Invalid complaint request.";

} else {

    $sql = "SELECT
                complaint_id,
                student_id,
                hostel,
                location_type,
                room_number,
                floor_number,
                category,
                description,
                severity,
                severity_confidence,
                recurring,
                similar_count,
                status,
                created_at,
                updated_at
            FROM complaints
            WHERE complaint_id = :complaint_id
              AND student_id = :student_id";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":complaint_id" => $complaint_id,
        ":student_id" => $student_id
    ]);

    $complaint = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$complaint) {
        $error = "Complaint not found.";
    }
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
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .navbar {
            background: #2563eb;
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

        .back-link {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .container {
            max-width: 1100px;
            margin: 30px auto;
            padding: 0 20px;
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
        }

        .grid {
            display: grid;
            grid-template-columns: 1.3fr 0.7fr;
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 12px;
            padding: 25px;
            margin-bottom: 25px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .card h2 {
            margin: 0 0 20px;
            font-size: 20px;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 8px;
        }

        .full {
            grid-column: span 2;
        }

        .label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 8px;
        }

        .value {
            font-size: 15px;
            font-weight: bold;
        }

        .description {
            color: #4b5563;
            line-height: 1.7;
            font-weight: normal;
        }

        .badge {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .pending {
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

        .timeline {
            position: relative;
            padding-left: 25px;
        }

        .timeline::before {
            content: "";
            position: absolute;
            left: 5px;
            top: 5px;
            bottom: 5px;
            width: 2px;
            background: #d1d5db;
        }

        .timeline-item {
            position: relative;
            margin-bottom: 25px;
        }

        .timeline-item::before {
            content: "";
            position: absolute;
            left: -25px;
            top: 3px;
            width: 12px;
            height: 12px;
            border-radius: 50%;
            background: #2563eb;
            border: 3px solid #dbeafe;
        }

        .timeline-item h4 {
            margin: 0 0 6px;
            font-size: 15px;
        }

        .timeline-item p {
            margin: 4px 0;
            color: #6b7280;
            font-size: 13px;
        }

        .info-box {
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            padding: 16px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.6;
        }

        .btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 12px 18px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        .btn:hover {
            background: #1d4ed8;
        }

        @media (max-width: 800px) {
            .grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 500px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .full {
                grid-column: span 1;
            }

            .navbar {
                padding: 16px;
            }

            .navbar h2 {
                font-size: 18px;
            }

            .page-header h1 {
                font-size: 25px;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h2>FixNest Student</h2>
        <a href="my-complaints.php" class="back-link">← My Complaints</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Complaint Details</h1>
            <p>View your complaint status and maintenance progress.</p>
        </div>

        <div class="details-grid">

    <div class="detail-item">
        <div class="label">Complaint ID</div>
        <div class="value">
            <?= htmlspecialchars($complaint["complaint_id"]) ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Current Status</div>
        <div class="value">
            <?php
            $status_class = "";

            if ($complaint["status"] === "Pending") {
                $status_class = "pending";
            } elseif ($complaint["status"] === "In Progress") {
                $status_class = "progress";
            } elseif ($complaint["status"] === "Resolved") {
                $status_class = "resolved";
            } elseif ($complaint["status"] === "Rejected") {
                $status_class = "rejected";
            }
            ?>

            <span class="badge <?= htmlspecialchars($status_class) ?>">
                <?= htmlspecialchars($complaint["status"]) ?>
            </span>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Room Number</div>
        <div class="value">
            <?php
            if (!empty($complaint["room_number"])) {
                echo htmlspecialchars($complaint["room_number"]);
            } elseif (!empty($complaint["floor_number"])) {
                echo "Floor " . htmlspecialchars($complaint["floor_number"]);
            } else {
                echo "-";
            }
            ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Hostel</div>
        <div class="value">
            <?= htmlspecialchars($complaint["hostel"]) ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Category</div>
        <div class="value">
            <?= htmlspecialchars($complaint["category"]) ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Priority</div>
        <div class="value">
            <?= htmlspecialchars($complaint["severity"]) ?>
        </div>
    </div>

    <div class="detail-item full">
        <div class="label">Complaint Description</div>
        <div class="value description">
            <?= nl2br(htmlspecialchars($complaint["description"])) ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Created Date</div>
        <div class="value">
            <?= htmlspecialchars(date("d F Y, h:i A", strtotime($complaint["created_at"]))) ?>
        </div>
    </div>

    <div class="detail-item">
        <div class="label">Last Updated</div>
        <div class="value">
            <?= htmlspecialchars(date("d F Y, h:i A", strtotime($complaint["updated_at"]))) ?>
        </div>
    </div>

</div>

    </div>

</body>
</html>