<?php
session_start();

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = "Complaint update saved successfully! Backend team later database connection add pannuvanga.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Complaint Details - FixNest Maintenance</title>

    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f7fb;
            color: #1f2937;
        }

        .navbar {
            height: 70px;
            background: #0f766e;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 35px;
        }

        .brand {
            font-size: 24px;
            font-weight: bold;
        }

        .role {
            background: rgba(255,255,255,0.18);
            padding: 10px 16px;
            border-radius: 20px;
            font-size: 14px;
        }

        .container {
            max-width: 1150px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .back-link {
            display: inline-block;
            margin-bottom: 20px;
            color: #0f766e;
            text-decoration: none;
            font-weight: bold;
        }

        .page-title {
            margin-bottom: 25px;
        }

        .page-title h1 {
            font-size: 30px;
            margin-bottom: 8px;
        }

        .page-title p {
            color: #6b7280;
        }

        .layout {
            display: grid;
            grid-template-columns: 1.3fr 0.7fr;
            gap: 25px;
        }

        .card {
            background: white;
            border-radius: 14px;
            padding: 25px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.05);
            margin-bottom: 25px;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 20px;
            color: #111827;
        }

        .details-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }

        .detail-item {
            background: #f8fafc;
            padding: 15px;
            border-radius: 10px;
        }

        .detail-item.full {
            grid-column: span 2;
        }

        .detail-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 7px;
        }

        .detail-value {
            font-weight: bold;
            color: #111827;
        }

        .description {
            line-height: 1.7;
            color: #4b5563;
            font-weight: normal;
        }

        .status-badge {
            display: inline-block;
            background: #dbeafe;
            color: #1d4ed8;
            padding: 7px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        select,
        textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 20px;
            outline: none;
        }

        select:focus,
        textarea:focus {
            border-color: #0f766e;
        }

        textarea {
            resize: vertical;
            min-height: 130px;
        }

        .btn {
            border: none;
            padding: 13px 20px;
            border-radius: 8px;
            cursor: pointer;
            font-weight: bold;
            font-size: 14px;
        }

        .btn-primary {
            background: #0f766e;
            color: white;
        }

        .btn-primary:hover {
            background: #115e59;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            text-decoration: none;
            display: inline-block;
            margin-left: 8px;
        }

        .success-message {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
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
            background: #0f766e;
            border: 3px solid #ccfbf1;
        }

        .timeline-item h4 {
            margin-bottom: 5px;
            font-size: 15px;
        }

        .timeline-item p {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }

        .small-note {
            color: #6b7280;
            font-size: 12px;
            line-height: 1.5;
            margin-top: 15px;
        }

        @media (max-width: 800px) {
            .layout {
                grid-template-columns: 1fr;
            }

            .navbar {
                padding: 0 18px;
            }

            .brand {
                font-size: 20px;
            }
        }

        @media (max-width: 500px) {
            .details-grid {
                grid-template-columns: 1fr;
            }

            .detail-item.full {
                grid-column: span 1;
            }

            .btn-secondary {
                margin-left: 0;
                margin-top: 10px;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="brand">FixNest Maintenance</div>
        <div class="role">Maintenance Team</div>
    </div>

    <div class="container">

        <a href="dashboard.php" class="back-link">← Back to Dashboard</a>

        <div class="page-title">
            <h1>Complaint Details</h1>
            <p>View complaint information and update maintenance progress.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="success-message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="layout">

            <div>
                <div class="card">
                    <h2>Complaint Information</h2>

                    <div class="details-grid">

                        <div class="detail-item">
                            <div class="detail-label">Complaint ID</div>
                            <div class="detail-value">CMP-1001</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Current Status</div>
                            <div class="detail-value">
                                <span class="status-badge">Assigned</span>
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Student Name</div>
                            <div class="detail-value">Arun Kumar</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Student ID</div>
                            <div class="detail-value">STU001</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Room Number</div>
                            <div class="detail-value">A-204</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Block</div>
                            <div class="detail-value">Block A</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">AI Predicted Category</div>
                            <div class="detail-value">Plumbing</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Priority</div>
                            <div class="detail-value">High</div>
                        </div>

                        <div class="detail-item full">
                            <div class="detail-label">Complaint Description</div>
                            <div class="detail-value description">
                                Water leakage is happening near the bathroom tap.
                                The floor is becoming wet and slippery.
                                Please fix this issue as soon as possible.
                            </div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Created Date</div>
                            <div class="detail-value">06 September 2026</div>
                        </div>

                        <div class="detail-item">
                            <div class="detail-label">Assigned Date</div>
                            <div class="detail-value">06 September 2026</div>
                        </div>

                    </div>
                </div>

                <div class="card">
                    <h2>Complaint Timeline</h2>

                    <div class="timeline">

                        <div class="timeline-item">
                            <h4>Complaint Assigned</h4>
                            <p>Maintenance team assigned this complaint.</p>
                            <p>06 September 2026, 10:30 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Approved</h4>
                            <p>Warden approved the complaint for maintenance action.</p>
                            <p>06 September 2026, 10:15 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Raised</h4>
                            <p>Student submitted a new complaint.</p>
                            <p>06 September 2026, 09:45 AM</p>
                        </div>

                    </div>
                </div>
            </div>

            <div>
                <div class="card">
                    <h2>Update Maintenance Status</h2>

                    <form method="POST">

                        <label for="status">Complaint Status</label>

                        <select id="status" name="status" required>
                            <option value="Assigned">Assigned</option>
                            <option value="In Progress">In Progress</option>
                            <option value="Resolved">Resolved</option>
                        </select>

                        <label for="remarks">Work Remarks</label>

                        <textarea
                            id="remarks"
                            name="remarks"
                            placeholder="Enter work progress or resolution details..."
                            required
                        ></textarea>

                        <button type="submit" class="btn btn-primary">
                            Save Update
                        </button>

                        <a href="dashboard.php" class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>

                    <p class="small-note">
                        Current page frontend/demo mode-la work aagum.
                        Later backend team status and remarks database-la save pannuvanga.
                    </p>
                </div>
            </div>

        </div>

    </div>

</body>
</html>