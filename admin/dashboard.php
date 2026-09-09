<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit;
}

/* Demo data */

if (!isset($_SESSION["admin_students"])) {
    $_SESSION["admin_students"] = [
        [
            "id" => 1,
            "name" => "Arun Kumar",
            "username" => "STU001",
            "student_id" => "STU001",
            "department" => "Computer Science",
            "email" => "arun@gmail.com",
            "phone" => "9876543210",
            "hostel_type" => "Boys Hostel"
        ],
        [
            "id" => 2,
            "name" => "Priya S",
            "username" => "STU002",
            "student_id" => "STU002",
            "department" => "Information Technology",
            "email" => "priya@gmail.com",
            "phone" => "9876543211",
            "hostel_type" => "Girls Hostel"
        ]
    ];
}

if (!isset($_SESSION["admin_wardens"])) {
    $_SESSION["admin_wardens"] = [
        [
            "id" => 1,
            "name" => "Ramesh",
            "username" => "boyswarden",
            "email" => "boyswarden@gmail.com",
            "phone" => "9876500001",
            "hostel_type" => "Boys Hostel"
        ],
        [
            "id" => 2,
            "name" => "Meena",
            "username" => "girlswarden",
            "email" => "girlswarden@gmail.com",
            "phone" => "9876500002",
            "hostel_type" => "Girls Hostel"
        ]
    ];
}

if (!isset($_SESSION["admin_maintenance"])) {
    $_SESSION["admin_maintenance"] = [
        [
            "id" => 1,
            "name" => "Suresh",
            "username" => "maintenance1",
            "email" => "maintenance@gmail.com",
            "phone" => "9876511111",
            "specialization" => "Electrical"
        ],
        [
            "id" => 2,
            "name" => "Karthik",
            "username" => "maintenance2",
            "email" => "maintenance2@gmail.com",
            "phone" => "9876522222",
            "specialization" => "Plumbing"
        ]
    ];
}

$students = &$_SESSION["admin_students"];
$wardens = &$_SESSION["admin_wardens"];
$maintenance_staff = &$_SESSION["admin_maintenance"];

$active_tab = $_GET["tab"] ?? "students";
$edit_type = $_GET["edit_type"] ?? "";
$edit_id = isset($_GET["edit_id"]) ? (int) $_GET["edit_id"] : 0;

$message = "";
$error = "";

/* Delete record */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["delete_record"])) {
    $delete_type = $_POST["delete_type"];
    $delete_id = (int) $_POST["delete_id"];

    if ($delete_type === "student") {
        foreach ($students as $key => $student) {
            if ($student["id"] === $delete_id) {
                unset($students[$key]);
                $message = "Student deleted successfully.";
                break;
            }
        }

        $students = array_values($students);
        $active_tab = "students";
    }

    if ($delete_type === "warden") {
        foreach ($wardens as $key => $warden) {
            if ($warden["id"] === $delete_id) {
                unset($wardens[$key]);
                $message = "Warden deleted successfully.";
                break;
            }
        }

        $wardens = array_values($wardens);
        $active_tab = "wardens";
    }

    if ($delete_type === "maintenance") {
        foreach ($maintenance_staff as $key => $staff) {
            if ($staff["id"] === $delete_id) {
                unset($maintenance_staff[$key]);
                $message = "Maintenance staff deleted successfully.";
                break;
            }
        }

        $maintenance_staff = array_values($maintenance_staff);
        $active_tab = "maintenance";
    }
}

/* Add / update student */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_student"])) {
    $student_id = (int) ($_POST["record_id"] ?? 0);

    $unique_student_id = trim($_POST["student_id"]);

    $student_data = [
        "id" => $student_id > 0 ? $student_id : time(),
        "name" => trim($_POST["name"]),
        "username" => $unique_student_id,
        "student_id" => $unique_student_id,
        "department" => trim($_POST["department"]),
        "email" => trim($_POST["email"]),
        "phone" => trim($_POST["phone"]),
        "hostel_type" => $_POST["hostel_type"]
    ];

    if (
        $student_data["name"] === "" ||
        $student_data["student_id"] === "" ||
        $student_data["email"] === ""
    ) {
        $error = "Please fill all required student fields.";
    } else {
        $updated = false;

        foreach ($students as $key => $student) {
            if ($student["id"] === $student_id && $student_id > 0) {
                $students[$key] = $student_data;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $message = "Student updated successfully.";
        } else {
            $students[] = $student_data;
            $message = "Student added successfully.";
        }

        $active_tab = "students";
    }
}

