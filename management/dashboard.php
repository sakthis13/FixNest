<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "management") {
    header("Location: ../index.php");
    exit();
}

/* Sample monthly complaint data */
$total_complaints = 48;
$not_started = 12;
$in_progress = 18;
$completed = 18;

/* Category-wise complaint count */
$electrical = 14;
$plumbing = 12;
$carpentry = 9;
$cleaning = 7;
$other = 6;

/* Filter values */
$selected_category = $_GET["category"] ?? "All";
$selected_priority = $_GET["priority"] ?? "All";

/* Sample complaint list */
$complaints = [
    [
        "id" => "FX-1024",
        "student" => "Arun Kumar",
        "room" => "B-204",
        "category" => "Electrical",
        "description" => "Tube light not working",
        "priority" => "High"
    ],
    [
        "id" => "FX-1025",
        "student" => "Priya S",
        "room" => "A-112",
        "category" => "Plumbing",
        "description" => "Water leakage in bathroom",
        "priority" => "Medium"
    ],
    [
        "id" => "FX-1026",
        "student" => "Vignesh R",
        "room" => "C-306",
        "category" => "Carpentry",
        "description" => "Broken cupboard door",
        "priority" => "Low"
    ],
    [
        "id" => "FX-1027",
        "student" => "Karthik M",
        "room" => "D-210",
        "category" => "Cleaning",
        "description" => "Room cleaning required",
        "priority" => "Medium"
    ],
    [
        "id" => "FX-1028",
        "student" => "Divya P",
        "room" => "A-205",
        "category" => "Electrical",
        "description" => "Fan is not working",
        "priority" => "High"
    ],
    [
        "id" => "FX-1029",
        "student" => "Sanjay R",
        "room" => "B-110",
        "category" => "Other",
        "description" => "Window lock issue",
        "priority" => "Low"
    ]
];

/* Apply filters */
$filtered_complaints = [];

foreach ($complaints as $complaint) {
    $category_match =
        $selected_category === "All" ||
        $complaint["category"] === $selected_category;

    $priority_match =
        $selected_priority === "All" ||
        $complaint["priority"] === $selected_priority;

    if ($category_match && $priority_match) {
        $filtered_complaints[] = $complaint;
    }
}

