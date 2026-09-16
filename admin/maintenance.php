<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";
$success = "";


/* =========================================================
   DELETE MAINTENANCE HEAD
========================================================= */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST["delete_id"])) {

        $delete_id = $_POST["delete_id"];

        try {

            $delete_sql = "
                DELETE FROM maintenance_users
                WHERE maintenance_id = :maintenance_id
            ";

            $delete_stmt = $pdo->prepare($delete_sql);

            $delete_stmt->execute([
                ":maintenance_id" => $delete_id
            ]);

            header("Location: maintenance.php?deleted=1");
            exit();

        } catch (PDOException $e) {

            $error = "Unable to delete Maintenance Head.";
        }
    }
}


/* =========================================================
   SUCCESS MESSAGES
========================================================= */

if (isset($_GET["added"]) && $_GET["added"] === "1") {
    $success = "Maintenance Head added successfully.";
}

if (isset($_GET["updated"]) && $_GET["updated"] === "1") {
    $success = "Maintenance Head updated successfully.";
}

if (isset($_GET["deleted"]) && $_GET["deleted"] === "1") {
    $success = "Maintenance Head deleted successfully.";
}


/* =========================================================
   GET MAINTENANCE HEADS
========================================================= */

$maintenance_users = [];

try {

    $sql = "
        SELECT
            maintenance_id,
            username,
            worker_name,
            role,
            phone,
            email,
            created_at
        FROM maintenance_users
        WHERE role = 'head'
        ORDER BY maintenance_id DESC
    ";

    $stmt = $pdo->query($sql);

    $maintenance_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load Maintenance Head accounts.";
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>Maintenance Management | FixNest Admin</title>


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


        /* =================================================
           NAVBAR
        ================================================= */

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

            background: rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;

            font-size: 14px;
        }


        .back-link:hover {
            background: rgba(255,255,255,0.25);
        }


        /* =================================================
           CONTAINER
        ================================================= */

        .container {
            max-width: 1200px;

            margin: 35px auto;

            padding: 0 20px;
        }


        /* =================================================
           HEADER
        ================================================= */

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


        /* =================================================
           BUTTONS
        ================================================= */

        .btn {
            display: inline-block;

            padding: 10px 16px;

            border-radius: 7px;

            text-decoration: none;

            font-size: 14px;

            font-weight: bold;

            border: none;

            cursor: pointer;
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

            border: 1px solid #cbd5e1;
        }


        .btn-secondary:hover {
            background: #f8fafc;
        }


        /* =================================================
           SUCCESS / ERROR
        ================================================= */

        .success-message {
            background: #dcfce7;

            border: 1px solid #bbf7d0;

            color: #166534;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        .error-message {
            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #991b1b;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* =================================================
           CARD
        ================================================= */

        .card {
            background: white;

            border-radius: 12px;

            padding: 25px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        .card-header {
            display: flex;

            justify-content: space-between;

            align-items: center;

            margin-bottom: 20px;
        }


        .card-header h2 {
            font-size: 20px;
        }


        /* =================================================
           TABLE
        ================================================= */

        .table-wrapper {
            overflow-x: auto;
        }


        table {
            width: 100%;

            border-collapse: collapse;

            min-width: 850px;
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

            font-size: 13px;

            white-space: nowrap;
        }


        td {
            color: #374151;
        }


        tbody tr:hover {
            background: #f8fafc;
        }


        /* =================================================
           ROLE BADGE
        ================================================= */

        .role-badge {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            background: #dbeafe;

            color: #1e40af;

            font-size: 11px;

            font-weight: bold;
        }


        /* =================================================
           ACTION BUTTONS
        ================================================= */

        .actions {
            display: flex;

            gap: 7px;

            align-items: center;
        }


        .action-btn {
            display: inline-block;

            padding: 7px 11px;

            border-radius: 6px;

            text-decoration: none;

            font-size: 12px;

            font-weight: bold;

            border: none;

            cursor: pointer;
        }


        .edit-btn {
            background: #2563eb;

            color: white;
        }


        .edit-btn:hover {
            background: #1d4ed8;
        }


        .delete-btn {
            background: #fee2e2;

            color: #b91c1c;
        }


        .delete-btn:hover {
            background: #fecaca;
        }


        /* =================================================
           EMPTY
        ================================================= */

        .empty {
            text-align: center;

            padding: 40px 20px;

            color: #64748b;

            font-size: 14px;
        }


        /* =================================================
           INFO
        ================================================= */

        .info {
            margin-top: 20px;

            background: #eff6ff;

            border-left: 4px solid #2563eb;

            padding: 16px;

            border-radius: 7px;

            color: #1e40af;

            font-size: 13px;

            line-height: 1.6;
        }


        /* =================================================
           RESPONSIVE
        ================================================= */

        @media (max-width: 700px) {

            .navbar {
                padding: 0 15px;
            }


            .logo {
                font-size: 18px;
            }


            .container {
                margin-top: 25px;
            }


            .page-header {
                flex-direction: column;

                align-items: flex-start;
            }


            .page-header h1 {
                font-size: 25px;
            }


            .card {
                padding: 18px;
            }

        }

    </style>

</head>


<body>


<!-- =====================================================
     NAVBAR
====================================================== -->

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>


    <a
        href="dashboard.php"
        class="back-link"
    >
        ← Dashboard
    </a>

</nav>


<!-- =====================================================
     MAIN
====================================================== -->

<div class="container">


    <!-- HEADER -->

    <div class="page-header">

        <div>

            <h1>
                Maintenance Management
            </h1>

            <p>
                Manage the Maintenance Head account.
            </p>

        </div>


        <a
            href="add-maintenance.php"
            class="btn btn-primary"
        >
            + Add Maintenance Head
        </a>

    </div>


    <!-- SUCCESS -->

    <?php if ($success !== ""): ?>

        <div class="success-message">

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <!-- ERROR -->

    <?php if ($error !== ""): ?>

        <div class="error-message">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- =================================================
         MAINTENANCE TABLE
    ================================================== -->

    <div class="card">


        <div class="card-header">

            <h2>
                Maintenance Head
            </h2>

        </div>


        <?php if (count($maintenance_users) === 0): ?>

            <div class="empty">

                No Maintenance Head account found.

                <br><br>

                <a
                    href="add-maintenance.php"
                    class="btn btn-primary"
                >
                    Add Maintenance Head
                </a>

            </div>

        <?php else: ?>


            <div class="table-wrapper">

                <table>

                    <thead>

                        <tr>

                            <th>
                                ID
                            </th>

                            <th>
                                Name
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                Role
                            </th>

                            <th>
                                Phone
                            </th>

                            <th>
                                Email
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


                    <?php foreach (
                        $maintenance_users
                        as $row
                    ): ?>


                        <tr>


                            <td>

                                <?= htmlspecialchars(
                                    $row["maintenance_id"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $row["worker_name"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $row["username"]
                                ) ?>

                            </td>


                            <td>

                                <span class="role-badge">

                                    Maintenance Head

                                </span>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $row["phone"] ?? "-"
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $row["email"] ?? "-"
                                ) ?>

                            </td>


                            <td>

                                <?= !empty($row["created_at"])
                                    ? htmlspecialchars(
                                        date(
                                            "d M Y",
                                            strtotime(
                                                $row["created_at"]
                                            )
                                        )
                                    )
                                    : "-"
                                ?>

                            </td>


                            <td>

                                <div class="actions">


                                    <!-- EDIT -->

                                    <a
                                        href="edit-maintenance.php?id=<?= urlencode(
                                            $row["maintenance_id"]
                                        ) ?>"
                                        class="action-btn edit-btn"
                                    >
                                        Edit
                                    </a>


                                    <!-- DELETE -->

                                    <form
                                        method="POST"
                                        style="display:inline;"
                                        onsubmit="return confirm(
                                            'Are you sure you want to delete this Maintenance Head account?'
                                        );"
                                    >

                                        <input
                                            type="hidden"
                                            name="delete_id"
                                            value="<?= htmlspecialchars(
                                                $row["maintenance_id"]
                                            ) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="action-btn delete-btn"
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


    </div>


    <!-- INFO -->

    <div class="info">

        <strong>Maintenance Head:</strong>

        The Maintenance Head receives complaints forwarded
        by the warden and manages the maintenance work.

        Worker accounts are not being created at this stage.

    </div>


</div>


</body>

</html>