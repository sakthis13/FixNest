<?php
session_start();

/*
|--------------------------------------------------------------------------
| Warden Login Details
|--------------------------------------------------------------------------
| Boys Warden:
| username: boyswarden
| password: boys123
|
| Girls Warden:
| username: girlswarden
| password: girls123
|--------------------------------------------------------------------------
*/

$warden_username = $_SESSION["username"] ?? "boyswarden";
$warden_role = $_SESSION["role"] ?? "warden";

if ($warden_role !== "warden") {
    header("Location: ../index.php");
    exit;
}

/*
|--------------------------------------------------------------------------
| Warden Hostel Type
|--------------------------------------------------------------------------
| Login session-la hostel_type set pannirundha adha use pannum.
| Illana username base panni identify pannum.
|--------------------------------------------------------------------------
*/

if (isset($_SESSION["hostel_type"])) {
    $warden_hostel = $_SESSION["hostel_type"];
} else {
    if ($warden_username === "girlswarden") {
        $warden_hostel = "Girls Hostel";
    } else {
        $warden_hostel = "Boys Hostel";
    }
}

/*
|--------------------------------------------------------------------------
| Demo Complaints
|--------------------------------------------------------------------------
| Backend/database connect pannumbodhu indha array-ku badhila DB data use
| pannalam.
|--------------------------------------------------------------------------
*/

$complaints = [
    [
        "id" => 1,
        "student" => "Student User",
        "hostel" => "Boys Hostel",
        "location" => "Room 204",
        "category" => "Electrical",
        "description" => "Room fan is not working properly.",
        "priority" => "High",
        "status" => "To-do",
        "date" => "06 Sep 2026"
    ],
    [
        "id" => 2,
        "student" => "Arun Kumar",
        "hostel" => "Girls Hostel",
        "location" => "Common Bathroom - 2nd Floor",
        "category" => "Plumbing",
        "description" => "Water leakage in the common bathroom.",
        "priority" => "Urgent",
        "status" => "In Progress",
        "date" => "06 Sep 2026"
    ],
    [
        "id" => 3,
        "student" => "Karthik",
        "hostel" => "Boys Hostel",
        "location" => "Lobby - Ground Floor",
        "category" => "Cleaning",
        "description" => "Lobby cleaning is required.",
        "priority" => "Low",
        "status" => "Complete",
        "date" => "05 Sep 2026"
    ],
    [
        "id" => 4,
        "student" => "Priya",
        "hostel" => "Girls Hostel",
        "location" => "Study Hall",
        "category" => "Furniture",
        "description" => "Study table is damaged.",
        "priority" => "Medium",
        "status" => "To-do",
        "date" => "05 Sep 2026"
    ],
    [
        "id" => 5,
        "student" => "Rahul",
        "hostel" => "Boys Hostel",
        "location" => "Room 105",
        "category" => "Water",
        "description" => "No water supply in the room.",
        "priority" => "Urgent",
        "status" => "In Progress",
        "date" => "04 Sep 2026"
    ]
];

/*
|--------------------------------------------------------------------------
| Show only logged-in warden hostel complaints
|--------------------------------------------------------------------------
*/

$hostel_complaints = [];

foreach ($complaints as $complaint) {
    if ($complaint["hostel"] === $warden_hostel) {
        $hostel_complaints[] = $complaint;
    }
}

$selected_priority = $_GET["priority"] ?? "All";
$selected_status = $_GET["status"] ?? "All";

$filtered_complaints = [];

foreach ($hostel_complaints as $complaint) {
    $priority_match =
        $selected_priority === "All" ||
        $complaint["priority"] === $selected_priority;

    $status_match =
        $selected_status === "All" ||
        $complaint["status"] === $selected_status;

    if ($priority_match && $status_match) {
        $filtered_complaints[] = $complaint;
    }
}

$total_complaints = count($hostel_complaints);
$todo_count = 0;
$progress_count = 0;
$complete_count = 0;

