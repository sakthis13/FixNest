<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";


/*
|--------------------------------------------------------------------------
| GET MANAGEMENT ID
|--------------------------------------------------------------------------
*/

$management_id = $_GET["id"] ?? null;

if (!$management_id) {
    header("Location: management.php");
    exit();
}


$message = "";
$message_type = "";


/*
|--------------------------------------------------------------------------
| UPDATE MANAGEMENT USER
|--------------------------------------------------------------------------
*/

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $management_id = $_POST["management_id"] ?? null;

    $username = trim($_POST["username"] ?? "");
    $management_name = trim($_POST["management_name"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $new_password = trim($_POST["new_password"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | BASIC VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $management_id === "" ||
        $username === "" ||
        $management_name === ""
    ) {

        $message = "Please fill in all required fields.";
        $message_type = "error";

    } else {

        try {

            /*
            |--------------------------------------------------------------------------
            | CHECK USERNAME
            |--------------------------------------------------------------------------
            */

            $stmt = $pdo->prepare("
                SELECT management_id
                FROM management_users
                WHERE username = :username
                  AND management_id != :management_id
                LIMIT 1
            ");

            $stmt->execute([
                ":username" => $username,
                ":management_id" => $management_id
            ]);

            $existing = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($existing) {

                $message = "This username is already being used.";
                $message_type = "error";

            } else {


                /*
                |--------------------------------------------------------------------------
                | UPDATE WITH NEW PASSWORD
                |--------------------------------------------------------------------------
                */

                if ($new_password !== "") {

                    $hashed_password = password_hash(
                        $new_password,
                        PASSWORD_DEFAULT
                    );

                    $sql = "
                        UPDATE management_users
                        SET
                            username = :username,
                            password = :password,
                            management_name = :management_name,
                            email = :email,
                            phone = :phone
                        WHERE management_id = :management_id
                    ";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ":username" => $username,
                        ":password" => $hashed_password,
                        ":management_name" => $management_name,
                        ":email" => $email !== "" ? $email : null,
                        ":phone" => $phone !== "" ? $phone : null,
                        ":management_id" => $management_id
                    ]);

                } else {


                    /*
                    |--------------------------------------------------------------------------
                    | UPDATE WITHOUT CHANGING PASSWORD
                    |--------------------------------------------------------------------------
                    */

                    $sql = "
                        UPDATE management_users
                        SET
                            username = :username,
                            management_name = :management_name,
                            email = :email,
                            phone = :phone
                        WHERE management_id = :management_id
                    ";

                    $stmt = $pdo->prepare($sql);

                    $stmt->execute([
                        ":username" => $username,
                        ":management_name" => $management_name,
                        ":email" => $email !== "" ? $email : null,
                        ":phone" => $phone !== "" ? $phone : null,
                        ":management_id" => $management_id
                    ]);

                }


                header(
                    "Location: management.php?updated=1"
                );

                exit();

            }

        } catch (PDOException $e) {

            $message =
                "Database error: "
                . $e->getMessage();

            $message_type = "error";

        }

    }

}


/*
|--------------------------------------------------------------------------
| FETCH MANAGEMENT USER
|--------------------------------------------------------------------------
*/

$stmt = $pdo->prepare("
    SELECT
        management_id,
        username,
        management_name,
        email,
        phone,
        created_at
    FROM management_users
    WHERE management_id = :management_id
    LIMIT 1
");

$stmt->execute([
    ":management_id" => $management_id
]);

$management = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$management) {

    die("Management user not found.");

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

    <title>Edit Management | FixNest Admin</title>


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

            background: rgba(255,255,255,0.15);

            padding: 9px 15px;

            border-radius: 7px;
        }


        /* CONTAINER */

        .container {

            max-width: 850px;

            margin: 35px auto;

            padding: 0 20px;
        }


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

            margin-bottom: 25px;
        }


        .page-header h1 {

            font-size: 30px;

            margin-bottom: 8px;
        }


        .page-header p {

            color: #64748b;

            font-size: 14px;
        }


        /* CARD */

        .card {

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        /* FORM */

        .form-group {

            margin-bottom: 20px;
        }


        .form-group label {

            display: block;

            margin-bottom: 8px;

            font-weight: bold;

            font-size: 14px;
        }


        .form-control {

            width: 100%;

            padding: 12px 14px;

            border: 1px solid #cbd5e1;

            border-radius: 7px;

            font-size: 15px;

            font-family: inherit;

            outline: none;
        }


        .form-control:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37,99,235,0.12);
        }


        .required {

            color: #dc2626;
        }


        .password-note {

            margin-top: 7px;

            color: #64748b;

            font-size: 12px;

            line-height: 1.5;
        }


        /* BUTTONS */

        .actions {

            display: flex;

            gap: 12px;

            margin-top: 25px;
        }


        .save-btn {

            border: none;

            background: #2563eb;

            color: white;

            padding: 12px 22px;

            border-radius: 7px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;
        }


        .save-btn:hover {

            background: #1d4ed8;
        }


        .cancel-btn {

            display: inline-block;

            padding: 12px 22px;

            background: #e5e7eb;

            color: #374151;

            border-radius: 7px;

            text-decoration: none;

            font-weight: bold;

            font-size: 15px;
        }


        .cancel-btn:hover {

            background: #d1d5db;
        }


        /* MESSAGES */

        .message {

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;

            font-weight: 600;
        }


        .message.error {

            background: #fee2e2;

            color: #b91c1c;
        }


        /* INFO */

        .info {

            margin-top: 20px;

            padding: 15px;

            background: #eff6ff;

            border-left: 4px solid #2563eb;

            border-radius: 7px;

            color: #1e40af;

            font-size: 13px;

            line-height: 1.6;
        }


        @media (max-width: 600px) {

            .navbar {

                padding: 0 15px;
            }


            .container {

                padding: 0 15px;
            }


            .card {

                padding: 20px;
            }


            .actions {

                flex-direction: column;
            }


            .save-btn,
            .cancel-btn {

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
        href="../index.php"
        class="logout"
    >
        Logout
    </a>

</nav>


<div class="container">


    <a
        href="management.php"
        class="back"
    >
        ← Back to Management
    </a>


    <div class="page-header">

        <h1>
            Edit Management User
        </h1>

        <p>
            Update management account information and password.
        </p>

    </div>


    <?php if (!empty($message)): ?>

        <div class="message <?= htmlspecialchars($message_type) ?>">

            <?= htmlspecialchars($message) ?>

        </div>

    <?php endif; ?>


    <div class="card">


        <form method="POST">


            <input
                type="hidden"
                name="management_id"
                value="<?= htmlspecialchars(
                    $management["management_id"]
                ) ?>"
            >


            <div class="form-group">

                <label for="management_name">

                    Management Name
                    <span class="required">*</span>

                </label>


                <input
                    type="text"
                    id="management_name"
                    name="management_name"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $management["management_name"]
                    ) ?>"
                    required
                >

            </div>


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
                    value="<?= htmlspecialchars(
                        $management["username"]
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="email">
                    Email
                </label>


                <input
                    type="email"
                    id="email"
                    name="email"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $management["email"] ?? ""
                    ) ?>"
                    placeholder="Enter email address"
                >

            </div>


            <div class="form-group">

                <label for="phone">
                    Phone
                </label>


                <input
                    type="text"
                    id="phone"
                    name="phone"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $management["phone"] ?? ""
                    ) ?>"
                    placeholder="Enter phone number"
                >

            </div>


            <div class="form-group">

                <label for="new_password">

                    New Password

                </label>


                <input
                    type="password"
                    id="new_password"
                    name="new_password"
                    class="form-control"
                    placeholder="Leave blank to keep current password"
                >


                <div class="password-note">

                    Leave this field empty if you do not want
                    to change the current password.

                </div>

            </div>


            <div class="actions">


                <button
                    type="submit"
                    class="save-btn"
                >
                    Save Changes
                </button>


                <a
                    href="management.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>


            </div>


        </form>


    </div>


    <div class="info">

        <strong>Security:</strong>

        The existing password is never displayed.
        Entering a new password will replace the current
        password securely.

    </div>


</div>


</body>

</html>