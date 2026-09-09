<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "warden") {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warden Complaints - FixNest</title>

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

        .nav-links {
            display: flex;
            gap: 18px;
            align-items: center;
        }

        .nav-links a {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .logout {
            background: #dc2626;
            padding: 9px 14px;
            border-radius: 6px;
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
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .filters {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr auto;
            gap: 12px;
            margin-bottom: 25px;
        }

        input,
        select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #2563eb;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 11px 15px;
            font-size: 13px;
            font-weight: bold;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            white-space: nowrap;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-success {
            background: #16a34a;
            color: white;
        }

        .btn-danger {
            background: #dc2626;
            color: white;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1000px;
        }

        th,
        td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 14px;
        }

        th {
            background: #f9fafb;
            color: #374151;
            font-size: 13px;
        }

        tr:hover {
            background: #f9fafb;
        }

        .badge {
            display: inline-block;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .approved {
            background: #dcfce7;
            color: #166534;
        }

        .assigned {
            background: #e0e7ff;
            color: #3730a3;
        }

        .rejected {
            background: #fee2e2;
            color: #991b1b;
        }

        .high {
            background: #fee2e2;
            color: #991b1b;
        }

        .medium {
            background: #fef3c7;
            color: #92400e;
        }

        .low {
            background: #dcfce7;
            color: #166534;
        }

        .actions {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #6b7280;
            display: none;
        }

        @media (max-width: 850px) {
            .filters {
                grid-template-columns: 1fr 1fr;
            }

            .navbar {
                padding: 16px;
            }

            .nav-links {
                gap: 10px;
            }
        }

        @media (max-width: 500px) {
            .navbar {
                flex-direction: column;
                align-items: flex-start;
                gap: 14px;
            }

            .nav-links {
                flex-wrap: wrap;
            }

            .filters {
                grid-template-columns: 1fr;
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

    <div class="navbar">
        <h2>FixNest Warden</h2>

        <div class="nav-links">
            <a href="dashboard.php">Dashboard</a>
            <a href="complaints.php">Complaints</a>
            <a href="students.php">Students</a>
            <a href="reports.php">Reports</a>
            <a href="../logout.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Manage Complaints</h1>
            <p>Review student complaints and approve or reject them.</p>
        </div>

        <div class="card">

            <div class="filters">

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Search complaint, student or room..."
                    onkeyup="filterComplaints()"
                >

                <select id="statusFilter" onchange="filterComplaints()">
                    <option value="">All Status</option>
                    <option value="Pending">Pending</option>
                    <option value="Approved">Approved</option>
                    <option value="Assigned">Assigned</option>
                    <option value="Rejected">Rejected</option>
                </select>

                <select id="priorityFilter" onchange="filterComplaints()">
                    <option value="">All Priority</option>
                    <option value="High">High</option>
                    <option value="Medium">Medium</option>
                    <option value="Low">Low</option>
                </select>

                <button class="btn btn-secondary" onclick="clearFilters()">
                    Clear
                </button>

            </div>

            <div class="table-wrapper">

                <table id="complaintTable">

                    <thead>
                        <tr>
                            <th>Complaint ID</th>
                            <th>Student</th>
                            <th>Room</th>
                            <th>Category</th>
                            <th>Priority</th>
                            <th>Status</th>
                            <th>Created Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>

                    <tbody>

                        <tr>
                            <td>FX-1024</td>
                            <td>Arun Kumar</td>
                            <td>B-204</td>
                            <td>Plumbing</td>
                            <td>
                                <span class="badge high">High</span>
                            </td>
                            <td>
                                <span class="badge pending">Pending</span>
                            </td>
                            <td>06 Sep 2026</td>
                            <td>
                                <div class="actions">
                                    <button
                                        class="btn btn-success"
                                        onclick="approveComplaint('FX-1024')"
                                    >
                                        Approve
                                    </button>

                                    <button
                                        class="btn btn-danger"
                                        onclick="rejectComplaint('FX-1024')"
                                    >
                                        Reject
                                    </button>

                                    <a
                                        href="dashboard.php"
                                        class="btn btn-primary"
                                    >
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>FX-1025</td>
                            <td>Priya S</td>
                            <td>A-112</td>
                            <td>Electrical</td>
                            <td>
                                <span class="badge medium">Medium</span>
                            </td>
                            <td>
                                <span class="badge approved">Approved</span>
                            </td>
                            <td>05 Sep 2026</td>
                            <td>
                                <div class="actions">
                                    <button
                                        class="btn btn-success"
                                        onclick="approveComplaint('FX-1025')"
                                    >
                                        Approve
                                    </button>

                                    <button
                                        class="btn btn-danger"
                                        onclick="rejectComplaint('FX-1025')"
                                    >
                                        Reject
                                    </button>

                                    <a
                                        href="dashboard.php"
                                        class="btn btn-primary"
                                    >
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>FX-1026</td>
                            <td>Vignesh R</td>
                            <td>C-306</td>
                            <td>Carpentry</td>
                            <td>
                                <span class="badge medium">Medium</span>
                            </td>
                            <td>
                                <span class="badge assigned">Assigned</span>
                            </td>
                            <td>04 Sep 2026</td>
                            <td>
                                <div class="actions">
                                    <button
                                        class="btn btn-success"
                                        onclick="approveComplaint('FX-1026')"
                                    >
                                        Approve
                                    </button>

                                    <button
                                        class="btn btn-danger"
                                        onclick="rejectComplaint('FX-1026')"
                                    >
                                        Reject
                                    </button>

                                    <a
                                        href="dashboard.php"
                                        class="btn btn-primary"
                                    >
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>

                        <tr>
                            <td>FX-1027</td>
                            <td>Divya M</td>
                            <td>D-118</td>
                            <td>Cleaning</td>
                            <td>
                                <span class="badge low">Low</span>
                            </td>
                            <td>
                                <span class="badge rejected">Rejected</span>
                            </td>
                            <td>03 Sep 2026</td>
                            <td>
                                <div class="actions">
                                    <button
                                        class="btn btn-success"
                                        onclick="approveComplaint('FX-1027')"
                                    >
                                        Approve
                                    </button>

                                    <button
                                        class="btn btn-danger"
                                        onclick="rejectComplaint('FX-1027')"
                                    >
                                        Reject
                                    </button>

                                    <a
                                        href="dashboard.php"
                                        class="btn btn-primary"
                                    >
                                        View
                                    </a>
                                </div>
                            </td>
                        </tr>

                    </tbody>

                </table>

                <div class="empty" id="emptyMessage">
                    No complaints found.
                </div>

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

            const priorityValue = document
                .getElementById("priorityFilter")
                .value
                .toLowerCase();

            const rows = document.querySelectorAll(
                "#complaintTable tbody tr"
            );

            let visibleCount = 0;

            rows.forEach(function(row) {
                const rowText = row.innerText.toLowerCase();
                const statusText = row.children[5].innerText.toLowerCase();
                const priorityText = row.children[4].innerText.toLowerCase();

                const matchesSearch = rowText.includes(searchValue);

                const matchesStatus =
                    statusValue === "" ||
                    statusText.includes(statusValue);

                const matchesPriority =
                    priorityValue === "" ||
                    priorityText.includes(priorityValue);

                if (
                    matchesSearch &&
                    matchesStatus &&
                    matchesPriority
                ) {
                    row.style.display = "";
                    visibleCount++;
                } else {
                    row.style.display = "none";
                }
            });

            document.getElementById("emptyMessage").style.display =
                visibleCount === 0 ? "block" : "none";
        }

        function clearFilters() {
            document.getElementById("searchInput").value = "";
            document.getElementById("statusFilter").value = "";
            document.getElementById("priorityFilter").value = "";

            filterComplaints();
        }

        function approveComplaint(complaintId) {
            alert(
                "Complaint " +
                complaintId +
                " approved successfully. Backend integration will be added later."
            );
        }

        function rejectComplaint(complaintId) {
            const reason = prompt(
                "Enter rejection reason for " + complaintId + ":"
            );

            if (reason !== null && reason.trim() !== "") {
                alert(
                    "Complaint " +
                    complaintId +
                    " rejected.\nReason: " +
                    reason
                );
            }
        }
    </script>

</body>
</html>