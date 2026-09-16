<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username    = trim($_POST["username"] ?? "");
    $password    = $_POST["password"] ?? "";
    $warden_name = trim($_POST["warden_name"] ?? "");
    $gender      = trim($_POST["gender"] ?? "");
    $email       = trim($_POST["email"] ?? "");
    $phone       = trim($_POST["phone"] ?? "");


    /*
    |--------------------------------------------------------------------------
    | VALIDATION
    |--------------------------------------------------------------------------
    */

    if (
        $username === "" ||
        $password === "" ||
        $warden_name === "" ||
        $gender === "" ||
        $email === "" ||
        $phone === ""
    ) {

        $error = "Please fill in all fields.";

    } elseif (!in_array($gender, ["Male", "Female"], true)) {

        $error = "Please select a valid gender.";

    } else {


        /*
        |--------------------------------------------------------------------------
        | AUTOMATIC HOSTEL
        |--------------------------------------------------------------------------
        */

        if ($gender === "Male") {
            $hostel_type = "Boys Hostel";
        } else {
            $hostel_type = "Girls Hostel";
        }


        /*
        |--------------------------------------------------------------------------
        | CHECK USERNAME
        |--------------------------------------------------------------------------
        */

        try {

            $check = $pdo->prepare(
                "SELECT COUNT(*)
                 FROM wardens
                 WHERE username = :username"
            );

            $check->execute([
                ":username" => $username
            ]);

            if ($check->fetchColumn() > 0) {

                $error = "Username already exists.";

            } else {


                /*
                |--------------------------------------------------------------------------
                | INSERT WARDEN
                |--------------------------------------------------------------------------
                */

                $sql = "INSERT INTO wardens
                        (
                            username,
                            password,
                            warden_name,
                            gender,
                            hostel_type,
                            email,
                            phone
                        )
                        VALUES
                        (
                            :username,
                            :password,
                            :warden_name,
                            :gender,
                            :hostel_type,
                            :email,
                            :phone
                        )";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([
                    ":username"    => $username,
                    ":password"    => password_hash(
                        $password,
                        PASSWORD_DEFAULT
                    ),
                    ":warden_name" => $warden_name,
                    ":gender"      => $gender,
                    ":hostel_type" => $hostel_type,
                    ":email"       => $email,
                    ":phone"       => $phone
                ]);


                /*
                |--------------------------------------------------------------------------
                | SUCCESS
                |--------------------------------------------------------------------------
                */

                header("Location: wardens.php?added=1");
                exit();

            }

        } catch (PDOException $e) {

            $error = "Unable to add warden. Database error.";

        }

    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
          content="width=device-width, initial-scale=1.0">

    <title>Add Warden | FixNest Admin</title>

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

        .back-link {
            color: white;
            text-decoration: none;
            font-size: 14px;
        }

        .back-link:hover {
            text-decoration: underline;
        }

        /* CONTAINER */

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
            font-size: 14px;
        }

        /* CARD */

        .card {
            background: white;
            padding: 28px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }

        /* ERROR */

        .error {
            background: #fee2e2;
            border: 1px solid #fecaca;
            color: #991b1b;

            padding: 13px 16px;

            border-radius: 8px;

            margin-bottom: 20px;

            font-size: 14px;
        }

        /* FORM */

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

        .form-control {
            width: 100%;

            padding: 11px 13px;

            border: 1px solid #d1d5db;

            border-radius: 7px;

            font-size: 14px;

            outline: none;
        }

        .form-control:focus {
            border-color: #2563eb;

            box-shadow:
                0 0 0 3px rgba(37,99,235,0.10);
        }

        /* HOSTEL PREVIEW */

        .hostel-preview {
            background: #eff6ff;

            border: 1px solid #bfdbfe;

            color: #1e40af;

            padding: 13px;

            border-radius: 8px;

            margin-top: 8px;

            font-size: 13px;
        }

        /* BUTTONS */

        .buttons {
            display: flex;
            gap: 10px;

            margin-top: 25px;
        }

        .btn {
            border: none;

            padding: 10px 17px;

            border-radius: 7px;

            font-size: 14px;

            font-weight: bold;

            cursor: pointer;

            text-decoration: none;
        }

        .btn-primary {
            background: #2563eb;
            color: white;
        }

        .btn-primary:hover {
            background: #1d4ed8;
        }

        .btn-secondary {
            background: #f1f5f9;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #e2e8f0;
        }

        .note {
            margin-top: 20px;

            padding: 14px;

            background: #f8fafc;

            border-left: 4px solid #2563eb;

            color: #64748b;

            font-size: 13px;

            line-height: 1.5;
        }

        @media (max-width: 600px) {

            .navbar {
                padding: 15px;
            }

            .container {
                margin: 25px auto;
            }

            .card {
                padding: 20px;
            }

            .buttons {
                flex-direction: column;
            }

            .btn {
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

    <a href="wardens.php"
       class="back-link">

        ← Back to Wardens

    </a>

</nav>


<div class="container">


    <div class="page-header">

        <h1>
            Add Warden
        </h1>

        <p>
            Create a new warden account and hostel assignment.
        </p>

    </div>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <div class="card">


        <form method="POST">


            <!-- NAME -->

            <div class="form-group">

                <label for="warden_name">
                    Warden Name
                </label>

                <input
                    type="text"
                    name="warden_name"
                    id="warden_name"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $_POST["warden_name"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <!-- GENDER -->

            <div class="form-group">

                <label for="gender">
                    Gender
                </label>

                <select
                    name="gender"
                    id="gender"
                    class="form-control"
                    required
                    onchange="updateHostel()"
                >

                    <option value="">
                        Select Gender
                    </option>

                    <option
                        value="Male"
                        <?= (($_POST["gender"] ?? "") === "Male")
                            ? "selected"
                            : "" ?>
                    >
                        Male
                    </option>

                    <option
                        value="Female"
                        <?= (($_POST["gender"] ?? "") === "Female")
                            ? "selected"
                            : "" ?>
                    >
                        Female
                    </option>

                </select>


                <div
                    id="hostelPreview"
                    class="hostel-preview"
                >

                    Hostel will be assigned automatically
                    according to gender.

                </div>

            </div>


            <!-- USERNAME -->

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    name="username"
                    id="username"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $_POST["username"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <!-- PASSWORD -->

            <div class="form-group">

                <label for="password">
                    Password
                </label>

                <input
                    type="password"
                    name="password"
                    id="password"
                    class="form-control"
                    required
                >

            </div>


            <!-- EMAIL -->

            <div class="form-group">

                <label for="email">
                    Email
                </label>

                <input
                    type="email"
                    name="email"
                    id="email"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $_POST["email"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <!-- PHONE -->

            <div class="form-group">

                <label for="phone">
                    Phone
                </label>

                <input
                    type="text"
                    name="phone"
                    id="phone"
                    class="form-control"
                    value="<?= htmlspecialchars(
                        $_POST["phone"] ?? ""
                    ) ?>"
                    required
                >

            </div>


            <!-- BUTTONS -->

            <div class="buttons">

                <button
                    type="submit"
                    class="btn btn-primary"
                >

                    Add Warden

                </button>


                <a
                    href="wardens.php"
                    class="btn btn-secondary"
                >

                    Cancel

                </a>

            </div>


        </form>


        <div class="note">

            <strong>Automatic hostel assignment:</strong>

            Male wardens are assigned to
            <strong>Boys Hostel</strong> and female wardens
            are assigned to <strong>Girls Hostel</strong>.

            The administrator does not need to enter the hostel manually.

        </div>


    </div>


</div>


<script>

function updateHostel() {

    const gender =
        document.getElementById("gender").value;

    const preview =
        document.getElementById("hostelPreview");


    if (gender === "Male") {

        preview.textContent =
            "Hostel: Boys Hostel";

    } else if (gender === "Female") {

        preview.textContent =
            "Hostel: Girls Hostel";

    } else {

        preview.textContent =
            "Hostel will be assigned automatically according to gender.";

    }

}

updateHostel();

</script>


</body>

</html>