/* Add / update warden */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_warden"])) {
    $warden_id = (int) ($_POST["record_id"] ?? 0);

    $warden_data = [
        "id" => $warden_id > 0 ? $warden_id : time(),
        "name" => trim($_POST["name"]),
        "username" => trim($_POST["username"]),
        "email" => trim($_POST["email"]),
        "phone" => trim($_POST["phone"]),
        "hostel_type" => $_POST["hostel_type"]
    ];

    if (
        $warden_data["name"] === "" ||
        $warden_data["username"] === "" ||
        $warden_data["email"] === ""
    ) {
        $error = "Please fill all required warden fields.";
    } else {
        $updated = false;

        foreach ($wardens as $key => $warden) {
            if ($warden["id"] === $warden_id && $warden_id > 0) {
                $wardens[$key] = $warden_data;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $message = "Warden updated successfully.";
        } else {
            $wardens[] = $warden_data;
            $message = "Warden added successfully.";
        }

        $active_tab = "wardens";
    }
}

/* Add / update maintenance staff */

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["save_maintenance"])) {
    $maintenance_id = (int) ($_POST["record_id"] ?? 0);

    $maintenance_data = [
        "id" => $maintenance_id > 0 ? $maintenance_id : time(),
        "name" => trim($_POST["name"]),
        "username" => trim($_POST["username"]),
        "email" => trim($_POST["email"]),
        "phone" => trim($_POST["phone"]),
        "specialization" => trim($_POST["specialization"])
    ];

    if (
        $maintenance_data["name"] === "" ||
        $maintenance_data["username"] === "" ||
        $maintenance_data["email"] === ""
    ) {
        $error = "Please fill all required maintenance fields.";
    } else {
        $updated = false;

        foreach ($maintenance_staff as $key => $staff) {
            if ($staff["id"] === $maintenance_id && $maintenance_id > 0) {
                $maintenance_staff[$key] = $maintenance_data;
                $updated = true;
                break;
            }
        }

        if ($updated) {
            $message = "Maintenance staff updated successfully.";
        } else {
            $maintenance_staff[] = $maintenance_data;
            $message = "Maintenance staff added successfully.";
        }

        $active_tab = "maintenance";
    }
}

/* Find edit record */

$edit_record = null;

if ($edit_id > 0) {
    if ($edit_type === "student") {
        foreach ($students as $student) {
            if ($student["id"] === $edit_id) {
                $edit_record = $student;
                break;
            }
        }
    }

    if ($edit_type === "warden") {
        foreach ($wardens as $warden) {
            if ($warden["id"] === $edit_id) {
                $edit_record = $warden;
                break;
            }
        }
    }

    if ($edit_type === "maintenance") {
        foreach ($maintenance_staff as $staff) {
            if ($staff["id"] === $edit_id) {
                $edit_record = $staff;
                break;
            }
        }
    }
}

/* Student hostel filter */

$hostel_filter = $_GET["hostel"] ?? "All";
$filtered_students = [];

foreach ($students as $student) {
    if (
        $hostel_filter === "All" ||
        $student["hostel_type"] === $hostel_filter
    ) {
        $filtered_students[] = $student;
    }
}

