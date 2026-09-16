<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";


/* ---------------------------------------------------------
   GET WARDEN ID
--------------------------------------------------------- */

$warden_id = isset($_GET["warden_id"])
    ? (int) $_GET["warden_id"]
    : 0;

if ($warden_id <= 0) {
    header("Location: wardens.php");
    exit();
}


/* ---------------------------------------------------------
   FETCH WARDEN
--------------------------------------------------------- */

$sql = "SELECT
            warden_id,
            username,
            password,
            warden_name,
            gender,
            hostel_type,
            email,
            phone
        FROM wardens
        WHERE warden_id = :warden_id";

$stmt = $pdo->prepare($sql);

$stmt->execute([
    ":warden_id" => $warden_id
]);

$warden = $stmt->fetch(PDO::FETCH_ASSOC);


if (!$warden) {
    header("Location: wardens.php");
    exit();
}


/* ---------------------------------------------------------
   VARIABLES
--------------------------------------------------------- */

$error = "";
$success = "";


/* ---------------------------------------------------------
   UPDATE WARDEN
--------------------------------------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $warden_name = trim($_POST["warden_name"] ?? "");
    $username = trim($_POST["username"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $hostel_type = trim($_POST["hostel_type"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $new_password = trim($_POST["password"] ?? "");


    /* -----------------------------------------------------
       BASIC VALIDATION
    ----------------------------------------------------- */

    if (
        $warden_name === "" ||
        $username === "" ||
        $gender === "" ||
        $hostel_type === "" ||
        $email === "" ||
        $phone === ""
    ) {

        $error = "Please fill in all required fields.";

    } else {

        try {

            /* -------------------------------------------------
               CHECK DUPLICATE USERNAME
            ------------------------------------------------- */

            $check_sql = "SELECT warden_id
                          FROM wardens
                          WHERE username = :username
                          AND warden_id != :warden_id";

            $check_stmt = $pdo->prepare($check_sql);

            $check_stmt->execute([
                ":username" => $username,
                ":warden_id" => $warden_id
            ]);

            if ($check_stmt->fetch()) {

                $error = "Username already exists.";

            } else {


                /* -------------------------------------------------
                   UPDATE WITH OR WITHOUT PASSWORD
                ------------------------------------------------- */

                if ($new_password !== "") {

                    $update_sql = "UPDATE wardens
                                   SET
                                       username = :username,
                                       password = :password,
                                       warden_name = :warden_name,
                                       gender = :gender,
                                       hostel_type = :hostel_type,
                                       email = :email,
                                       phone = :phone
                                   WHERE warden_id = :warden_id";

                    $update_stmt = $pdo->prepare($update_sql);

                    $update_stmt->execute([
                        ":username" => $username,
                        ":password" => $new_password,
                        ":warden_name" => $warden_name,
                        ":gender" => $gender,
                        ":hostel_type" => $hostel_type,
                        ":email" => $email,
                        ":phone" => $phone,
                        ":warden_id" => $warden_id
                    ]);

                } else {

                    $update_sql = "UPDATE wardens
                                   SET
                                       username = :username,
                                       warden_name = :warden_name,
                                       gender = :gender,
                                       hostel_type = :hostel_type,
                                       email = :email,
                                       phone = :phone
                                   WHERE warden_id = :warden_id";

                    $update_stmt = $pdo->prepare($update_sql);

                    $update_stmt->execute([
                        ":username" => $username,
                        ":warden_name" => $warden_name,
                        ":gender" => $gender,
                        ":hostel_type" => $hostel_type,
                        ":email" => $email,
                        ":phone" => $phone,
                        ":warden_id" => $warden_id
                    ]);
                }


                /* ---------------------------------------------
                   REDIRECT AFTER UPDATE
                --------------------------------------------- */

                header(
                    "Location: wardens.php?updated=1"
                );

                exit();
            }

        } catch (PDOException $e) {

            $error = "Database error: " . $e->getMessage();
        }
    }


    /* ---------------------------------------------------------
       KEEP ENTERED VALUES IF VALIDATION FAILS
    --------------------------------------------------------- */

    $warden["warden_name"] = $warden_name;
    $warden["username"] = $username;
    $warden["gender"] = $gender;
    $warden["hostel_type"] = $hostel_type;
    $warden["email"] = $email;
    $warden["phone"] = $phone;
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

    <title>Edit Warden | FixNest Admin</title>


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
        }

        /* -------------------------------------------------
           CONTAINER
        ------------------------------------------------- */

        .container {
            max-width: 800px;
            margin: 35px auto;
            padding: 0 20px;
        }

        .page-header {
            margin-bottom: 25px;
        }

        .page-header h1 {
            font-size: 30px;
            margin-bottom: 7px;
        }

        .page-header p {
            color: #64748b;
        }

        /* -------------------------------------------------
           CARD
        ------------------------------------------------- */

        .card {
            background: white;

            padding: 28px;

            border-radius: 12px;

            box-shadow: 0 3px 12px rgba(0,0,0,0.06);
        }

        /* -------------------------------------------------
           FORM
        ------------------------------------------------- */

        .form-group {
            margin-bottom: 20px;
        }

        label {
            display: block;

            margin-bottom: 7px;

            font-size: 14px;
            font-weight: bold;

            color: #374151;
        }

        .required {
            color: #dc2626;
        }

        input,
        select {
            width: 100%;

            padding: 11px 13px;

            border: 1px solid #cbd5e1;

            border-radius: 7px;

            font-size: 14px;

            outline: none;

            background: white;
        }

        input:focus,
        select:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37,99,235,0.10);
        }

        .help-text {
            display: block;

            margin-top: 6px;

            font-size: 12px;

            color: #64748b;
        }

        .form-grid {
            display: grid;

            grid-template-columns: 1fr 1fr;

            gap: 20px;
        }

        /* -------------------------------------------------
           ERROR
        ------------------------------------------------- */

        .error {
            background: #fee2e2;

            border: 1px solid #fecaca;

            color: #991b1b;

            padding: 13px 15px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
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
            padding: 11px 18px;

            border-radius: 7px;

            text-decoration: none;

            border: none;

            cursor: pointer;

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

        .btn-secondary:hover {
            background: #d1d5db;
        }

        /* -------------------------------------------------
           RESPONSIVE
        ------------------------------------------------- */

        @media (max-width: 650px) {

            .navbar {
                padding: 0 15px;
            }

            .logo {
                font-size: 18px;
            }

            .container {
                margin-top: 25px;
            }

            .card {
                padding: 20px;
            }

            .form-grid {
                grid-template-columns: 1fr;
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
        href="wardens.php"
        class="back-link"
    >
        ← Back to Wardens
    </a>

</nav>


<div class="container">


    <div class="page-header">

        <h1>
            Edit Warden
        </h1>

        <p>
            Update the selected warden's account and personal details.
        </p>

    </div>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="card">


        <form method="POST">


            <!-- NAME + USERNAME -->

            <div class="form-grid">


                <div class="form-group">

                    <label for="warden_name">
                        Warden Name
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="warden_name"
                        name="warden_name"
                        value="<?= htmlspecialchars($warden["warden_name"]) ?>"
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
                        value="<?= htmlspecialchars($warden["username"]) ?>"
                        required
                    >

                </div>


            </div>


            <!-- GENDER + HOSTEL -->

            <div class="form-grid">


                <div class="form-group">

                    <label for="gender">
                        Gender
                        <span class="required">*</span>
                    </label>

                    <select
                        name="gender"
                        id="gender"
                        required
                    >

                        <option value="">
                            Select Gender
                        </option>

                        <option
                            value="Male"
                            <?= $warden["gender"] === "Male" ? "selected" : "" ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?= $warden["gender"] === "Female" ? "selected" : "" ?>
                        >
                            Female
                        </option>

                    </select>

                </div>


                <div class="form-group">

                    <label for="hostel_type">
                        Hostel Type
                        <span class="required">*</span>
                    </label>

                    <select
                        name="hostel_type"
                        id="hostel_type"
                        required
                    >

                        <option value="">
                            Select Hostel
                        </option>

                        <option
                            value="Boys Hostel"
                            <?= $warden["hostel_type"] === "Boys Hostel" ? "selected" : "" ?>
                        >
                            Boys Hostel
                        </option>

                        <option
                            value="Girls Hostel"
                            <?= $warden["hostel_type"] === "Girls Hostel" ? "selected" : "" ?>
                        >
                            Girls Hostel
                        </option>

                    </select>

                </div>


            </div>


            <!-- EMAIL + PHONE -->

            <div class="form-grid">


                <div class="form-group">

                    <label for="email">
                        Email
                        <span class="required">*</span>
                    </label>

                    <input
                        type="email"
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($warden["email"]) ?>"
                        required
                    >

                </div>


                <div class="form-group">

                    <label for="phone">
                        Phone
                        <span class="required">*</span>
                    </label>

                    <input
                        type="text"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($warden["phone"]) ?>"
                        required
                    >

                </div>


            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    New Password
                </label>

                <input
                    type="password"
                    id="password"
                    name="password"
                    placeholder="Leave blank to keep current password"
                >

                <span class="help-text">
                    Only enter a password if you want to change it.
                </span>

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Update Warden
                </button>


                <a
                    href="wardens.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

            </div>


        </form>


    </div>


</div>


</body>

</html>