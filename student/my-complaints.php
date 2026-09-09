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
    <title>My Complaints - FixNest</title>

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

        .logout {
            background: white;
            color: #2563eb;
            padding: 9px 14px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1250px;
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

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
            overflow-x: auto;
        }

        .filters {
            display: flex;
            gap: 12px;
            margin-bottom: 22px;
            flex-wrap: wrap;
        }

        .filters input,
        .filters select {
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        .filters input {
            min-width: 260px;
        }

        .filters input:focus,
        .filters select:focus {
            border-color: #2563eb;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
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
            background: #f9fafb;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
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

        .priority-high {
            color: #dc2626;
            font-weight: bold;
        }

        .priority-medium {
            color: #d97706;
            font-weight: bold;
        }

        .view-btn {
            display: inline-block;
            background: #2563eb;
            color: white;
            text-decoration: none;
            padding: 8px 13px;
            border-radius: 6px;
            font-size: 12px;
            font-weight: bold;
        }

        .view-btn:hover {
            background: #1d4ed8;
        }

        .empty-message {
            display: none;
            text-align: center;
            padding: 25px;
            color: #6b7280;
        }

        @media (max-width: 600px) {
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

            .filters input {
                min-width: 100%;
            }

            .filters select {
                width: 100%;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h2>FixNest Student</h2>
        <a href="../index.php" class="logout">Logout</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>My Complaints</h1>
            <p>Track all complaints raised by you.</p>
        </div>

        <div class="card">

            <div class="filters">
                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search complaint ID or description..."
                    onkeyup="filterComplaints()"
                >

                <select id="statusFilter" onchange="filterComplaints()">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="In Progress">In Progress</option>
                    <option value="Resolved">Resolved</option>
                </select>
            </div>

            <table id="complaintTable">
                <thead>
                    <tr>
                        <th>Complaint ID</th>
                        <th>Room</th>
                        <th>Category</th>
                        <th>Description</th>
                        <th>Priority</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Action</th>
                    </tr>
                </thead>

                <tbody>

                    <tr>
                        <td>FX-1024</td>
                        <td>B-204</td>
                        <td>Plumbing</td>
                        <td>Water leakage in bathroom</td>
                        <td>
                            <span class="priority-high">High</span>
                        </td>
                        <td>
                            <span class="status progress">In Progress</span>
                        </td>
                        <td>06 Sep 2026</td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1024"
                                class="view-btn"
                            >
                                View
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>FX-1025</td>
                        <td>B-204</td>
                        <td>Electrical</td>
                        <td>Tube light not working</td>
                        <td>
                            <span class="priority-medium">Medium</span>
                        </td>
                        <td>
                            <span class="status pending">Pending</span>
                        </td>
                        <td>05 Sep 2026</td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1025"
                                class="view-btn"
                            >
                                View
                            </a>
                        </td>
                    </tr>

                    <tr>
                        <td>FX-1026</td>
                        <td>B-204</td>
                        <td>Carpentry</td>
                        <td>Broken cupboard door</td>
                        <td>
                            <span class="priority-medium">Medium</span>
                        </td>
                        <td>
                            <span class="status resolved">Resolved</span>
                        </td>
                        <td>02 Sep 2026</td>
                        <td>
                            <a
                                href="complaint-details.php?complaint_id=FX-1026"
                                class="view-btn"
                            >
                                View
                            </a>
                        </td>
                    </tr>

                </tbody>
            </table>

            <div id="emptyMessage" class="empty-message">
                No complaints found.
            </div>

        </div>

    </div>

    <script>
        function filterComplaints() {
            const searchValue = document
                .getElementById("searchInput")
                .value
                .toLowerCase();

            const statusValue = document
                .getElementById("statusFilter")
                .value
                .toLowerCase();

            const rows = document.querySelectorAll("#complaintTable tbody tr");
            let visibleRows = 0;

            rows.forEach(function(row) {
                const rowText = row.innerText.toLowerCase();
                const rowStatus = row
                    .querySelector(".status")
                    .innerText
                    .toLowerCase();

                const matchesSearch = rowText.includes(searchValue);
                const matchesStatus =
                    statusValue === "" || rowStatus === statusValue;

                if (matchesSearch && matchesStatus) {
                    row.style.display = "";
                    visibleRows++;
                } else {
                    row.style.display = "none";
                }
            });

            document.getElementById("emptyMessage").style.display =
                visibleRows === 0 ? "block" : "none";
        }
    </script>

</body>
</html>