$total_students = count($students);
$total_wardens = count($wardens);
$total_maintenance = count($maintenance_staff);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - FixNest</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #172033;
        }

        .navbar {
            background: linear-gradient(135deg, #0d47a1, #1976d2);
            color: white;
            padding: 18px 35px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            flex-wrap: wrap;
        }

        .brand {
            font-size: 25px;
            font-weight: bold;
        }

        .navbar-right {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .logout {
            background: white;
            color: #0d47a1;
            text-decoration: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: bold;
        }

        .container {
            width: 94%;
            max-width: 1400px;
            margin: 28px auto;
        }

        .page-heading {
            margin-bottom: 22px;
        }

        .page-heading h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-heading p {
            margin: 0;
            color: #687386;
        }

        .stats {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 18px;
            margin-bottom: 25px;
        }

        .stat-card {
            background: white;
            border-radius: 15px;
            padding: 23px;
            box-shadow: 0 5px 18px rgba(35, 58, 95, 0.08);
            border-left: 5px solid #1976d2;
        }

        .stat-card h3 {
            margin: 0 0 12px;
            color: #687386;
            font-size: 15px;
        }

        .stat-card strong {
            font-size: 32px;
            color: #0d47a1;
        }

        .layout {
            display: grid;
            grid-template-columns: 245px 1fr;
            gap: 22px;
            align-items: start;
        }

        .sidebar {
            background: white;
            padding: 16px;
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(35, 58, 95, 0.08);
        }

        .sidebar h3 {
            margin: 5px 10px 15px;
            font-size: 17px;
        }

        .side-link {
            display: block;
            text-decoration: none;
            color: #3d4b61;
            padding: 14px;
            margin-bottom: 8px;
            border-radius: 9px;
            font-weight: bold;
        }

        .side-link:hover,
        .side-link.active {
            background: #e8f1ff;
            color: #0d47a1;
        }

        .content {
            background: white;
            padding: 25px;
            border-radius: 15px;
            box-shadow: 0 5px 18px rgba(35, 58, 95, 0.08);
            min-width: 0;
        }

        .content-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .content-header h2 {
            margin: 0;
            font-size: 24px;
        }

        .primary-btn,
        .secondary-btn,
        .danger-btn {
            border: 0;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            padding: 11px 17px;
            border-radius: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        .primary-btn {
            background: #1976d2;
            color: white;
        }

        .secondary-btn {
            background: #e8f1ff;
            color: #0d47a1;
        }

        .message {
            background: #e8f5e9;
            color: #2e7d32;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .error {
            background: #ffebee;
            color: #c62828;
            padding: 13px 16px;
            border-radius: 8px;
            margin-bottom: 18px;
        }

        .filter-box {
            background: #f5f8fc;
            padding: 15px;
            border-radius: 10px;
            display: flex;
            gap: 12px;
            align-items: end;
            flex-wrap: wrap;
            margin-bottom: 20px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 7px;
            flex: 1;
            min-width: 180px;
        }

        .form-group label {
            font-size: 13px;
            font-weight: bold;
            color: #4e5d73;
        }

        .form-group input,
        .form-group select {
            width: 100%;
            padding: 11px 12px;
            border: 1px solid #d4dce8;
            border-radius: 8px;
            font-size: 14px;
            background: white;
        }

        .form-card {
            background: #f7faff;
            border: 1px solid #dce8f8;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 25px;
        }

        .form-card h3 {
            margin-top: 0;
            color: #0d47a1;
        }

        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 15px;
        }

        .form-actions {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .table-wrapper {
            width: 100%;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 850px;
        }

        th {
            background: #edf4ff;
            color: #174a91;
            text-align: left;
            padding: 14px;
            font-size: 13px;
        }

        td {
            border-bottom: 1px solid #edf0f5;
            padding: 14px;
            font-size: 14px;
            vertical-align: top;
        }

        tr:hover td {
            background: #fafcff;
        }

        .badge {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
            white-space: nowrap;
        }

        .boys {
            background: #e3f2fd;
            color: #1565c0;
        }

        .girls {
            background: #fce4ec;
            color: #ad1457;
        }

        .action-buttons {
            display: flex;
            gap: 7px;
            flex-wrap: wrap;
        }

        .small-btn {
            padding: 7px 10px;
            border-radius: 6px;
            text-decoration: none;
            font-size: 12px;
            font-weight: bold;
        }

        .edit-btn {
            background: #fff3e0;
            color: #e65100;
        }

        .delete-btn {
            background: #ffebee;
            color: #c62828;
            border: 0;
            cursor: pointer;
        }

        .empty {
            text-align: center;
            padding: 35px;
            color: #7b8798;
        }

        @media (max-width: 1000px) {
            .stats {
                grid-template-columns: repeat(2, 1fr);
            }

            .layout {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: grid;
                grid-template-columns: repeat(3, 1fr);
                gap: 8px;
            }

            .sidebar h3 {
                grid-column: 1 / -1;
            }
        }

        @media (max-width: 650px) {
            .navbar {
                padding: 16px 20px;
            }

            .container {
                width: 92%;
            }

            .stats {
                grid-template-columns: 1fr;
            }

            .sidebar {
                display: block;
            }

            .form-grid {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 18px;
            }
        }
    </style>
</head>

<body>

<nav class="navbar">
    <div class="brand">FixNest Admin</div>

    <div class="navbar-right">
        <span>Welcome, Admin</span>
        <a class="logout" href="../logout.php">Logout</a>
    </div>
</nav>

<div class="container">

    <div class="page-heading">
        <h1>Admin Dashboard</h1>
        <p>Manage students, wardens and maintenance staff.</p>
    </div>

    <div class="stats">
        <div class="stat-card">
            <h3>Total Students</h3>
            <strong><?php echo $total_students; ?></strong>
        </div>

        <div class="stat-card">
            <h3>Total Wardens</h3>
            <strong><?php echo $total_wardens; ?></strong>
        </div>

        <div class="stat-card">
            <h3>Maintenance Staff</h3>
            <strong><?php echo $total_maintenance; ?></strong>
        </div>

        <div class="stat-card">
            <h3>Total Users</h3>
            <strong>
                <?php echo $total_students + $total_wardens + $total_maintenance; ?>
            </strong>
        </div>
    </div>

    <div class="layout">

        <aside class="sidebar">
            <h3>Management Menu</h3>

            <a
                class="side-link <?php echo $active_tab === "students" ? "active" : ""; ?>"
                href="dashboard.php?tab=students"
            >
                Student Management
            </a>

            <a
                class="side-link <?php echo $active_tab === "wardens" ? "active" : ""; ?>"
                href="dashboard.php?tab=wardens"
            >
                Warden Management
            </a>

            <a
                class="side-link <?php echo $active_tab === "maintenance" ? "active" : ""; ?>"
                href="dashboard.php?tab=maintenance"
            >
                Maintenance Management
            </a>
        </aside>

        <main class="content">

            <?php if ($message !== ""): ?>
                <div class="message">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>

            <?php if ($error !== ""): ?>
                <div class="error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <?php if ($active_tab === "students"): ?>

                <div class="content-header">
                    <h2>Student Management</h2>

                    <a
                        class="primary-btn"
                        href="dashboard.php?tab=students&action=add"
                    >
                        + Add Student
                    </a>
                </div>

                <?php if (
                    (isset($_GET["action"]) && $_GET["action"] === "add") ||
                    $edit_type === "student"
                ): ?>

                    <div class="form-card">
                        <h3>
                            <?php
                            echo $edit_type === "student"
                                ? "Edit Student"
                                : "Add New Student";
                            ?>
                        </h3>

                        <form method="POST" action="dashboard.php?tab=students">

                            <input
                                type="hidden"
                                name="record_id"
                                value="<?php echo $edit_record["id"] ?? 0; ?>"
                            >

                            <div class="form-grid">

                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["name"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Unique Student ID / Username *</label>
                                    <input
                                        type="text"
                                        name="student_id"
                                        required
                                        placeholder="Example: STU003"
                                        value="<?php echo htmlspecialchars($edit_record["student_id"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Department</label>
                                    <input
                                        type="text"
                                        name="department"
                                        value="<?php echo htmlspecialchars($edit_record["department"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Email *</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["email"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="<?php echo htmlspecialchars($edit_record["phone"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Hostel Type *</label>
                                    <select name="hostel_type" required>
                                        <option value="Boys Hostel"
                                            <?php
                                            echo (($edit_record["hostel_type"] ?? "") === "Boys Hostel")
                                                ? "selected"
                                                : "";
                                            ?>>
                                            Boys Hostel
                                        </option>

                                        <option value="Girls Hostel"
                                            <?php
                                            echo (($edit_record["hostel_type"] ?? "") === "Girls Hostel")
                                                ? "selected"
                                                : "";
                                            ?>>
                                            Girls Hostel
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button
                                    class="primary-btn"
                                    type="submit"
                                    name="save_student"
                                >
                                    Save Student
                                </button>

                                <a
                                    class="secondary-btn"
                                    href="dashboard.php?tab=students"
                                >
                                    Cancel
                                </a>
                            </div>

                        </form>
                    </div>

                <?php endif; ?>

                <form class="filter-box" method="GET" action="dashboard.php">

                    <input type="hidden" name="tab" value="students">

                    <div class="form-group">
                        <label>Filter by Hostel</label>

                        <select name="hostel">
                            <option value="All"
                                <?php echo $hostel_filter === "All" ? "selected" : ""; ?>>
                                All Hostels
                            </option>

                            <option value="Boys Hostel"
                                <?php echo $hostel_filter === "Boys Hostel" ? "selected" : ""; ?>>
                                Boys Hostel
                            </option>

                            <option value="Girls Hostel"
                                <?php echo $hostel_filter === "Girls Hostel" ? "selected" : ""; ?>>
                                Girls Hostel
                            </option>
                        </select>
                    </div>

                    <button class="primary-btn" type="submit">
                        Apply Filter
                    </button>

                    <a
                        class="secondary-btn"
                        href="dashboard.php?tab=students"
                    >
                        Reset
                    </a>

                </form>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Student ID / Username</th>
                                <th>Name</th>
                                <th>Department</th>
                                <th>Email</th>
                                <th>Hostel</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php if (count($filtered_students) === 0): ?>

                                <tr>
                                    <td class="empty" colspan="6">
                                        No students found.
                                    </td>
                                </tr>

                            <?php else: ?>

                                <?php foreach ($filtered_students as $student): ?>

                                    <tr>
                                        <td>
                                            <strong>
                                                <?php echo htmlspecialchars($student["student_id"]); ?>
                                            </strong>

                                            <br>

                                            <small style="color:#687386;">
                                                Username:
                                                <?php echo htmlspecialchars($student["student_id"]); ?>
                                            </small>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($student["name"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($student["department"]); ?>
                                        </td>

                                        <td>
                                            <?php echo htmlspecialchars($student["email"]); ?>
                                        </td>

                                        <td>
                                            <span class="badge <?php echo $student["hostel_type"] === "Boys Hostel" ? "boys" : "girls"; ?>">
                                                <?php echo htmlspecialchars($student["hostel_type"]); ?>
                                            </span>
                                        </td>

                                        <td>
                                            <div class="action-buttons">

                                                <a
                                                    class="small-btn edit-btn"
                                                    href="dashboard.php?tab=students&edit_type=student&edit_id=<?php echo $student["id"]; ?>"
                                                >
                                                    Edit
                                                </a>

                                                <form
                                                    method="POST"
                                                    onsubmit="return confirm('Delete this student?');"
                                                >
                                                    <input
                                                        type="hidden"
                                                        name="delete_type"
                                                        value="student"
                                                    >

                                                    <input
                                                        type="hidden"
                                                        name="delete_id"
                                                        value="<?php echo $student["id"]; ?>"
                                                    >

                                                    <button
                                                        class="small-btn delete-btn"
                                                        type="submit"
                                                        name="delete_record"
                                                    >
                                                        Delete
                                                    </button>
                                                </form>

                                            </div>
                                        </td>
                                    </tr>

                                <?php endforeach; ?>

                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($active_tab === "wardens"): ?>

                <div class="content-header">
                    <h2>Warden Management</h2>

                    <a
                        class="primary-btn"
                        href="dashboard.php?tab=wardens&action=add"
                    >
                        + Add Warden
                    </a>
                </div>

                <?php if (
                    (isset($_GET["action"]) && $_GET["action"] === "add") ||
                    $edit_type === "warden"
                ): ?>

                    <div class="form-card">
                        <h3>
                            <?php
                            echo $edit_type === "warden"
                                ? "Edit Warden"
                                : "Add New Warden";
                            ?>
                        </h3>

                        <form method="POST" action="dashboard.php?tab=wardens">

                            <input
                                type="hidden"
                                name="record_id"
                                value="<?php echo $edit_record["id"] ?? 0; ?>"
                            >

                            <div class="form-grid">

                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["name"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Username *</label>
                                    <input
                                        type="text"
                                        name="username"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["username"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Email *</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["email"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="<?php echo htmlspecialchars($edit_record["phone"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Hostel Type *</label>
                                    <select name="hostel_type" required>
                                        <option value="Boys Hostel"
                                            <?php echo (($edit_record["hostel_type"] ?? "") === "Boys Hostel") ? "selected" : ""; ?>>
                                            Boys Hostel
                                        </option>

                                        <option value="Girls Hostel"
                                            <?php echo (($edit_record["hostel_type"] ?? "") === "Girls Hostel") ? "selected" : ""; ?>>
                                            Girls Hostel
                                        </option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button
                                    class="primary-btn"
                                    type="submit"
                                    name="save_warden"
                                >
                                    Save Warden
                                </button>

                                <a
                                    class="secondary-btn"
                                    href="dashboard.php?tab=wardens"
                                >
                                    Cancel
                                </a>
                            </div>

                        </form>
                    </div>

                <?php endif; ?>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Hostel Type</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($wardens as $warden): ?>

                                <tr>
                                    <td><?php echo htmlspecialchars($warden["name"]); ?></td>
                                    <td><?php echo htmlspecialchars($warden["username"]); ?></td>
                                    <td><?php echo htmlspecialchars($warden["email"]); ?></td>
                                    <td><?php echo htmlspecialchars($warden["phone"]); ?></td>

                                    <td>
                                        <span class="badge <?php echo $warden["hostel_type"] === "Boys Hostel" ? "boys" : "girls"; ?>">
                                            <?php echo htmlspecialchars($warden["hostel_type"]); ?>
                                        </span>
                                    </td>

                                    <td>
                                        <div class="action-buttons">

                                            <a
                                                class="small-btn edit-btn"
                                                href="dashboard.php?tab=wardens&edit_type=warden&edit_id=<?php echo $warden["id"]; ?>"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                onsubmit="return confirm('Delete this warden?');"
                                            >
                                                <input type="hidden" name="delete_type" value="warden">
                                                <input type="hidden" name="delete_id" value="<?php echo $warden["id"]; ?>">

                                                <button
                                                    class="small-btn delete-btn"
                                                    type="submit"
                                                    name="delete_record"
                                                >
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php elseif ($active_tab === "maintenance"): ?>

                <div class="content-header">
                    <h2>Maintenance Management</h2>

                    <a
                        class="primary-btn"
                        href="dashboard.php?tab=maintenance&action=add"
                    >
                        + Add Maintenance Staff
                    </a>
                </div>

                <?php if (
                    (isset($_GET["action"]) && $_GET["action"] === "add") ||
                    $edit_type === "maintenance"
                ): ?>

                    <div class="form-card">
                        <h3>
                            <?php
                            echo $edit_type === "maintenance"
                                ? "Edit Maintenance Staff"
                                : "Add Maintenance Staff";
                            ?>
                        </h3>

                        <form method="POST" action="dashboard.php?tab=maintenance">

                            <input
                                type="hidden"
                                name="record_id"
                                value="<?php echo $edit_record["id"] ?? 0; ?>"
                            >

                            <div class="form-grid">

                                <div class="form-group">
                                    <label>Full Name *</label>
                                    <input
                                        type="text"
                                        name="name"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["name"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Username *</label>
                                    <input
                                        type="text"
                                        name="username"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["username"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Email *</label>
                                    <input
                                        type="email"
                                        name="email"
                                        required
                                        value="<?php echo htmlspecialchars($edit_record["email"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Phone</label>
                                    <input
                                        type="text"
                                        name="phone"
                                        value="<?php echo htmlspecialchars($edit_record["phone"] ?? ""); ?>"
                                    >
                                </div>

                                <div class="form-group">
                                    <label>Specialization</label>
                                    <select name="specialization">
                                        <option value="Electrical">Electrical</option>
                                        <option value="Plumbing">Plumbing</option>
                                        <option value="Carpentry">Carpentry</option>
                                        <option value="Cleaning">Cleaning</option>
                                        <option value="General Maintenance">General Maintenance</option>
                                    </select>
                                </div>

                            </div>

                            <div class="form-actions">
                                <button
                                    class="primary-btn"
                                    type="submit"
                                    name="save_maintenance"
                                >
                                    Save Maintenance Staff
                                </button>

                                <a
                                    class="secondary-btn"
                                    href="dashboard.php?tab=maintenance"
                                >
                                    Cancel
                                </a>
                            </div>

                        </form>
                    </div>

                <?php endif; ?>

                <div class="table-wrapper">
                    <table>
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Username</th>
                                <th>Email</th>
                                <th>Phone</th>
                                <th>Specialization</th>
                                <th>Actions</th>
                            </tr>
                        </thead>

                        <tbody>
                            <?php foreach ($maintenance_staff as $staff): ?>

                                <tr>
                                    <td><?php echo htmlspecialchars($staff["name"]); ?></td>
                                    <td><?php echo htmlspecialchars($staff["username"]); ?></td>
                                    <td><?php echo htmlspecialchars($staff["email"]); ?></td>
                                    <td><?php echo htmlspecialchars($staff["phone"]); ?></td>
                                    <td><?php echo htmlspecialchars($staff["specialization"]); ?></td>

                                    <td>
                                        <div class="action-buttons">

                                            <a
                                                class="small-btn edit-btn"
                                                href="dashboard.php?tab=maintenance&edit_type=maintenance&edit_id=<?php echo $staff["id"]; ?>"
                                            >
                                                Edit
                                            </a>

                                            <form
                                                method="POST"
                                                onsubmit="return confirm('Delete this maintenance staff?');"
                                            >
                                                <input type="hidden" name="delete_type" value="maintenance">
                                                <input type="hidden" name="delete_id" value="<?php echo $staff["id"]; ?>">

                                                <button
                                                    class="small-btn delete-btn"
                                                    type="submit"
                                                    name="delete_record"
                                                >
                                                    Delete
                                                </button>
                                            </form>

                                        </div>
                                    </td>
                                </tr>

                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

            <?php endif; ?>

        </main>
    </div>
</div>

</body>
</html>