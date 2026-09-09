<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
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

        <div class="grid">

            <div>

                <div class="card">
                    <h2>Complaint Information</h2>

                    <div class="details-grid">

                        <div class="detail-item">
                            <div class="label">Complaint ID</div>
                            <div class="value">FX-1024</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Current Status</div>
                            <div class="value">
                                <span class="badge progress">In Progress</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Room Number</div>
                            <div class="value">B-204</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Block</div>
                            <div class="value">Block B</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">AI Predicted Category</div>
                            <div class="value">Plumbing</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Priority</div>
                            <div class="value">High</div>
                        </div>

                        <div class="detail-item full">
                            <div class="label">Complaint Description</div>
                            <div class="value description">
                                Water leakage is happening in the bathroom.
                                The floor is becoming wet and slippery.
                                Please fix this issue as soon as possible.
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Created Date</div>
                            <div class="value">06 September 2026</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Last Updated</div>
                            <div class="value">06 September 2026</div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <h2>Complaint Timeline</h2>

                    <div class="timeline">

                        <div class="timeline-item">
                            <h4>Work In Progress</h4>
                            <p>Maintenance team started working on your complaint.</p>
                            <p>06 September 2026, 11:00 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Assigned</h4>
                            <p>Your complaint was assigned to the maintenance team.</p>
                            <p>06 September 2026, 10:30 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Approved</h4>
                            <p>Warden approved your complaint.</p>
                            <p>06 September 2026, 10:15 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Raised</h4>
                            <p>You submitted this complaint successfully.</p>
                            <p>06 September 2026, 09:45 AM</p>
                        </div>

                    </div>
                </div>

            </div>

            <div>

                <div class="card">
                    <h2>Maintenance Remarks</h2>

                    <p style="color:#4b5563; line-height:1.7;">
                        Maintenance team inspected the bathroom leakage.
                        Required plumbing materials are being arranged.
                    </p>

                    <div class="info-box">
                        You will be notified when the complaint is resolved.
                    </div>
                </div>

                <div class="card">
                    <h2>Need More Help?</h2>

                    <p style="color:#6b7280; line-height:1.6;">
                        If the issue is urgent or the status is not updated,
                        please contact your hostel warden.
                    </p>

                    <a href="my-complaints.php" class="btn">
                        Back to Complaints
                    </a>
                </div>

            </div>

        </div>

    </div>

</body>
</html>