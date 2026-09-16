<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";


/*
|--------------------------------------------------------------------------
| ACCOUNT LIST
|--------------------------------------------------------------------------
*/

$accounts = [];

try {

    /*
    |--------------------------------------------------------------------------
    | STUDENTS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            student_id AS login_id,
            student_name AS name,
            email,
            phone,
            'student' AS role,
            created_at
        FROM students
        ORDER BY created_at DESC
    ");

    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($students as $student) {
        $accounts[] = $student;
    }


    /*
    |--------------------------------------------------------------------------
    | WARDENS
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            username AS login_id,
            warden_name AS name,
            email,
            phone,
            'warden' AS role,
            created_at
        FROM wardens
        ORDER BY created_at DESC
    ");

    $wardens = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($wardens as $warden) {
        $accounts[] = $warden;
    }


    /*
    |--------------------------------------------------------------------------
    | MAINTENANCE
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            username AS login_id,
            worker_name AS name,
            email,
            phone,
            'maintenance' AS role,
            created_at
        FROM maintenance_users
        ORDER BY created_at DESC
    ");

    $maintenance_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($maintenance_users as $maintenance) {
        $accounts[] = $maintenance;
    }


    /*
    |--------------------------------------------------------------------------
    | MANAGEMENT
    |--------------------------------------------------------------------------
    */

    $stmt = $pdo->query("
        SELECT
            username AS login_id,
            management_name AS name,
            email,
            phone,
            'management' AS role,
            created_at
        FROM management_users
        ORDER BY created_at DESC
    ");

    $management_users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    foreach ($management_users as $management) {
        $accounts[] = $management;
    }


    /*
    |--------------------------------------------------------------------------
    | SORT ALL ACCOUNTS BY CREATED DATE
    |--------------------------------------------------------------------------
    */

    usort($accounts, function ($a, $b) {

        return strtotime($b["created_at"])
             <=> strtotime($a["created_at"]);

    });


} catch (PDOException $e) {

    die(
        "Database Error: "
        . htmlspecialchars($e->getMessage())
    );

}


/*
|--------------------------------------------------------------------------
| COUNTS
|--------------------------------------------------------------------------
*/

$total_accounts = count($accounts);

$student_accounts = 0;
$warden_accounts = 0;
$maintenance_accounts = 0;
$management_accounts = 0;


foreach ($accounts as $account) {

    if ($account["role"] === "student") {

        $student_accounts++;

    } elseif ($account["role"] === "warden") {

        $warden_accounts++;

    } elseif ($account["role"] === "maintenance") {

        $maintenance_accounts++;

    } elseif ($account["role"] === "management") {

        $management_accounts++;

    }

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

    <title>User Accounts | FixNest Admin</title>


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


        .logout {

            color: white;

            text-decoration: none;

            background: rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;
        }


        .logout:hover {

            background: rgba(255,255,255,0.25);
        }


        /* =========================
           CONTAINER
        ========================== */

        .container {

            max-width: 1200px;

            margin: 35px auto;

            padding: 0 20px;
        }


        /* =========================
           BACK
        ========================== */

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


        /* =========================
           HEADER
        ========================== */

        .page-header {

            margin-bottom: 30px;
        }


        .page-header h1 {

            font-size: 30px;

            margin-bottom: 8px;
        }


        .page-header p {

            color: #64748b;

            font-size: 14px;
        }


        /* =========================
           STATS
        ========================== */

        .stats {

            display: grid;

            grid-template-columns:
                repeat(5, minmax(0, 1fr));

            gap: 18px;

            margin-bottom: 25px;
        }


        .stat-card {

            background: white;

            padding: 22px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        .stat-title {

            color: #64748b;

            font-size: 13px;

            margin-bottom: 10px;
        }


        .stat-number {

            font-size: 28px;

            font-weight: bold;

            color: #1e3a8a;
        }


        /* =========================
           MAIN CARD
        ========================== */

        .card {

            background: white;

            padding: 25px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);

            overflow-x: auto;
        }


        .card h2 {

            font-size: 20px;

            margin-bottom: 20px;
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
           ROLE BADGES
        ========================== */

        .role {

            display: inline-block;

            padding: 6px 10px;

            border-radius: 20px;

            font-size: 11px;

            font-weight: bold;

            text-transform: uppercase;
        }


        .student {

            background: #dcfce7;

            color: #166534;
        }


        .warden {

            background: #fef3c7;

            color: #92400e;
        }


        .maintenance {

            background: #dbeafe;

            color: #1d4ed8;
        }


        .management {

            background: #ede9fe;

            color: #6d28d9;
        }


        /* =========================
           EMPTY
        ========================== */

        .empty {

            text-align: center;

            padding: 45px;

            color: #64748b;
        }


        /* =========================
           INFO
        ========================== */

        .info {

            margin-top: 20px;

            padding: 16px;

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

        @media (max-width: 1100px) {

            .stats {

                grid-template-columns:
                    repeat(3, minmax(0, 1fr));

            }

        }


        @media (max-width: 900px) {

            .stats {

                grid-template-columns:
                    repeat(2, minmax(0, 1fr));

            }

        }


        @media (max-width: 600px) {

            .navbar {

                padding: 0 15px;
            }


            .admin-label {

                display: none;
            }


            .container {

                padding: 0 15px;
            }


            .stats {

                grid-template-columns: 1fr;
            }


            .page-header h1 {

                font-size: 25px;
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


        <a
            href="../index.php"
            class="logout"
        >
            Logout
        </a>

    </div>

</nav>



<div class="container">


    <!-- BACK -->

    <a
        href="dashboard.php"
        class="back"
    >
        ← Back to Dashboard
    </a>



    <!-- HEADER -->

    <div class="page-header">

        <h1>
            User Accounts
        </h1>

        <p>
            View all student, warden, maintenance and
            management login accounts.
        </p>

    </div>



    <!-- =========================
         STATISTICS
    ========================== -->

    <div class="stats">


        <div class="stat-card">

            <div class="stat-title">
                Total Accounts
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($total_accounts) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Student Accounts
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($student_accounts) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Warden Accounts
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($warden_accounts) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Maintenance Accounts
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($maintenance_accounts) ?>
            </div>

        </div>


        <div class="stat-card">

            <div class="stat-title">
                Management Accounts
            </div>

            <div class="stat-number">
                <?= htmlspecialchars($management_accounts) ?>
            </div>

        </div>


    </div>



    <!-- =========================
         ACCOUNTS TABLE
    ========================== -->

    <div class="card">


        <h2>
            All Login Accounts
        </h2>


        <?php if (empty($accounts)): ?>


            <div class="empty">

                No user accounts found.

            </div>


        <?php else: ?>


            <table>


                <thead>

                    <tr>

                        <th>
                            Login ID
                        </th>

                        <th>
                            Name
                        </th>

                        <th>
                            Role
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

                    </tr>

                </thead>


                <tbody>


                <?php foreach ($accounts as $account): ?>


                    <?php

                    $role_class = strtolower(
                        $account["role"]
                    );

                    ?>


                    <tr>


                        <td>

                            <strong>
                                <?= htmlspecialchars(
                                    $account["login_id"]
                                ) ?>
                            </strong>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $account["name"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <span
                                class="role <?= htmlspecialchars(
                                    $role_class
                                ) ?>"
                            >

                                <?= htmlspecialchars(
                                    $account["role"]
                                ) ?>

                            </span>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $account["email"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $account["phone"] ?? "-"
                            ) ?>

                        </td>


                        <td>

                            <?= htmlspecialchars(
                                $account["created_at"] ?? "-"
                            ) ?>

                        </td>


                    </tr>


                <?php endforeach; ?>


                </tbody>


            </table>


        <?php endif; ?>


    </div>



    <!-- =========================
         INFO
    ========================== -->

    <div class="info">

        <strong>Security:</strong>

        Passwords are intentionally not displayed.
        Student, warden, maintenance and management
        accounts are maintained in their respective
        database tables.

    </div>


</div>


</body>

</html>