<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";


/* ---------------------------------------------------------
   ADD MAINTENANCE HEAD
--------------------------------------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";
    $worker_name = trim($_POST["worker_name"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $email = trim($_POST["email"] ?? "");

    /*
     * Only Maintenance Head is allowed currently.
     */
    $role = "head";


    /* -----------------------------------------------------
       VALIDATION
    ----------------------------------------------------- */

    if (
        $username === "" ||
        $password === "" ||
        $worker_name === ""
    ) {

        $error = "Please fill in all required fields.";

    } else {

        try {

            /* ---------------------------------------------
               CHECK DUPLICATE USERNAME
            --------------------------------------------- */

            $check_sql = "
                SELECT maintenance_id
                FROM maintenance_users
                WHERE username = :username
            ";

            $check_stmt = $pdo->prepare($check_sql);

            $check_stmt->execute([
                ":username" => $username
            ]);

            if ($check_stmt->fetch()) {

                $error = "Username already exists.";

            } else {

                /* -----------------------------------------
                   HASH PASSWORD
                ----------------------------------------- */

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /* -----------------------------------------
                   INSERT MAINTENANCE HEAD
                ----------------------------------------- */

                $insert_sql = "
                    INSERT INTO maintenance_users
                    (
                        username,
                        password,
                        worker_name,
                        role,
                        phone,
                        email
                    )
                    VALUES
                    (
                        :username,
                        :password,
                        :worker_name,
                        :role,
                        :phone,
                        :email
                    )
                ";

                $insert_stmt = $pdo->prepare($insert_sql);

                $insert_stmt->execute([
                    ":username" => $username,
                    ":password" => $hashed_password,
                    ":worker_name" => $worker_name,
                    ":role" => $role,
                    ":phone" => $phone !== "" ? $phone : null,
                    ":email" => $email !== "" ? $email : null
                ]);


                /* -----------------------------------------
                   REDIRECT
                ----------------------------------------- */

                header(
                    "Location: maintenance.php?added=1"
                );

                exit();
            }

        } catch (PDOException $e) {

            $error = "Unable to add Maintenance Head.";
        }
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

    <title>Add Maintenance Head | FixNest Admin</title>


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


        /* -------------------------------------------------
           NAVBAR
        ------------------------------------------------- */

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


        /* -------------------------------------------------
           CONTAINER
        ------------------------------------------------- */

        .container {
            max-width: 750px;
            margin: 40px auto;
            padding: 0 20px;
        }


        /* -------------------------------------------------
           HEADER
        ------------------------------------------------- */

        .page-header {
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


        /* -------------------------------------------------
           CARD
        ------------------------------------------------- */

        .card {
            background: white;

            border-radius: 12px;

            padding: 28px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        /* -------------------------------------------------
           ERROR
        ------------------------------------------------- */

        .error-message {
            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #991b1b;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* -------------------------------------------------
           FORM
        ------------------------------------------------- */

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;

            margin-bottom: 7px;

            font-size: 14px;

            font-weight: bold;

            color: #374151;
        }

        .required {
            color: #dc2626;
        }

        .form-control {
            width: 100%;

            padding: 11px 13px;

            border: 1px solid #cbd5e1;

            border-radius: 7px;

            font-size: 14px;

            outline: none;

            background: white;

            color: #1f2937;
        }

        .form-control:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37,99,235,0.1);
        }

        .form-help {
            margin-top: 5px;

            color: #64748b;

            font-size: 12px;
        }


        /* -------------------------------------------------
           ROLE DISPLAY
        ------------------------------------------------- */

        .role-box {
            background: #eff6ff;

            border: 1px solid #bfdbfe;

            border-radius: 8px;

            padding: 15px;
        }

        .role-box strong {
            display: block;

            color: #1e3a8a;

            margin-bottom: 4px;
        }

        .role-box span {
            color: #64748b;

            font-size: 12px;
        }


        /* -------------------------------------------------
           BUTTONS
        ------------------------------------------------- */

        .buttons {
            display: flex;

            gap: 10px;

            margin-top: 25px;
        }

        .btn {
            display: inline-block;

            padding: 11px 18px;

            border-radius: 7px;

            border: none;

            cursor: pointer;

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
            background: white;

            color: #374151;

            border: 1px solid #cbd5e1;
        }

        .btn-secondary:hover {
            background: #f8fafc;
        }


        /* -------------------------------------------------
           INFO
        ------------------------------------------------- */

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


        /* -------------------------------------------------
           RESPONSIVE
        ------------------------------------------------- */

        @media (max-width: 600px) {

            .navbar {
                padding: 0 15px;
            }

            .logo {
                font-size: 18px;
            }

            .container {
                margin-top: 25px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .card {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
                width: 100%;
                text-align: center;
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
        href="maintenance.php"
        class="back-link"
    >
        ← Maintenance
    </a>

</nav>


<div class="container">


    <!-- HEADER -->

    <div class="page-header">

        <h1>
            Add Maintenance Head
        </h1>

        <p>
            Create the Maintenance Head login account.
        </p>

    </div>


    <!-- ERROR -->

    <?php if ($error !== ""): ?>

        <div class="error-message">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <!-- FORM -->

    <div class="card">

        <form method="POST">


            <!-- NAME -->

            <div class="form-group">

                <label for="worker_name">

                    Name
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    id="worker_name"
                    name="worker_name"
                    class="form-control"
                    placeholder="Enter Maintenance Head name"
                    value="<?= htmlspecialchars(
                        $_POST["worker_name"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <!-- USERNAME -->

            <div class="form-group">

                <label for="username">

                    Username
                    <span class="required">*</span>

                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    class="form-control"
                    placeholder="Enter login username"
                    value="<?= htmlspecialchars(
                        $_POST["username"] ?? ""
                    ) ?>"
                    required
                >

                <div class="form-help">
                    This username will be used to log in to
                    the Maintenance Head dashboard.
                </div>

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">

                    Password
                    <span class="required">*</span>

                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    class="form-control"
                    placeholder="Enter login password"
                    required
                >

            </div>


            <!-- ROLE -->

            <div class="form-group">

                <label>
                    Account Role
                </label>

                <div class="role-box">

                    <strong>
                        Maintenance Head
                    </strong>

                    <span>
                        Views complaints forwarded by the warden
                        and manually manages maintenance assignments.
                    </span>

                </div>

                <!-- Role is automatically set to head -->

                <input
                    type="hidden"
                    name="role"
                    value="head"
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control"
                    placeholder="Enter phone number"
                    value="<?= htmlspecialchars(
                        $_POST["phone"] ?? ""
                    ) ?>"
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    placeholder="Enter email address"
                    value="<?= htmlspecialchars(
                        $_POST["email"] ?? ""
                    ) ?>"
                >

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Add Maintenance Head
                </button>


                <a
                    href="maintenance.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>


        </form>

    </div>


    <!-- INFO -->

    <div class="info">

        <strong>Maintenance Head responsibility:</strong>

        The Maintenance Head will view complaints forwarded
        by the warden and manually manage the maintenance work.
        Worker accounts can be added later if required.

    </div>


</div>


</body>

</html>