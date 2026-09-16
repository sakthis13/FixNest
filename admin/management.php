<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";
$success = "";


/*
|--------------------------------------------------------------------------
| DELETE MANAGEMENT USER
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST"
    && isset($_POST["delete_management"])) {

    $management_id = $_POST["management_id"] ?? "";

    if ($management_id !== "") {

        try {

            $stmt = $pdo->prepare("
                DELETE FROM management_users
                WHERE management_id = :management_id
            ");

            $stmt->execute([
                ":management_id" => $management_id
            ]);

            header("Location: management.php?deleted=1");
            exit();

        } catch (PDOException $e) {

            $error = "Unable to delete Management user.";
        }
    }
}


/*
|--------------------------------------------------------------------------
| SUCCESS MESSAGE
|--------------------------------------------------------------------------
*/

if (isset($_GET["management_added"])) {
    $success = "Management user added successfully.";
}

if (isset($_GET["updated"])) {
    $success = "Management user updated successfully.";
}

if (isset($_GET["deleted"])) {
    $success = "Management user deleted successfully.";
}


/*
|--------------------------------------------------------------------------
| FETCH MANAGEMENT USERS
|--------------------------------------------------------------------------
*/

try {

    $stmt = $pdo->prepare("
        SELECT
            management_id,
            username,
            management_name,
            email,
            phone,
            created_at
        FROM management_users
        ORDER BY created_at DESC
    ");

    $stmt->execute();

    $management_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {

    $error = "Unable to load Management users.";
    $management_users = [];
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

    <title>Management Users | FixNest Admin</title>


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


        .logout {

            color: white;

            text-decoration: none;

            background:
                rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;
        }


        /* CONTAINER */

        .container {

            max-width: 1250px;

            margin: 35px auto;

            padding: 0 20px;
        }


        /* BACK */

        .back {

            display: inline-block;

            margin-bottom: 20px;

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;
        }


        .back:hover {

            text-decoration: underline;
        }


        /* HEADER */

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


        /* ADD BUTTON */

        .add-btn {

            display: inline-block;

            background: #2563eb;

            color: white;

            text-decoration: none;

            padding: 11px 17px;

            border-radius: 7px;

            font-size: 14px;

            font-weight: bold;

            white-space: nowrap;
        }


        .add-btn:hover {

            background: #1d4ed8;
        }


        /* CARD */

        .card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px
                rgba(0,0,0,0.06);

            overflow-x: auto;
        }


        .card h2 {

            font-size: 20px;

            margin-bottom: 20px;
        }


        /* TABLE */

        table {

            width: 100%;

            min-width: 900px;

            border-collapse: collapse;
        }


        th,
        td {

            padding: 13px;

            text-align: left;

            border-bottom:
                1px solid #e5e7eb;

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


        /* ACTIONS */

        .actions {

            display: flex;

            gap: 8px;

            align-items: center;
        }


        .edit-btn {

            display: inline-block;

            background: #2563eb;

            color: white;

            text-decoration: none;

            padding: 7px 11px;

            border-radius: 6px;

            font-size: 12px;

            font-weight: bold;
        }


        .edit-btn:hover {

            background: #1d4ed8;
        }


        .delete-btn {

            background: #dc2626;

            color: white;

            border: none;

            padding: 7px 11px;

            border-radius: 6px;

            font-size: 12px;

            font-weight: bold;

            cursor: pointer;
        }


        .delete-btn:hover {

            background: #b91c1c;
        }


        /* MESSAGES */

        .success {

            background: #dcfce7;

            color: #166534;

            padding: 13px 15px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;
        }


        .error {

            background: #fee2e2;

            color: #991b1b;

            padding: 13px 15px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;
        }


        /* EMPTY */

        .empty {

            text-align: center;

            padding: 45px;

            color: #64748b;

            font-size: 14px;
        }


        /* INFO */

        .info {

            margin-top: 20px;

            padding: 16px;

            background: #eff6ff;

            border-left:
                4px solid #2563eb;

            border-radius: 7px;

            color: #1e40af;

            font-size: 13px;

            line-height: 1.6;
        }


        /* RESPONSIVE */

        @media (max-width: 700px) {

            .navbar {

                padding: 0 15px;
            }


            .container {

                padding: 0 15px;
            }


            .page-header {

                flex-direction: column;

                align-items: flex-start;
            }


            .page-header h1 {

                font-size: 25px;
            }

        }

    </style>

</head>


<body>


<nav class="navbar">

    <div class="logo">
        FixNest Admin
    </div>


    <a
        href="../index.php"
        class="logout"
    >
        Logout
    </a>

</nav>



<div class="container">


    <a
        href="dashboard.php"
        class="back"
    >
        ← Back to Admin Dashboard
    </a>



    <div class="page-header">

        <div>

            <h1>
                Management Users
            </h1>

            <p>
                Manage accounts that access the
                Management analysis dashboard.
            </p>

        </div>


        <a
            href="add-management.php"
            class="add-btn"
        >
            + Add Management
        </a>

    </div>



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



    <div class="card">


        <h2>
            Management Accounts
        </h2>


        <?php if (empty($management_users)): ?>


            <div class="empty">

                No Management users have been added yet.

            </div>


        <?php else: ?>


            <table>

                <thead>

                    <tr>

                        <th>
                            ID
                        </th>

                        <th>
                            Management Name
                        </th>

                        <th>
                            Username
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
                            Action
                        </th>

                    </tr>

                </thead>


                <tbody>


                <?php foreach (
                    $management_users
                    as $management
                ): ?>


                    <tr>


                        <td>

                            <?= htmlspecialchars(
                                $management["management_id"]
                            ) ?>

                        </td>


                        <td>

                            <strong>

                                <?= htmlspecialchars(
                                    $management["management_name"]
                                ) ?>

                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $management["username"]
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $management["email"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $management["phone"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $management["created_at"]
                                ?? "-"
                            ) ?>

                        </td>


                        <td>


                            <div class="actions">


                                <a
    href="edit-management.php?id=<?= urlencode($management["management_id"]) ?>"
    class="edit-btn"
>
    Edit
</a>


                                <form
                                    method="POST"
                                    style="display:inline;"
                                    onsubmit="return confirm(
                                        'Are you sure you want to delete this Management user?'
                                    );"
                                >

                                    <input
                                        type="hidden"
                                        name="management_id"
                                        value="<?= htmlspecialchars(
                                            $management["management_id"]
                                        ) ?>"
                                    >


                                    <button
                                        type="submit"
                                        name="delete_management"
                                        class="delete-btn"
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


        <?php endif; ?>


    </div>



    <div class="info">

        <strong>Management access:</strong>

        Management users can access the
        Management dashboard for complaint
        analysis, recurring-issue monitoring,
        maintenance information and
        preventive-maintenance insights.

    </div>


</div>


</body>

</html>