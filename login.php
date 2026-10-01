<?php

session_start();

require_once __DIR__ . "/includes/db.php";

$error = "";

# server connection and login credentials


if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");


    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

        $account_found = false;
        $database_error = false;

# admin login

        if ($username === "admin" && $password === "admin123") {

            $_SESSION["username"] = "admin";
            $_SESSION["role"] = "admin";

            header("Location: admin/dashboard.php");
            exit();
        }


        

        try {

            $stmt = $pdo->prepare("
                SELECT
                    student_id,
                    student_name,
                    password,
                    gender,
                    department,
                    email,
                    phone
                FROM students
                WHERE student_id = :username
                LIMIT 1
            ");

            $stmt->execute([
                ":username" => $username
            ]);

            $student = $stmt->fetch(PDO::FETCH_ASSOC);


            if ($student) {

                $account_found = true;

                $stored_password =
                    (string)($student["password"] ?? "");


              
                $password_correct =
                    password_verify(
                        $password,
                        $stored_password
                    )
                    ||
                    hash_equals(
                        $stored_password,
                        $password
                    );


                if ($password_correct) {


                   

                    $gender =
                        strtolower(
                            trim(
                                $student["gender"] ?? ""
                            )
                        );


                    $hostel_type = "";


                    if (
                        $gender === "male" ||
                        $gender === "m"
                    ) {

                        $hostel_type = "Boys Hostel";

                    } elseif (
                        $gender === "female" ||
                        $gender === "f"
                    ) {

                        $hostel_type = "Girls Hostel";

                    }


                  

                    if ($hostel_type === "") {

                        $error =
                            "Unable to determine hostel from your gender.";

                    } else {

                       

                        $_SESSION["username"] =
                            $student["student_id"];

                        $_SESSION["role"] =
                            "student";

                        $_SESSION["student_id"] =
                            $student["student_id"];

                        $_SESSION["student_name"] =
                            $student["student_name"];

                        $_SESSION["gender"] =
                            $student["gender"];

                        $_SESSION["department"] =
                            $student["department"];

                        $_SESSION["hostel_type"] =
                            $hostel_type;

                        $_SESSION["email"] =
                            $student["email"];

                        $_SESSION["phone"] =
                            $student["phone"];


                        header(
                            "Location: student/dashboard.php"
                        );

                        exit();
                    }
                }
            }

        } catch (PDOException $e) {

            $database_error = true;
        }


       

        if ($error === "") {

            try {

                $stmt = $pdo->prepare("
                    SELECT
                        warden_id,
                        username,
                        password,
                        warden_name,
                        gender,
                        hostel_type,
                        email,
                        phone
                    FROM wardens
                    WHERE username = :username
                    LIMIT 1
                ");

                $stmt->execute([
                    ":username" => $username
                ]);

                $warden =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if ($warden) {

                    $account_found = true;

                    $stored_password =
                        (string)($warden["password"] ?? "");


                    $password_correct =
                        password_verify(
                            $password,
                            $stored_password
                        )
                        ||
                        hash_equals(
                            $stored_password,
                            $password
                        );


                    if ($password_correct) {

                        $_SESSION["username"] =
                            $warden["username"];

                        $_SESSION["role"] =
                            "warden";

                        $_SESSION["warden_id"] =
                            $warden["warden_id"];

                        $_SESSION["warden_name"] =
                            $warden["warden_name"];

                        $_SESSION["hostel_type"] =
                            $warden["hostel_type"];

                        $_SESSION["gender"] =
                            $warden["gender"];

                        $_SESSION["email"] =
                            $warden["email"];

                        $_SESSION["phone"] =
                            $warden["phone"];


                        header(
                            "Location: warden/dashboard.php"
                        );

                        exit();
                    }
                }

            } catch (PDOException $e) {

                $database_error = true;
            }
        }


        

        if ($error === "") {

            try {

                $stmt = $pdo->prepare("
                    SELECT
                        maintenance_id,
                        username,
                        password,
                        worker_name,
                        role,
                        phone,
                        email
                    FROM maintenance_users
                    WHERE username = :username
                    LIMIT 1
                ");

                $stmt->execute([
                    ":username" => $username
                ]);

                $maintenance =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if ($maintenance) {

                    $account_found = true;

                    $stored_password =
                        (string)(
                            $maintenance["password"] ?? ""
                        );


                    $password_correct =
                        password_verify(
                            $password,
                            $stored_password
                        )
                        ||
                        hash_equals(
                            $stored_password,
                            $password
                        );


                    if ($password_correct) {

                        $_SESSION["username"] =
                            $maintenance["username"];

                        $_SESSION["role"] =
                            "maintenance";

                        $_SESSION["maintenance_id"] =
                            $maintenance["maintenance_id"];

                        $_SESSION["worker_name"] =
                            $maintenance["worker_name"];

                        $_SESSION["email"] =
                            $maintenance["email"];

                        $_SESSION["phone"] =
                            $maintenance["phone"];


                        header(
                            "Location: maintenance/dashboard.php"
                        );

                        exit();
                    }
                }

            } catch (PDOException $e) {

                $database_error = true;
            }
        }


      

        if ($error === "") {

            try {

                $stmt = $pdo->prepare("
                    SELECT
                        management_id,
                        username,
                        password,
                        management_name,
                        email,
                        phone
                    FROM management_users
                    WHERE username = :username
                    LIMIT 1
                ");

                $stmt->execute([
                    ":username" => $username
                ]);

                $management =
                    $stmt->fetch(PDO::FETCH_ASSOC);


                if ($management) {

                    $account_found = true;

                    $stored_password =
                        (string)(
                            $management["password"] ?? ""
                        );


                    $password_correct =
                        password_verify(
                            $password,
                            $stored_password
                        )
                        ||
                        hash_equals(
                            $stored_password,
                            $password
                        );


                    if ($password_correct) {

                        $_SESSION["username"] =
                            $management["username"];

                        $_SESSION["role"] =
                            "management";

                        $_SESSION["management_id"] =
                            $management["management_id"];

                        $_SESSION["management_name"] =
                            $management["management_name"];

                        $_SESSION["email"] =
                            $management["email"];

                        $_SESSION["phone"] =
                            $management["phone"];


                        header(
                            "Location: management/dashboard.php"
                        );

                        exit();
                    }
                }

            } catch (PDOException $e) {

                $database_error = true;
            }
        }


       
        if ($error === "") {

            if ($account_found) {

                $error =
                    "Invalid username or password.";

            } elseif ($database_error) {

                $error =
                    "Unable to check user account. Please verify the database tables and columns.";

            } else {

                $error =
                    "Invalid username or password.";
            }
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

    <title>
        FixNest | Login
    </title>


    <style>

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }


        body {

            font-family: Arial, sans-serif;

            min-height: 100vh;

            background:
                linear-gradient(
                    135deg,
                    #eff6ff,
                    #dbeafe
                );

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }


        .login-container {

            width: 100%;

            max-width: 430px;

            background: white;

            padding: 35px;

            border-radius: 15px;

            box-shadow:
                0 10px 35px
                rgba(0, 0, 0, 0.10);
        }


        .logo {

            text-align: center;

            font-size: 30px;

            font-weight: bold;

            color: #1e3a8a;

            margin-bottom: 8px;
        }


        .subtitle {

            text-align: center;

            color: #64748b;

            font-size: 14px;

            margin-bottom: 30px;
        }


        .form-group {

            margin-bottom: 18px;
        }


        label {

            display: block;

            font-size: 13px;

            font-weight: bold;

            margin-bottom: 7px;

            color: #374151;
        }


        input {

            width: 100%;

            padding: 12px 14px;

            border:
                1px solid #cbd5e1;

            border-radius: 8px;

            font-size: 14px;

            outline: none;
        }


        input:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.10);
        }


        button {

            width: 100%;

            border: none;

            background: #1e3a8a;

            color: white;

            padding: 13px;

            border-radius: 8px;

            font-size: 15px;

            font-weight: bold;

            cursor: pointer;
        }


        button:hover {

            background: #1e40af;
        }


        .error {

            background: #fef2f2;

            border-left:
                4px solid #dc2626;

            color: #991b1b;

            padding: 12px;

            border-radius: 6px;

            font-size: 13px;

            margin-bottom: 18px;

            line-height: 1.5;
        }


        .info {

            margin-top: 22px;

            padding: 12px;

            background: #eff6ff;

            border-radius: 7px;

            color: #1e40af;

            font-size: 12px;

            line-height: 1.5;

            text-align: center;
        }


        @media (max-width: 500px) {

            .login-container {

                padding: 25px;
            }

        }

    </style>

</head>


<body>


<div class="login-container">


    <div class="logo">
        FixNest
    </div>


    <div class="subtitle">
        Hostel Complaint Management System
    </div>


    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>


    <form
        method="POST"
        action=""
    >


        <div class="form-group">

            <label for="username">
                Username / Student ID
            </label>

            <input
                type="text"
                id="username"
                name="username"
                placeholder="Enter your username"
                required
                autocomplete="username"
                value="<?= htmlspecialchars(
                    $_POST["username"] ?? ""
                ) ?>"
            >

        </div>


        <div class="form-group">

            <label for="password">
                Password
            </label>

            <input
                type="password"
                id="password"
                name="password"
                placeholder="Enter your password"
                required
                autocomplete="current-password"
            >

        </div>


        <button type="submit">
            Login
        </button>

    </form>


    <div class="info">

        Students are assigned to
        <strong>Boys Hostel</strong> or
        <strong>Girls Hostel</strong>
        based on their registered gender.

    </div>


</div>


</body>

</html>