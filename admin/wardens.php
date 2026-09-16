<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$success = "";
$error = "";


/*
|--------------------------------------------------------------------------
| DELETE WARDEN
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $action = $_POST["action"] ?? "";

    if ($action === "delete") {

        $warden_id = $_POST["warden_id"] ?? "";

        if ($warden_id !== "") {

            try {

                $stmt = $pdo->prepare(
                    "DELETE FROM wardens
                     WHERE warden_id = :warden_id"
                );

                $stmt->execute([
                    ":warden_id" => $warden_id
                ]);

                $success = "Warden deleted successfully.";

            } catch (PDOException $e) {

                $error = "Unable to delete warden.";

            }
        }
    }
}


/*
|--------------------------------------------------------------------------
| FETCH WARDENS
|--------------------------------------------------------------------------
*/

$wardens = [];

try {

    $stmt = $pdo->query(
        "SELECT
            warden_id,
            username,
            warden_name,
            gender,
            hostel_type,
            email,
            phone,
            created_at
         FROM wardens
         ORDER BY warden_id ASC"
    );

    $wardens = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load wardens.";

}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Manage Wardens | FixNest Admin</title>

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

        /* NAVBAR */

        .navbar {
            background: #1e3a8a;
            color: white;

            padding: 17px 30px;

            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo {
            font-size: 21px;
            font-weight: bold;
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .back-link {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .logout {
            color: white;
            text-decoration: none;

            background: rgba(255,255,255,0.15);

            padding: 8px 13px;

            border-radius: 6px;

            font-size: 14px;
        }

        /* CONTAINER */

        .container {
            max-width: 1250px;
            margin: 35px auto;
            padding: 0 20px;
        }

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

        /* BUTTON */

        .add-btn {
            background: #2563eb;
            color: white;

            text-decoration: none;

            padding: 10px 15px;

            border-radius: 7px;

            font-size: 13px;
            font-weight: bold;

            white-space: nowrap;
        }

        .add-btn:hover {
            background: #1d4ed8;
        }

        /* MESSAGES */

        .success {
            background: #dcfce7;
            border: 1px solid #bbf7d0;
            color: #166534;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;
        }

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;
        }

        /* CARD */

        .card {
            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }

        .card-title {
            font-size: 20px;
            margin-bottom: 20px;
        }

        /* TABLE */

        .table-wrapper {
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 950px;

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

            font-weight: bold;

            white-space: nowrap;
        }

        td {
            color: #374151;
        }

        tbody tr:hover {
            background: #f8fafc;
        }

        /* HOSTEL */

        .hostel-badge {
            display: inline-block;

            padding: 5px 10px;

            border-radius: 20px;

            font-size: 11px;
            font-weight: bold;
        }

        .boys {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .girls {
            background: #fce7f3;
            color: #be185d;
        }

        /* ACTIONS */

        .actions {
            display: flex;
            gap: 7px;
        }

        .edit-btn,
        .delete-btn {

            border: none;

            padding: 7px 11px;

            border-radius: 6px;

            font-size: 12px;

            font-weight: bold;

            text-decoration: none;

            cursor: pointer;
        }

        .edit-btn {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .edit-btn:hover {
            background: #bfdbfe;
        }

        .delete-btn {
            background: #fee2e2;
            color: #dc2626;
        }

        .delete-btn:hover {
            background: #fecaca;
        }

        /* EMPTY */

        .empty {
            text-align: center;

            padding: 35px;

            color: #64748b;
        }

        /* RESPONSIVE */

        @media (max-width: 700px) {

            .navbar {
                padding: 15px;
            }

            .back-link {
                display: none;
            }

            .container {
                margin: 25px auto;
            }

            .page-header {
                flex-direction: column;
                align-items: flex-start;
            }

            .card {
                padding: 18px;
            }

        }

    </style>

</head>


<body>


<!-- NAVBAR -->

<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>

    <div class="nav-links">

        <a href="dashboard.php"
           class="back-link">

            ← Dashboard

        </a>

        <a href="../index.php"
           class="logout">

            Logout

        </a>

    </div>

</nav>


<div class="container">


    <!-- PAGE HEADER -->

    <div class="page-header">

        <div>

            <h1>
                Manage Wardens
            </h1>

            <p>
                Add, edit and remove warden information.
            </p>

        </div>


        <a href="add-warden.php"
           class="add-btn">

            + Add Warden

        </a>

    </div>


    <!-- MESSAGES -->

    <?php if ($success !== ""): ?>

        <div class="success">

            <?= htmlspecialchars($success) ?>

        </div>

    <?php endif; ?>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- WARDEN TABLE -->

    <div class="card">

        <h2 class="card-title">
            Warden Accounts
        </h2>


        <div class="table-wrapper">

            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Username
                        </th>

                        <th>
                            Warden Name
                        </th>

                        <th>
                            Gender
                        </th>

                        <th>
                            Hostel
                        </th>

                        <th>
                            Email
                        </th>

                        <th>
                            Phone
                        </th>

                        <th>
                            Actions
                        </th>

                    </tr>

                </thead>


                <tbody>

                <?php if (empty($wardens)): ?>

                    <tr>

                        <td colspan="8"
                            class="empty">

                            No wardens found.

                        </td>

                    </tr>

                <?php else: ?>


                    <?php foreach ($wardens as $warden): ?>

                        <tr>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["warden_id"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["username"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["warden_name"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["gender"]
                                ) ?>

                            </td>


                            <td>

                                <?php

                                $hostel =
                                    $warden["hostel_type"]
                                    ?? "";

                                $hostel_class =
                                    strtolower($hostel)
                                    === "girls hostel"
                                    ? "girls"
                                    : "boys";

                                ?>

                                <span
                                    class="hostel-badge
                                    <?= $hostel_class ?>"
                                >

                                    <?= htmlspecialchars(
                                        $hostel
                                    ) ?>

                                </span>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["email"]
                                ) ?>

                            </td>


                            <td>

                                <?= htmlspecialchars(
                                    $warden["phone"]
                                ) ?>

                            </td>


                            <td>

                                <div class="actions">


                                    <a
                                        href="edit-warden.php?warden_id=<?= urlencode($warden["warden_id"]) ?>"
                                        class="edit-btn"
                                    >

                                        Edit

                                    </a>


                                    <form
                                        method="POST"
                                        onsubmit="return confirm('Are you sure you want to delete this warden?');"
                                    >

                                        <input
                                            type="hidden"
                                            name="action"
                                            value="delete"
                                        >

                                        <input
                                            type="hidden"
                                            name="warden_id"
                                            value="<?= htmlspecialchars($warden["warden_id"]) ?>"
                                        >

                                        <button
                                            type="submit"
                                            class="delete-btn"
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

    </div>


</div>


</body>

</html>