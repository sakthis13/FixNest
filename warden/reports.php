<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - FixNest Warden</title>

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
            background: #0f766e;
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
            color: #0f766e;
            padding: 9px 14px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 1400px;
            margin: auto;
            padding: 30px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .filter-section {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
        }

        select {
            padding: 11px 13px;
            min-width: 180px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: white;
            color: #111827;
            outline: none;
        }

        select:focus {
            border-color: #0f766e;
        }

        .report-grid {
            display: grid;
            grid-template-columns: repeat(4, minmax(0, 1fr));
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .stat-label {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 12px;
        }

        .stat-value {
            font-size: 30px;
            font-weight: bold;
            color: #111827;
        }

        .stat-note {
            margin-top: 8px;
            color: #0f766e;
            font-size: 12px;
        }

        .content-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .card h2 {
            margin: 0 0 20px;
            font-size: 19px;
        }

        .bar-row {
            margin-bottom: 20px;
        }

        .bar-heading {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            margin-bottom: 8px;
            font-size: 13px;
        }

        .bar-heading span:first-child {
            color: #374151;
        }

        .bar-heading strong {
            color: #111827;
        }

        .bar-background {
            width: 100%;
            height: 10px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            background: #0f766e;
            border-radius: 20px;
        }

        .category-table {
            width: 100%;
            border-collapse: collapse;
        }

        .category-table th,
        .category-table td {
            padding: 13px 8px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .category-table th {
            color: #115e59;
            background: #ccfbf1;
        }

        .category-table td {
            color: #4b5563;
        }

        .status-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .resolved {
            background: #dcfce7;
            color: #166534;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        .in-progress {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .export-btn {
            margin-top: 22px;
            padding: 11px 16px;
            border: none;
            border-radius: 7px;
            background: #0f766e;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .export-btn:hover {
            background: #115e59;
        }

        .info-box {
            margin-top: 24px;
            padding: 16px;
            border-radius: 10px;
            background: #f0fdfa;
            border: 1px solid #99f6e4;
            color: #115e59;
            font-size: 13px;
            line-height: 1.6;
        }

        @media (max-width: 1000px) {
            .report-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .content-grid {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .container {
                padding: 20px 15px;
            }

            .navbar {
                padding: 16px 18px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .report-grid {
                grid-template-columns: 1fr;
            }

            .filter-section,
            .filter-group,
            select {
                width: 100%;
                min-width: 0;
            }
        }
    </style>
</head>

<body>

<div class="navbar">
    <h2>FixNest Warden</h2>
    <a class="logout" href="../index.php">Logout</a>
</div>

<div class="container">

    <div class="page-header">

        <div>
            <h1>Reports & Analytics</h1>
            <p>View complaint statistics and hostel maintenance performance.</p>
        </div>

        <div class="filter-section">

            <div class="filter-group">
                <label for="periodFilter">Report Period</label>

                <select id="periodFilter" onchange="changePeriod()">
                    <option value="month">This Month</option>
                    <option value="week">This Week</option>
                    <option value="year">This Year</option>
                </select>
            </div>

        </div>

    </div>

    <div class="report-grid">

        <div class="stat-card">
            <div class="stat-label">Total Complaints</div>
            <div class="stat-value" id="totalComplaints">48</div>
            <div class="stat-note">All complaints received</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Resolved Complaints</div>
            <div class="stat-value" id="resolvedComplaints">31</div>
            <div class="stat-note">64.5% resolution rate</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">Pending Complaints</div>
            <div class="stat-value" id="pendingComplaints">9</div>
            <div class="stat-note">Waiting for action</div>
        </div>

        <div class="stat-card">
            <div class="stat-label">In Progress</div>
            <div class="stat-value" id="progressComplaints">8</div>
            <div class="stat-note">Currently assigned</div>
        </div>

    </div>

    <div class="content-grid">

        <div class="card">
            <h2>Complaint Status Overview</h2>

            <div class="bar-row">
                <div class="bar-heading">
                    <span>Resolved</span>
                    <strong>31</strong>
                </div>

                <div class="bar-background">
                    <div class="bar-fill" style="width: 65%;"></div>
                </div>
            </div>

            <div class="bar-row">
                <div class="bar-heading">
                    <span>Pending</span>
                    <strong>9</strong>
                </div>

                <div class="bar-background">
                    <div class="bar-fill" style="width: 19%;"></div>
                </div>
            </div>

            <div class="bar-row">
                <div class="bar-heading">
                    <span>In Progress</span>
                    <strong>8</strong>
                </div>

                <div class="bar-background">
                    <div class="bar-fill" style="width: 16%;"></div>
                </div>
            </div>

            <button
                type="button"
                class="export-btn"
                onclick="exportReport()"
            >
                Export Report
            </button>
        </div>

        <div class="card">
            <h2>Complaints by Category</h2>

            <table class="category-table">
                <thead>
                    <tr>
                        <th>Category</th>
                        <th>Total</th>
                        <th>Status</th>
                    </tr>
                </thead>

                <tbody>
                    <tr>
                        <td>Plumbing</td>
                        <td>18</td>
                        <td>
                            <span class="status-badge resolved">Resolved</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Electrical</td>
                        <td>12</td>
                        <td>
                            <span class="status-badge in-progress">In Progress</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Cleaning</td>
                        <td>10</td>
                        <td>
                            <span class="status-badge pending">Pending</span>
                        </td>
                    </tr>

                    <tr>
                        <td>Carpentry</td>
                        <td>8</td>
                        <td>
                            <span class="status-badge resolved">Resolved</span>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

    </div>

    <div class="info-box">
        This report currently displays sample frontend data.
        Real-time complaint statistics will be connected to the backend
        database and API later.
    </div>

</div>

<script>
    function changePeriod() {
        const selectedPeriod =
            document.getElementById("periodFilter").value;

        if (selectedPeriod === "week") {
            document.getElementById("totalComplaints").innerText = "12";
            document.getElementById("resolvedComplaints").innerText = "8";
            document.getElementById("pendingComplaints").innerText = "2";
            document.getElementById("progressComplaints").innerText = "2";
        } else if (selectedPeriod === "year") {
            document.getElementById("totalComplaints").innerText = "426";
            document.getElementById("resolvedComplaints").innerText = "292";
            document.getElementById("pendingComplaints").innerText = "71";
            document.getElementById("progressComplaints").innerText = "63";
        } else {
            document.getElementById("totalComplaints").innerText = "48";
            document.getElementById("resolvedComplaints").innerText = "31";
            document.getElementById("pendingComplaints").innerText = "9";
            document.getElementById("progressComplaints").innerText = "8";
        }
    }

    function exportReport() {
        alert(
            "Report export will be connected to the backend later."
        );
    }
</script>

</body>
</html>