/* Percentage values */
$not_started_percent = ($not_started / $total_complaints) * 100;
$in_progress_percent = ($in_progress / $total_complaints) * 100;
$completed_percent = ($completed / $total_complaints) * 100;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Management Dashboard - FixNest</title>

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
            background: #1d4ed8;
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
            color: #1d4ed8;
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

        .stats-grid {
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
            color: #2563eb;
            font-size: 12px;
        }

        .main-grid {
            display: grid;
            grid-template-columns: 1.5fr 1fr;
            gap: 24px;
            margin-bottom: 25px;
        }

        .content-card,
        .bottom-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.06);
        }

        .content-card h2,
        .bottom-card h2 {
            margin: 0 0 8px;
            font-size: 20px;
        }

        .card-subtitle {
            color: #6b7280;
            font-size: 13px;
            margin-bottom: 25px;
        }

        .chart-area {
            height: 300px;
            display: flex;
            align-items: flex-end;
            justify-content: space-around;
            gap: 18px;
            padding: 20px 10px 35px;
            border-bottom: 1px solid #d1d5db;
        }

        .chart-column {
            height: 100%;
            flex: 1;
            display: flex;
            flex-direction: column;
            justify-content: flex-end;
            align-items: center;
            gap: 10px;
            position: relative;
        }

        .bar-value {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
        }

        .bar {
            width: 55px;
            max-width: 80%;
            border-radius: 8px 8px 0 0;
            transition: 0.3s;
        }

        .bar:hover {
            opacity: 0.75;
        }

        .bar.electrical {
            height: 70%;
            background: #2563eb;
        }

        .bar.plumbing {
            height: 60%;
            background: #0ea5e9;
        }

        .bar.carpentry {
            height: 45%;
            background: #f59e0b;
        }

        .bar.cleaning {
            height: 35%;
            background: #10b981;
        }

        .bar.other {
            height: 30%;
            background: #8b5cf6;
        }

        .bar-label {
            font-size: 12px;
            color: #4b5563;
            text-align: center;
        }

        .analysis-item {
            margin-bottom: 23px;
        }

        .analysis-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 8px;
            font-size: 13px;
            font-weight: bold;
        }

        .analysis-count {
            color: #374151;
        }

        .range {
            width: 100%;
            height: 13px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
        }

        .range-fill {
            height: 100%;
            border-radius: 20px;
        }

        .range-total {
            width: 100%;
            background: #1d4ed8;
        }

        .range-not-started {
            width: <?php echo $not_started_percent; ?>%;
            background: #f59e0b;
        }

        .range-progress {
            width: <?php echo $in_progress_percent; ?>%;
            background: #0ea5e9;
        }

        .range-completed {
            width: <?php echo $completed_percent; ?>%;
            background: #16a34a;
        }

        .summary-box {
            margin-top: 25px;
            padding: 16px;
            border-radius: 10px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 13px;
            line-height: 1.6;
        }

        .filter-box {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin: 20px 0;
            padding: 18px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            min-width: 180px;
        }

        .filter-group label {
            font-size: 12px;
            font-weight: bold;
            color: #374151;
        }

        .filter-group select {
            padding: 10px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: white;
            color: #111827;
            font-size: 13px;
            outline: none;
        }

        .filter-group select:focus {
            border-color: #2563eb;
        }

        .filter-btn {
            align-self: flex-end;
            padding: 10px 18px;
            border: none;
            border-radius: 7px;
            background: #1d4ed8;
            color: white;
            font-weight: bold;
            cursor: pointer;
        }

        .filter-btn:hover {
            background: #1e40af;
        }

        .reset-btn {
            align-self: flex-end;
            padding: 10px 18px;
            border-radius: 7px;
            background: white;
            color: #1d4ed8;
            border: 1px solid #1d4ed8;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .table-wrapper {
            overflow-x: auto;
        }

        .complaint-table {
            width: 100%;
            min-width: 900px;
            border-collapse: collapse;
        }

        .complaint-table th,
        .complaint-table td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        .complaint-table th {
            background: #dbeafe;
            color: #1e40af;
            white-space: nowrap;
        }

        .complaint-table td {
            color: #374151;
        }

        .complaint-table tbody tr:hover {
            background: #f9fafb;
        }

        .priority-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
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

        .category-badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 11px;
            font-weight: bold;
        }

        .blue-badge {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .sky-badge {
            background: #e0f2fe;
            color: #0369a1;
        }

        .orange-badge {
            background: #fef3c7;
            color: #92400e;
        }

        .green-badge {
            background: #dcfce7;
            color: #166534;
        }

        .purple-badge {
            background: #ede9fe;
            color: #6d28d9;
        }

        .no-data {
            text-align: center;
            padding: 25px;
            color: #6b7280;
            font-size: 14px;
        }

        .bottom-card {
            overflow-x: auto;
        }

        .category-table {
            width: 100%;
            min-width: 600px;
            border-collapse: collapse;
        }

        .category-table th,
        .category-table td {
            padding: 14px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            font-size: 13px;
        }

        .category-table th {
            background: #dbeafe;
            color: #1e40af;
        }

        .category-table td {
            color: #374151;
        }

        @media (max-width: 1000px) {
            .stats-grid {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .main-grid {
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

            .navbar h2 {
                font-size: 18px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .content-card,
            .bottom-card {
                padding: 18px;
            }

            .chart-area {
                gap: 8px;
                padding-left: 0;
                padding-right: 0;
            }

            .bar {
                width: 35px;
            }

            .bar-label {
                font-size: 10px;
            }

            .filter-group {
                width: 100%;
            }

            .filter-btn,
            .reset-btn {
                width: 100%;
                text-align: center;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <h2>FixNest Management</h2>
        <a class="logout" href="../index.php">Logout</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Management Dashboard</h1>
            <p>Monitor monthly complaints, category details and priority analysis.</p>
        </div>

        <div class="stats-grid">

            <div class="stat-card">
                <div class="stat-label">Total Complaints</div>
                <div class="stat-value"><?php echo $total_complaints; ?></div>
                <div class="stat-note">This month</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Not Started</div>
                <div class="stat-value"><?php echo $not_started; ?></div>
                <div class="stat-note">Work not started yet</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">In Progress</div>
                <div class="stat-value"><?php echo $in_progress; ?></div>
                <div class="stat-note">Currently working</div>
            </div>

            <div class="stat-card">
                <div class="stat-label">Completed</div>
                <div class="stat-value"><?php echo $completed; ?></div>
                <div class="stat-note">Successfully resolved</div>
            </div>

        </div>

        <div class="main-grid">

            <div class="content-card">
                <h2>Monthly Complaint Analysis</h2>

                <div class="card-subtitle">
                    Category-wise complaints received this month
                </div>

                <div class="chart-area">

                    <div class="chart-column">
                        <div class="bar-value"><?php echo $electrical; ?></div>
                        <div class="bar electrical"></div>
                        <div class="bar-label">Electrical</div>
                    </div>

                    <div class="chart-column">
                        <div class="bar-value"><?php echo $plumbing; ?></div>
                        <div class="bar plumbing"></div>
                        <div class="bar-label">Plumbing</div>
                    </div>

                    <div class="chart-column">
                        <div class="bar-value"><?php echo $carpentry; ?></div>
                        <div class="bar carpentry"></div>
                        <div class="bar-label">Carpentry</div>
                    </div>

                    <div class="chart-column">
                        <div class="bar-value"><?php echo $cleaning; ?></div>
                        <div class="bar cleaning"></div>
                        <div class="bar-label">Cleaning</div>
                    </div>

                    <div class="chart-column">
                        <div class="bar-value"><?php echo $other; ?></div>
                        <div class="bar other"></div>
                        <div class="bar-label">Other</div>
                    </div>

                </div>
            </div>

            <div class="content-card">
                <h2>Complaints Analysis</h2>

                <div class="card-subtitle">
                    Current complaint status overview
                </div>

                <div class="analysis-item">
                    <div class="analysis-header">
                        <span>Total Complaints</span>
                        <span class="analysis-count">
                            <?php echo $total_complaints; ?>
                        </span>
                    </div>

                    <div class="range">
                        <div class="range-fill range-total"></div>
                    </div>
                </div>

                <div class="analysis-item">
                    <div class="analysis-header">
                        <span>Not Started</span>
                        <span class="analysis-count">
                            <?php echo $not_started; ?>
                        </span>
                    </div>

                    <div class="range">
                        <div class="range-fill range-not-started"></div>
                    </div>
                </div>

                <div class="analysis-item">
                    <div class="analysis-header">
                        <span>In Progress</span>
                        <span class="analysis-count">
                            <?php echo $in_progress; ?>
                        </span>
                    </div>

                    <div class="range">
                        <div class="range-fill range-progress"></div>
                    </div>
                </div>

                <div class="analysis-item">
                    <div class="analysis-header">
                        <span>Completed</span>
                        <span class="analysis-count">
                            <?php echo $completed; ?>
                        </span>
                    </div>

                    <div class="range">
                        <div class="range-fill range-completed"></div>
                    </div>
                </div>

                <div class="summary-box">
                    This month, <?php echo $total_complaints; ?> complaints were received.
                    Out of these, <?php echo $completed; ?> complaints have been completed,
                    <?php echo $in_progress; ?> are currently in progress and
                    <?php echo $not_started; ?> complaints are not started yet.
                </div>
            </div>

        </div>

        <div class="content-card">

            <h2>Complaint Priority and Category Filter</h2>

            <div class="card-subtitle">
                Filter complaints based on category and priority
            </div>

            <form method="GET" class="filter-box">

                <div class="filter-group">
                    <label for="category">Category</label>

                    <select name="category" id="category">
                        <option value="All" <?php echo $selected_category === "All" ? "selected" : ""; ?>>
                            All Categories
                        </option>

                        <option value="Electrical" <?php echo $selected_category === "Electrical" ? "selected" : ""; ?>>
                            Electrical
                        </option>

                        <option value="Plumbing" <?php echo $selected_category === "Plumbing" ? "selected" : ""; ?>>
                            Plumbing
                        </option>

                        <option value="Carpentry" <?php echo $selected_category === "Carpentry" ? "selected" : ""; ?>>
                            Carpentry
                        </option>

                        <option value="Cleaning" <?php echo $selected_category === "Cleaning" ? "selected" : ""; ?>>
                            Cleaning
                        </option>

                        <option value="Other" <?php echo $selected_category === "Other" ? "selected" : ""; ?>>
                            Other
                        </option>
                    </select>
                </div>

                <div class="filter-group">
                    <label for="priority">Priority</label>

                    <select name="priority" id="priority">
                        <option value="All" <?php echo $selected_priority === "All" ? "selected" : ""; ?>>
                            All Priorities
                        </option>

                        <option value="High" <?php echo $selected_priority === "High" ? "selected" : ""; ?>>
                            High
                        </option>

                        <option value="Medium" <?php echo $selected_priority === "Medium" ? "selected" : ""; ?>>
                            Medium
                        </option>

                        <option value="Low" <?php echo $selected_priority === "Low" ? "selected" : ""; ?>>
                            Low
                        </option>
                    </select>
                </div>

                <button type="submit" class="filter-btn">
                    Apply Filter
                </button>

                <a href="dashboard.php" class="reset-btn">
                    Reset
                </a>

            </form>

        </div>

        <div class="content-card">

            <h2>Complaint List</h2>

            <div class="card-subtitle">
                Filtered complaints based on selected category and priority
            </div>

            <div class="table-wrapper">
                <table class="complaint-table">

                    <thead>
                        <tr>
                            <th>Complaint ID</th>
                            <th>Student</th>
                            <th>Room</th>
                            <th>Category</th>
                            <th>Description</th>
                            <th>Priority</th>
                        </tr>
                    </thead>

                    <tbody>

                        <?php if (count($filtered_complaints) > 0): ?>

                            <?php foreach ($filtered_complaints as $complaint): ?>

                                <tr>
                                    <td><?php echo $complaint["id"]; ?></td>

                                    <td><?php echo $complaint["student"]; ?></td>

                                    <td><?php echo $complaint["room"]; ?></td>

                                    <td>
                                        <?php
                                        $category_class = "blue-badge";

                                        if ($complaint["category"] === "Plumbing") {
                                            $category_class = "sky-badge";
                                        } elseif ($complaint["category"] === "Carpentry") {
                                            $category_class = "orange-badge";
                                        } elseif ($complaint["category"] === "Cleaning") {
                                            $category_class = "green-badge";
                                        } elseif ($complaint["category"] === "Other") {
                                            $category_class = "purple-badge";
                                        }
                                        ?>

                                        <span class="category-badge <?php echo $category_class; ?>">
                                            <?php echo $complaint["category"]; ?>
                                        </span>
                                    </td>

                                    <td><?php echo $complaint["description"]; ?></td>

                                    <td>
                                        <?php
                                        $priority_class = "priority-low";

                                        if ($complaint["priority"] === "High") {
                                            $priority_class = "priority-high";
                                        } elseif ($complaint["priority"] === "Medium") {
                                            $priority_class = "priority-medium";
                                        }
                                        ?>

                                        <span class="priority-badge <?php echo $priority_class; ?>">
                                            <?php echo $complaint["priority"]; ?>
                                        </span>
                                    </td>
                                </tr>

                            <?php endforeach; ?>

                        <?php else: ?>

                            <tr>
                                <td colspan="6" class="no-data">
                                    No complaints found for the selected filters.
                                </td>
                            </tr>

                        <?php endif; ?>

                    </tbody>

                </table>
            </div>

        </div>

    </div>

</body>
</html>