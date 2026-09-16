<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$student_id = trim($_GET["student_id"] ?? "");

if ($student_id === "") {
    header("Location: students.php");
    exit();
}

$error = "";

/* ---------------------------------------------------------
   DELETE STUDENT
--------------------------------------------------------- */

try {

    $stmt = $pdo->prepare("
        DELETE FROM students
        WHERE student_id = :student_id
    ");

    $stmt->execute([
        ":student_id" => $student_id
    ]);

    header("Location: students.php?deleted=1");
    exit();

} catch (PDOException $e) {

    /*
     * This can happen if complaints or another table
     * still references this student.
     */
    $error = $e->getMessage();

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Delete Student | FixNest Admin</title>

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

        .back-link {
            color: white;
            text-decoration: none;
        }

        .container {
            max-width: 650px;
            margin: 60px auto;
            padding: 0 20px;
        }

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;

            box-shadow:
                0 4px 15px rgba(0,0,0,0.06);

            text-align: center;
        }

        .icon {
            font-size: 45px;
            margin-bottom: 15px;
        }

        h1 {
            margin-bottom: 10px;
            color: #991b1b;
        }

        p {
            color: #64748b;
            line-height: 1.6;
            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;

            color: #991b1b;

            padding: 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            text-align: left;

            font-size: 14px;
        }

        .buttons {
            display: flex;
            justify-content: center;
            gap: 10px;
        }

        .btn {
            display: inline-block;

            padding: 11px 18px;

            border-radius: 7px;

            text-decoration: none;

            font-size: 14px;

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
            background: #e5e7eb;
            color: #374151;
        }

    </style>

</head>

<body>

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>

    <a
        href="students.php"
        class="back-link"
    >
        ← Back to Students
    </a>

</nav>


<div class="container">

    <div class="card">

        <div class="icon">
            ⚠️
        </div>

        <h1>
            Unable to Delete Student
        </h1>

        <p>
            This student could not be deleted because
            another record may still be referring to this
            student.
        </p>

        <?php if ($error !== ""): ?>

            <div class="error">

                <strong>Database Error:</strong><br><br>

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <div class="buttons">

            <a
                href="students.php"
                class="btn btn-primary"
            >
                Back to Students
            </a>

        </div>

    </div>

</div>

</body>

</html>