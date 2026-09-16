<?php

session_start();

require_once __DIR__ . "/includes/db.php";

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    if ($username === "" || $password === "") {

        $error = "Please enter username and password.";

    } else {

        $account_found = false;
        $database_error = false;


        /*
        |--------------------------------------------------------------------------
        | ADMIN LOGIN
        |--------------------------------------------------------------------------
        */

        if ($username === "admin" && $password === "admin123") {

            $_SESSION["username"] = "admin";
            $_SESSION["role"] = "admin";

            header("Location: admin/dashboard.php");
            exit();

        }


        /*
        |--------------------------------------------------------------------------
        | STUDENT LOGIN
        |--------------------------------------------------------------------------
        */

        try {

            $stmt = $pdo->prepare("
                SELECT
                    student_id,
                    student_name,
                    password,
                    hostel_type,
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

                $stored_password = (string)($student["password"] ?? "");

                $password_correct =
                    password_verify($password, $stored_password)
                    || hash_equals(
                        $stored_password,
                        $password
                    );

                if ($password_correct) {

                    $_SESSION["username"] =
                        $student["student_id"];

                    $_SESSION["role"] = "student";

                    $_SESSION["student_id"] =
                        $student["student_id"];

                    $_SESSION["student_name"] =
                        $student["student_name"];

                    $_SESSION["hostel_type"] =
                        $student["hostel_type"];

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

        } catch (PDOException $e) {

            $database_error = true;
        }


        /*
        |--------------------------------------------------------------------------
        | WARDEN LOGIN
        |--------------------------------------------------------------------------
        */

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

            $warden = $stmt->fetch(PDO::FETCH_ASSOC);

            if ($warden) {

                $account_found = true;

                $stored_password =
                    (string)($warden["password"] ?? "");

                $password_correct =
                    password_verify(
                        $password,
                        $stored_password
                    )
                    || hash_equals(
                        $stored_password,
                        $password
                    );

                if ($password_correct) {

                    $_SESSION["username"] =
                        $warden["username"];

                    $_SESSION["role"] = "warden";

                    $_SESSION["warden_id"] =
                        $warden["warden_id"];

                    $_SESSION["warden_name"] =
                        $warden["warden_name"];

                    $_SESSION["hostel_type"] =
                        $warden["hostel_type"];

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


        /*
        |--------------------------------------------------------------------------
        | MAINTENANCE LOGIN
        |--------------------------------------------------------------------------
        */

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
                    (string)($maintenance["password"] ?? "");

                $password_correct =
                    password_verify(
                        $password,
                        $stored_password
                    )
                    || hash_equals(
                        $stored_password,
                        $password
                    );

                if ($password_correct) {

                    $_SESSION["username"] =
                        $maintenance["username"];

                    $_SESSION["role"] = "maintenance";

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


        /*
        |--------------------------------------------------------------------------
        | MANAGEMENT LOGIN
        |--------------------------------------------------------------------------
        */

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
                    (string)($management["password"] ?? "");

                $password_correct =
                    password_verify(
                        $password,
                        $stored_password
                    )
                    || hash_equals(
                        $stored_password,
                        $password
                    );

                if ($password_correct) {

                    $_SESSION["username"] =
                        $management["username"];

                    $_SESSION["role"] = "management";

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


        /*
        |--------------------------------------------------------------------------
        | FINAL LOGIN RESULT
        |--------------------------------------------------------------------------
        */

        if ($account_found) {

            $error = "Invalid username or password.";

        } elseif ($database_error) {

            $error = "Unable to check user account. Please verify the database tables and columns.";

        } else {

            $error = "Invalid username or password.";

        }

    }
}

?>