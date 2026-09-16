<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";
$success = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $management_name = trim($_POST["management_name"] ?? "");
    $username        = trim($_POST["username"] ?? "");
    $password        = $_POST["password"] ?? "";
    $email           = trim($_POST["email"] ?? "");
    $phone           = trim($_POST["phone"] ?? "");

    if (
        $management_name === "" ||
        $username === "" ||
        $password === ""
    ) {

        $error = "Management name, username and password are required.";

    } elseif (strlen($password) < 6) {

        $error = "Password must contain at least 6 characters.";

    } else {

        try {

            /* Check duplicate username */

            $check = $pdo->prepare("
                SELECT management_id
                FROM management_users
                WHERE username = :username
                LIMIT 1
            ");

            $check->execute([
                ":username" => $username
            ]);

            if ($check->fetch()) {

                $error = "This username already exists.";

            } else {

                /* Hash password */

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /* Insert Management user */

                $stmt = $pdo->prepare("
                    INSERT INTO management_users
                    (
                        username,
                        password,
                        management_name,
                        email,
                        phone
                    )
                    VALUES
                    (
                        :username,
                        :password,
                        :management_name,
                        :email,
                        :phone
                    )
                ");

                $stmt->execute([
                    ":username" =>
                        $username,

                    ":password" =>
                        $hashed_password,

                    ":management_name" =>
                        $management_name,

                    ":email" =>
                        $email !== ""
                            ? $email
                            : null,

                    ":phone" =>
                        $phone !== ""
                            ? $phone
                            : null
                ]);


                header("Location: users.php?management_added=1");
                exit();
            }

        } catch (PDOException $e) {

            $error =
                "Unable to add Management user. "
                . $e->getMessage();
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

    <title>Add Management | FixNest Admin</title>

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

        .logout {
            color: white;
            text-decoration: none;
            background: rgba(255,255,255,0.15);
            padding: 9px 15px;
            border-radius: 7px;
        }

        .container {
            max-width: 750px;
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

        .card {
            background: white;
            padding: 30px;
            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }

        h1 {
            margin-bottom: 8px;
            font-size: 28px;
        }

        .subtitle {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            font-size: 14px;
        }

        input {
            width: 100%;
            padding: 12px;

            border: 1px solid #cbd5e1;
            border-radius: 7px;

            font-size: 15px;
            font-family: inherit;
        }

        input:focus {
            outline: none;
            border-color: #2563eb;
            box-shadow:
                0 0 0 3px
                rgba(37,99,235,0.12);
        }

        .required {
            color: #dc2626;
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

        .button-row {
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
            background: #e5e7eb;
            color: #374151;

            padding: 12px 22px;

            border-radius: 7px;

            text-decoration: none;

            font-size: 15px;
            font-weight: bold;
        }

        .cancel-btn:hover {
            background: #d1d5db;
        }

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
                padding: 22px;
            }

            .button-row {
                flex-direction: column;
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
        href="users.php"
        class="back"
    >
        ← Back to Users
    </a>


    <div class="card">

        <h1>
            Add Management User
        </h1>

        <p class="subtitle">
            Create an account for the Management dashboard.
        </p>


        <?php if ($error !== ""): ?>

            <div class="error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <form method="POST">


            <div class="form-group">

                <label for="management_name">
                    Management Name
                    <span class="required">*</span>
                </label>

                <input
                    type="text"
                    id="management_name"
                    name="management_name"
                    placeholder="Enter management name"
                    value="<?= htmlspecialchars(
                        $_POST["management_name"] ?? ""
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
                    placeholder="Enter username"
                    value="<?= htmlspecialchars(
                        $_POST["username"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <div class="form-group">

                <label for="password">
                    Password
                    <span class="required">*</span>
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Enter password"
                    minlength="6"
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
                    placeholder="Enter email"
                    value="<?= htmlspecialchars(
                        $_POST["email"] ?? ""
                    ) ?>"
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
                    placeholder="Enter phone number"
                    value="<?= htmlspecialchars(
                        $_POST["phone"] ?? ""
                    ) ?>"
                >

            </div>


            <div class="button-row">

                <button
                    type="submit"
                    class="save-btn"
                >
                    Add Management
                </button>


                <a
                    href="users.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>

            </div>


        </form>


        <div class="info">

            Management users can log in to the
            Management dashboard and view complaint
            analysis, recurring issues, maintenance
            information and preventive-maintenance
            indicators.

        </div>

    </div>

</div>

</body>

</html>