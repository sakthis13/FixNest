<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$students = [];
$error = "";

try {

    $sql = "SELECT
                student_id,
                student_name,
                gender,
                department,
                email,
                phone,
                created_at
            FROM students
            ORDER BY student_id ASC";

    $stmt = $pdo->query($sql);

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load student records.";

    // Uncomment temporarily while debugging:
    // $error = $e->getMessage();
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Students | FixNest</title>

    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }

        /* =========================
           NAVBAR
        ========================== */

        .navbar {
            height: 65px;
            background: #1e3a8a;
            color: white;

            display: flex;
            align-items: center;
            justify-content: space-between;

            padding: 0 30px;
        }

        .logo {
            font-size: 22px;
            font-weight: bold;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .admin-label {
            font-size: 14px;
            opacity: 0.9;
        }

        .nav-link {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.15);
            padding: 9px 14px;
            border-radius: 7px;
            font-size: 13px;
        }

        .nav-link:hover {
            background: rgba(255,255,255,0.25);
        }

        /* =========================
           CONTAINER
        ========================== */

        .container {
            max-width: 1250px;
            margin: 35px auto;
            padding: 0 20px;
        }

        /* =========================
           HEADER
        ========================== */

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;

            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 30px;
            margin-bottom: 7px;
        }

        .page-header p {
            color: #64748b;
            font-size: 14px;
        }

        /* =========================
           BUTTONS
        ========================== */

        .btn {
            display: inline-block;

            padding: 10px 16px;

            border-radius: 7px;

            text-decoration: none;

            border: none;

            cursor: pointer;

            font-size: 13px;

            font-weight: bold;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: white;
            color: #374151;
            border: 1px solid #d1d5db;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }

        .btn-edit {
            background: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }

        .btn-edit:hover {
            background: #dbeafe;
        }

        .btn-delete {
            background: #fef2f2;
            color: #dc2626;
            border: 1px solid #fecaca;
        }

        .btn-delete:hover {
            background: #fee2e2;
        }

        /* =========================
           CARD
        ========================== */

        .card {
            background: white;

            padding: 24px;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.06);

            overflow-x: auto;
        }

        /* =========================
           TOOLBAR
        ========================== */

        .toolbar {
            display: flex;
            justify-content: space-between;
            align-items: center;

            gap: 15px;

            margin-bottom: 20px;
        }

        .search-box {
            width: 300px;

            padding: 10px 13px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            font-size: 13px;

            outline: none;
        }

        .search-box:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37,99,235,0.1);
        }

        /* =========================
           TABLE
        ========================== */

        table {
            width: 100%;

            min-width: 900px;

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
            background: #f8fafc;
        }

        /* =========================
           STUDENT ID
        ========================== */

        .student-id {
            font-weight: bold;
            color: #1e3a8a;
        }

        /* =========================
           GENDER
        ========================== */

        .gender-badge {
            display: inline-block;

            padding: 5px 9px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: bold;
        }

        .male {
            background: #dbeafe;
            color: #1e40af;
        }

        .female {
            background: #fce7f3;
            color: #9d174d;
        }

        /* =========================
           ACTIONS
        ========================== */

        .actions {
            display: flex;
            gap: 7px;
        }

        .actions .btn {
            padding: 7px 10px;
            font-size: 12px;
        }

        /* =========================
           EMPTY
        ========================== */

        .empty-state {
            text-align: center;

            padding: 45px 20px;

            color: #64748b;
        }

        .empty-state h3 {
            margin-bottom: 7px;

            color: #374151;
        }

        /* =========================
           ERROR
        ========================== */

        .error-message {
            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #991b1b;

            padding: 14px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 13px;
        }

        /* =========================
           FOOTER INFO
        ========================== */

        .info {
            margin-top: 20px;

            padding: 15px 17px;

            background: #eff6ff;

            border-left: 4px solid #2563eb;

            border-radius: 7px;

            color: #1e40af;

            font-size: 13px;

            line-height: 1.6;
        }

        /* =========================
           RESPONSIVE
        ========================== */

        @media (max-width: 700px) {

            .navbar {
                padding: 0 15px;
            }

            .admin-label {
                display: none;
            }

            .container {
                margin: 25px auto;
                padding: 0 15px;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .toolbar {
                flex-direction: column;
                align-items: stretch;
            }

            .search-box {
                width: 100%;
            }

        }

    </style>

</head>


<body>


<!-- =========================
     NAVBAR
========================== -->

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>

    <div class="nav-right">

        <span class="admin-label">
            Administrator
        </span>

        <a href="dashboard.php" class="nav-link">
            Dashboard
        </a>

        <a href="../index.php" class="nav-link">
            Logout
        </a>

    </div>

</nav>


<!-- =========================
     MAIN
========================== -->

<div class="container">


    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>
                Manage Students
            </h1>

            <p>
                View and manage student records registered in FixNest.
            </p>

        </div>


        <a href="add-student.php"
           class="btn btn-primary">

            + Add Student

        </a>

    </div>


    <!-- ERROR -->

    <?php if ($error !== ""): ?>

        <div class="error-message">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- STUDENT TABLE -->

    <div class="card">


        <div class="toolbar">

            <h2>
                Student Records
            </h2>

            <input
                type="text"
                id="searchInput"
                class="search-box"
                placeholder="Search student..."
                onkeyup="searchStudents()"
            >

        </div>


        <?php if (count($students) > 0): ?>


            <table id="studentsTable">

                <thead>

                    <tr>

                        <th>
                            Student ID
                        </th>

                        <th>
                            Student Name
                        </th>

                        <th>
                            Gender
                        </th>

                        <th>
                            Department
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Created
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php foreach ($students as $student): ?>

                    <tr>

                        <td class="student-id">

                            <?= htmlspecialchars(
                                $student["student_id"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $student["student_name"]
                            ) ?>

                        </td>


                        <td>

                            <?php

                            $gender =
                                strtolower(
                                    trim(
                                        $student["gender"] ?? ""
                                    )
                                );

                            ?>

                            <span class="gender-badge
                                <?= $gender === "male"
                                    ? "male"
                                    : "female"
                                ?>">

                                <?= htmlspecialchars(
                                    $student["gender"]
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $student["department"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $student["email"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $student["phone"]
                            ) ?>

                        </td>


                        <td>

                            <?php

                            if (!empty($student["created_at"])) {

                                echo htmlspecialchars(
                                    date(
                                        "d M Y",
                                        strtotime(
                                            $student["created_at"]
                                        )
                                    )
                                );

                            } else {

                                echo "-";

                            }

                            ?>

                        </td>


                        <td>

                            <div class="actions">


                                <a
                                    href="edit-student.php?student_id=<?= urlencode($student["student_id"]) ?>"
                                    class="btn btn-edit"
                                >
                                    Edit
                                </a>


                                <a
                                    href="delete-student.php?student_id=<?= urlencode($student["student_id"]) ?>"
                                    class="btn btn-delete"
                                    onclick="return confirm('Are you sure you want to delete this student?');"
                                >
                                    Delete
                                </a>


                            </div>

                        </td>

                    </tr>

                <?php endforeach; ?>

                </tbody>

            </table>


        <?php else: ?>


            <div class="empty-state">

                <h3>
                    No Students Found
                </h3>

                <p>
                    There are currently no student records in the database.
                </p>

            </div>


        <?php endif; ?>


    </div>


    <!-- INFO -->

    <div class="info">

        <strong>Note:</strong>

        Hostel type and room number are not stored as student
        profile fields. Hostel assignment can be determined from
        the student's gender, while room information can change
        separately.

    </div>


</div>


<!-- =========================
     SEARCH SCRIPT
========================== -->

<script>

function searchStudents() {

    const input =
        document
        .getElementById("searchInput")
        .value
        .toLowerCase();

    const rows =
        document
        .querySelectorAll("#studentsTable tbody tr");

    rows.forEach(function(row) {

        const text =
            row.textContent.toLowerCase();

        row.style.display =
            text.includes(input)
            ? ""
            : "none";

    });

}

</script>


</body>

</html>