foreach ($hostel_complaints as $complaint) {
    if ($complaint["status"] === "To-do") {
        $todo_count++;
    }

    if ($complaint["status"] === "In Progress") {
        $progress_count++;
    }

    if ($complaint["status"] === "Complete") {
        $complete_count++;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Warden Dashboard - FixNest</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f5f7fb;
            color: #172033;
        }

        .navbar {
            background: #ffffff;
            border-bottom: 1px solid #e5e7eb;
            padding: 18px 6%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
        }

        .brand {
            color: #2563eb;
            font-size: 25px;
            font-weight: 800;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .warden-name {
            font-size: 14px;
            color: #4b5563;
        }

        .logout-btn {
            text-decoration: none;
            background: #fee2e2;
            color: #b91c1c;
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 700;
        }

        .container {
            width: 88%;
            max-width: 1400px;
            margin: 35px auto;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
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

        .hostel-badge {
            background: #dbeafe;
            color: #1d4ed8;
            padding: 12px 18px;
            border-radius: 10px;
            font-weight: 700;
            white-space: nowrap;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 28px;
        }

        .stat-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
        }

        .stat-label {
            color: #6b7280;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .stat-number {
            font-size: 32px;
            font-weight: 800;
        }

        .stat-blue .stat-number {
            color: #2563eb;
        }

        .stat-orange .stat-number {
            color: #d97706;
        }

        .stat-purple .stat-number {
            color: #7c3aed;
        }

        .stat-green .stat-number {
            color: #16a34a;
        }

        .filter-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            padding: 22px;
            margin-bottom: 25px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 16px;
        }

        .filter-form {
            display: grid;
            grid-template-columns: 1fr 1fr auto;
            gap: 15px;
            align-items: end;
        }

        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 700;
            margin-bottom: 8px;
            color: #374151;
        }

        .form-group select {
            width: 100%;
            padding: 12px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            background: #ffffff;
            font-size: 14px;
        }

        .filter-btn {
            border: none;
            background: #2563eb;
            color: #ffffff;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: 700;
            cursor: pointer;
        }

        .reset-btn {
            text-decoration: none;
            background: #f3f4f6;
            color: #374151;
            padding: 12px 22px;
            border-radius: 8px;
            font-weight: 700;
            display: inline-block;
            margin-left: 8px;
        }

        .complaints-card {
            background: #ffffff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            overflow: hidden;
        }

        .complaints-header {
            padding: 22px;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
        }

        .complaints-header h2 {
            margin: 0;
            font-size: 20px;
        }

        .complaints-header p {
            margin: 6px 0 0;
            color: #6b7280;
            font-size: 14px;
        }

        .complaint-list {
            display: flex;
            flex-direction: column;
        }

        .complaint-item {
            padding: 22px;
            border-bottom: 1px solid #e5e7eb;
        }

        .complaint-item:last-child {
            border-bottom: none;
        }

        .complaint-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 20px;
        }

        .complaint-title {
            font-size: 17px;
            font-weight: 800;
            margin-bottom: 8px;
        }

        .complaint-meta {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.8;
        }

        .complaint-description {
            margin-top: 12px;
            color: #374151;
            line-height: 1.6;
            font-size: 14px;
        }

        .badges {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            justify-content: flex-end;
        }

        .badge {
            padding: 7px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 700;
        }

        .priority-low {
            background: #dcfce7;
            color: #166534;
        }

        .priority-medium {
            background: #fef3c7;
            color: #92400e;
        }

        .priority-high {
            background: #ffedd5;
            color: #c2410c;
        }

        .priority-urgent {
            background: #fee2e2;
            color: #b91c1c;
        }

        .status-todo {
            background: #e5e7eb;
            color: #374151;
        }

        .status-progress {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .status-complete {
            background: #dcfce7;
            color: #166534;
        }

        .complaint-actions {
            margin-top: 18px;
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }

        .update-form {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            align-items: center;
        }

        .update-form select {
            padding: 9px 12px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: #ffffff;
            font-size: 13px;
        }

        .update-btn {
            border: none;
            background: #111827;
            color: #ffffff;
            padding: 10px 15px;
            border-radius: 7px;
            cursor: pointer;
            font-size: 13px;
            font-weight: 700;
        }

        .empty-state {
            padding: 55px 20px;
            text-align: center;
            color: #6b7280;
        }

        .empty-state h3 {
            margin: 0 0 8px;
            color: #374151;
        }

        @media (max-width: 900px) {
            .stats-grid {
                grid-template-columns: repeat(2, 1fr);
            }

            .filter-form {
                grid-template-columns: 1fr;
            }

            .page-header,
            .complaint-top {
                flex-direction: column;
            }

            .badges {
                justify-content: flex-start;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 5%;
                align-items: flex-start;
                flex-direction: column;
            }

            .nav-right {
                width: 100%;
                justify-content: space-between;
            }

            .container {
                width: 92%;
                margin: 25px auto;
            }

            .stats-grid {
                grid-template-columns: 1fr;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .hostel-badge {
                white-space: normal;
            }

            .complaint-item,
            .complaints-header,
            .filter-card {
                padding: 17px;
            }
        }
    </style>
</head>

<body>

    <nav class="navbar">
        <div class="brand">FixNest</div>

        <div class="nav-right">
            <div class="warden-name">
                Logged in as:
                <strong>
                    <?php echo htmlspecialchars($warden_username); ?>
                </strong>
            </div>

            <a class="logout-btn" href="../logout.php">Logout</a>
        </div>
    </nav>

    <main class="container">

        <div class="page-header">
            <div>
                <h1>Warden Dashboard</h1>
                <p>Manage and monitor complaints from your hostel.</p>
            </div>

            <div class="hostel-badge">
                <?php echo htmlspecialchars($warden_hostel); ?>
            </div>
        </div>

        <section class="stats-grid">

            <div class="stat-card stat-blue">
                <div class="stat-label">Total Complaints</div>
                <div class="stat-number">
                    <?php echo $total_complaints; ?>
                </div>
            </div>

            <div class="stat-card stat-orange">
                <div class="stat-label">To-do</div>
                <div class="stat-number">
                    <?php echo $todo_count; ?>
                </div>
            </div>

            <div class="stat-card stat-purple">
                <div class="stat-label">In Progress</div>
                <div class="stat-number">
                    <?php echo $progress_count; ?>
                </div>
            </div>

            <div class="stat-card stat-green">
                <div class="stat-label">Complete</div>
                <div class="stat-number">
                    <?php echo $complete_count; ?>
                </div>
            </div>

        </section>

        <section class="filter-card">
            <div class="filter-title">Filter Complaints</div>

            <form class="filter-form" method="GET">

                <div class="form-group">
                    <label for="priority">Priority</label>

                    <select name="priority" id="priority">
                        <option value="All" <?php echo $selected_priority === "All" ? "selected" : ""; ?>>
                            All Priorities
                        </option>

                        <option value="Urgent" <?php echo $selected_priority === "Urgent" ? "selected" : ""; ?>>
                            Urgent
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

                <div class="form-group">
                    <label for="status">Complaint Status</label>

                    <select name="status" id="status">
                        <option value="All" <?php echo $selected_status === "All" ? "selected" : ""; ?>>
                            All Status
                        </option>

                        <option value="To-do" <?php echo $selected_status === "To-do" ? "selected" : ""; ?>>
                            To-do
                        </option>

                        <option value="In Progress" <?php echo $selected_status === "In Progress" ? "selected" : ""; ?>>
                            In Progress
                        </option>

                        <option value="Complete" <?php echo $selected_status === "Complete" ? "selected" : ""; ?>>
                            Complete
                        </option>
                    </select>
                </div>

                <div>
                    <button class="filter-btn" type="submit">
                        Apply Filter
                    </button>

                    <a class="reset-btn" href="dashboard.php">
                        Reset
                    </a>
                </div>

            </form>
        </section>

        <section class="complaints-card">

            <div class="complaints-header">
                <div>
                    <h2><?php echo htmlspecialchars($warden_hostel); ?> Complaints</h2>

                    <p>
                        Showing <?php echo count($filtered_complaints); ?>
                        complaint(s) based on selected filters.
                    </p>
                </div>
            </div>

            <div class="complaint-list">

                <?php if (count($filtered_complaints) === 0): ?>

                    <div class="empty-state">
                        <h3>No complaints found</h3>
                        <p>No complaints match the selected priority or status.</p>
                    </div>

                <?php else: ?>

                    <?php foreach ($filtered_complaints as $complaint): ?>

                        <?php
                        $priority_class = strtolower($complaint["priority"]);

                        if ($complaint["status"] === "To-do") {
                            $status_class = "status-todo";
                        } elseif ($complaint["status"] === "In Progress") {
                            $status_class = "status-progress";
                        } else {
                            $status_class = "status-complete";
                        }
                        ?>

                        <article class="complaint-item">

                            <div class="complaint-top">

                                <div>
                                    <div class="complaint-title">
                                        #<?php echo $complaint["id"]; ?>
                                        -
                                        <?php echo htmlspecialchars($complaint["category"]); ?>
                                    </div>

                                    <div class="complaint-meta">
                                        <strong>Student:</strong>
                                        <?php echo htmlspecialchars($complaint["student"]); ?>
                                        <br>

                                        <strong>Location:</strong>
                                        <?php echo htmlspecialchars($complaint["location"]); ?>
                                        <br>

                                        <strong>Date:</strong>
                                        <?php echo htmlspecialchars($complaint["date"]); ?>
                                    </div>
                                </div>

                                <div class="badges">
                                    <span class="badge priority-<?php echo $priority_class; ?>">
                                        <?php echo htmlspecialchars($complaint["priority"]); ?>
                                    </span>

                                    <span class="badge <?php echo $status_class; ?>">
                                        <?php echo htmlspecialchars($complaint["status"]); ?>
                                    </span>
                                </div>

                            </div>

                            <div class="complaint-description">
                                <?php echo htmlspecialchars($complaint["description"]); ?>
                            </div>

                            <div class="complaint-actions">

                                <form class="update-form" method="POST">

                                    <input
                                        type="hidden"
                                        name="complaint_id"
                                        value="<?php echo $complaint["id"]; ?>"
                                    >

                                    <select name="priority">
                                        <option value="Urgent" <?php echo $complaint["priority"] === "Urgent" ? "selected" : ""; ?>>
                                            Urgent Priority
                                        </option>

                                        <option value="High" <?php echo $complaint["priority"] === "High" ? "selected" : ""; ?>>
                                            High Priority
                                        </option>

                                        <option value="Medium" <?php echo $complaint["priority"] === "Medium" ? "selected" : ""; ?>>
                                            Medium Priority
                                        </option>

                                        <option value="Low" <?php echo $complaint["priority"] === "Low" ? "selected" : ""; ?>>
                                            Low Priority
                                        </option>
                                    </select>

                                    <select name="status">
                                        <option value="To-do" <?php echo $complaint["status"] === "To-do" ? "selected" : ""; ?>>
                                            To-do
                                        </option>

                                        <option value="In Progress" <?php echo $complaint["status"] === "In Progress" ? "selected" : ""; ?>>
                                            In Progress
                                        </option>

                                        <option value="Complete" <?php echo $complaint["status"] === "Complete" ? "selected" : ""; ?>>
                                            Complete
                                        </option>
                                    </select>

                                    <button class="update-btn" type="submit">
                                        Update
                                    </button>

                                </form>

                            </div>

                        </article>

                    <?php endforeach; ?>

                <?php endif; ?>

            </div>

        </section>

    </main>

</body>
</html>