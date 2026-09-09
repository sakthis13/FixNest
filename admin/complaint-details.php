<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $message = "Complaint details updated successfully! Backend integration will be added later.";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Complaint Details - FixNest</title>

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
            background: #111827;
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
            max-width: 1150px;
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

        .message {
            background: #dcfce7;
            color: #166534;
            border: 1px solid #86efac;
            padding: 14px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .layout {
            display: grid;
            grid-template-columns: 1.3fr 0.7fr;
            gap: 25px;
        }

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            margin-bottom: 25px;
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

        label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
        }

        select,
        textarea {
            width: 100%;
            padding: 12px;
            margin-bottom: 20px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        select:focus,
        textarea:focus {
            border-color: #2563eb;
        }

        textarea {
            min-height: 120px;
            resize: vertical;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 12px 18px;
            font-size: 14px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
            margin-left: 8px;
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
            margin-bottom: 24px;
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

        .note {
            background: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            padding: 15px;
            border-radius: 8px;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .layout {
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

            .container {
                padding: 0 15px;
            }

            .page-header h1 {
                font-size: 25px;
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
        <h2>FixNest Admin</h2>
        <a href="complaints.php" class="back-link">← Back to Complaints</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Complaint Details</h1>
            <p>Review complaint information and assign maintenance staff.</p>
        </div>

        <?php if (!empty($message)): ?>
            <div class="message">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="layout">

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
                            <div class="label">Student Name</div>
                            <div class="value">Arun Kumar</div>
                        </div>

                        <div class="detail-item">
                            <div class="label">Student ID</div>
                            <div class="value">STU001</div>
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
                            <p>Maintenance team started working on the complaint.</p>
                            <p>06 September 2026, 11:00 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Assigned</h4>
                            <p>Complaint assigned to maintenance staff.</p>
                            <p>06 September 2026, 10:30 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Approved</h4>
                            <p>Warden approved the complaint.</p>
                            <p>06 September 2026, 10:15 AM</p>
                        </div>

                        <div class="timeline-item">
                            <h4>Complaint Raised</h4>
                            <p>Student submitted the complaint.</p>
                            <p>06 September 2026, 09:45 AM</p>
                        </div>

                    </div>
                </div>

            </div>

            <div>

                <div class="card">
                    <h2>Admin Action</h2>

                    <form method="POST">

                        <label for="status">Update Status</label>

                        <select id="status" name="status" required>
                            <option value="Pending">Pending</option>
                            <option value="Approved">Approved</option>
                            <option value="Assigned">Assigned</option>
                            <option value="In Progress" selected>In Progress</option>
                            <option value="Resolved">Resolved</option>
                            <option value="Rejected">Rejected</option>
                        </select>

                        <label for="maintenance">Assign Maintenance Staff</label>

                        <select id="maintenance" name="maintenance" required>
                            <option value="">Select maintenance staff</option>
                            <option value="Maintenance Team 1">Maintenance Team 1</option>
                            <option value="Maintenance Team 2">Maintenance Team 2</option>
                            <option value="Electrical Team">Electrical Team</option>
                            <option value="Plumbing Team">Plumbing Team</option>
                        </select>

                        <label for="remarks">Admin Remarks</label>

                        <textarea
                            id="remarks"
                            name="remarks"
                            placeholder="Enter admin remarks..."
                            required
                        ></textarea>

                        <button type="submit" class="btn btn-primary">
                            Save Changes
                        </button>

                        <a href="complaints.php" class="btn btn-secondary">
                            Cancel
                        </a>

                    </form>
                </div>

                <div class="card">
                    <h2>Important Note</h2>

                    <div class="note">
                        This is currently frontend demo data.
                        Status changes and maintenance assignment will be stored
                        in the database after backend integration.
                    </div>
                </div>

            </div>

        </div>

    </div>

</body>
</html>