<?php

session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "admin") {
    header("Location: ../index.php");
    exit();
}

require_once __DIR__ . "/../includes/db.php";

$error = "";
$success = "";


/* -------------------------
   HANDLE FORM SUBMISSION
-------------------------- */

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $student_id = trim($_POST["student_id"] ?? "");
    $student_name = trim($_POST["student_name"] ?? "");
    $gender = trim($_POST["gender"] ?? "");
    $department = trim($_POST["department"] ?? "");
    $email = trim($_POST["email"] ?? "");
    $phone = trim($_POST["phone"] ?? "");
    $password = $_POST["password"] ?? "";
    $confirm_password = $_POST["confirm_password"] ?? "";


    /* -------------------------
       VALIDATION
    -------------------------- */

    if (
        $student_id === "" ||
        $student_name === "" ||
        $gender === "" ||
        $department === "" ||
        $email === "" ||
        $phone === "" ||
        $password === "" ||
        $confirm_password === ""
    ) {

        $error = "Please fill in all required fields.";

    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {

        $error = "Please enter a valid email address.";

    } elseif (strlen($password) < 6) {

        $error = "Password must be at least 6 characters.";

    } elseif ($password !== $confirm_password) {

        $error = "Passwords do not match.";

    } else {

        try {

            /* -------------------------
               CHECK DUPLICATE STUDENT ID
            -------------------------- */

            $check = $pdo->prepare("
                SELECT student_id
                FROM students
                WHERE student_id = :student_id
            ");

            $check->execute([
                ":student_id" => $student_id
            ]);

            if ($check->fetch()) {

                $error = "A student with this Student ID already exists.";

            } else {

                /* -------------------------
                   HASH PASSWORD
                -------------------------- */

                $hashed_password = password_hash(
                    $password,
                    PASSWORD_DEFAULT
                );


                /* -------------------------
                   INSERT STUDENT
                -------------------------- */

                $sql = "
                    INSERT INTO students
                    (
                        student_id,
                        student_name,
                        gender,
                        department,
                        email,
                        phone,
                        password
                    )
                    VALUES
                    (
                        :student_id,
                        :student_name,
                        :gender,
                        :department,
                        :email,
                        :phone,
                        :password
                    )
                ";

                $stmt = $pdo->prepare($sql);

                $stmt->execute([

                    ":student_id" => $student_id,

                    ":student_name" => $student_name,

                    ":gender" => $gender,

                    ":department" => $department,

                    ":email" => $email,

                    ":phone" => $phone,

                    ":password" => $hashed_password

                ]);


                /* -------------------------
                   SUCCESS
                -------------------------- */

                header("Location: students.php?added=1");
                exit();
            }

        } catch (PDOException $e) {

            $error = "Database Error: " . $e->getMessage();
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

    <title>Add Student | FixNest Admin</title>


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


        /* -------------------------
           NAVBAR
        -------------------------- */

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


        /* -------------------------
           CONTAINER
        -------------------------- */

        .container {

            max-width: 850px;

            margin: 35px auto;

            padding: 0 20px;
        }


        /* -------------------------
           BACK
        -------------------------- */

        .back {

            display: inline-block;

            margin-bottom: 20px;

            color: #2563eb;

            text-decoration: none;

            font-weight: 600;
        }


        /* -------------------------
           HEADER
        -------------------------- */

        .page-header {

            margin-bottom: 25px;
        }


        .page-header h1 {

            font-size: 30px;

            margin-bottom: 8px;
        }


        .page-header p {

            color: #64748b;
        }


        /* -------------------------
           CARD
        -------------------------- */

        .card {

            background: white;

            padding: 30px;

            border-radius: 12px;

            box-shadow:
                0 3px 12px rgba(0,0,0,0.06);
        }


        /* -------------------------
           FORM GRID
        -------------------------- */

        .form-grid {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }


        .form-group {

            display: flex;

            flex-direction: column;
        }


        .full-width {

            grid-column: 1 / -1;
        }


        label {

            font-size: 14px;

            font-weight: bold;

            margin-bottom: 8px;

            color: #374151;
        }


        input,
        select {

            width: 100%;

            padding: 12px 13px;

            border: 1px solid #cbd5e1;

            border-radius: 7px;

            font-size: 15px;

            font-family: inherit;

            outline: none;
        }


        input:focus,
        select:focus {

            border-color: #2563eb;

            box-shadow:
                0 0 0 2px rgba(37,99,235,0.1);
        }


        .hint {

            margin-top: 6px;

            font-size: 12px;

            color: #64748b;
        }


        /* -------------------------
           PASSWORD SECTION
        -------------------------- */

        .password-section {

            grid-column: 1 / -1;

            background: #eff6ff;

            border: 1px solid #bfdbfe;

            padding: 20px;

            border-radius: 10px;
        }


        .password-section h2 {

            font-size: 17px;

            color: #1e40af;

            margin-bottom: 15px;
        }


        .password-grid {

            display: grid;

            grid-template-columns: repeat(2, 1fr);

            gap: 20px;
        }


        /* -------------------------
           ERROR
        -------------------------- */

        .error {

            background: #fee2e2;

            color: #991b1b;

            border: 1px solid #fecaca;

            padding: 13px 16px;

            border-radius: 7px;

            margin-bottom: 20px;

            font-size: 14px;
        }


        /* -------------------------
           BUTTONS
        -------------------------- */

        .buttons {

            margin-top: 25px;

            display: flex;

            gap: 12px;

            align-items: center;
        }


        .save-btn {

            background: #2563eb;

            color: white;

            border: none;

            border-radius: 7px;

            padding: 12px 22px;

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

            text-decoration: none;

            border-radius: 7px;

            padding: 12px 20px;

            font-size: 15px;

            font-weight: bold;
        }


        .cancel-btn:hover {

            background: #d1d5db;
        }


        /* -------------------------
           INFO
        -------------------------- */

        .info {

            margin-top: 20px;

            padding: 15px;

            background: #f8fafc;

            border-left: 4px solid #2563eb;

            border-radius: 6px;

            color: #475569;

            font-size: 13px;

            line-height: 1.6;
        }


        /* -------------------------
           RESPONSIVE
        -------------------------- */

        @media (max-width: 650px) {

            .container {

                padding: 0 15px;
            }


            .navbar {

                padding: 0 15px;
            }


            .logo {

                font-size: 18px;
            }


            .form-grid {

                grid-template-columns: 1fr;
            }


            .password-grid {

                grid-template-columns: 1fr;
            }


            .full-width {

                grid-column: auto;
            }


            .password-section {

                grid-column: auto;
            }


            .card {

                padding: 20px;
            }


            .buttons {

                flex-direction: column;

                align-items: stretch;
            }


            .save-btn,
            .cancel-btn {

                text-align: center;

                width: 100%;
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


    <a
        href="../index.php"
        class="logout"
    >
        Logout
    </a>

</nav>



<div class="container">


    <!-- BACK -->

    <a
        href="students.php"
        class="back"
    >
        ← Back to Students
    </a>



    <!-- HEADER -->

    <div class="page-header">

        <h1>
            Add Student
        </h1>

        <p>
            Create a new student account for FixNest.
        </p>

    </div>



    <!-- ERROR -->

    <?php if ($error !== ""): ?>

        <div class="error">

            <?= htmlspecialchars($error) ?>

        </div>

    <?php endif; ?>



    <!-- FORM CARD -->

    <div class="card">


        <form
            method="POST"
            action=""
        >


            <div class="form-grid">


                <!-- STUDENT ID -->

                <div class="form-group">

                    <label for="student_id">
                        Student ID
                    </label>

                    <input
                        type="text"
                        id="student_id"
                        name="student_id"
                        value="<?= htmlspecialchars($_POST["student_id"] ?? "") ?>"
                        placeholder="Enter student ID"
                        required
                    >

                </div>



                <!-- STUDENT NAME -->

                <div class="form-group">

                    <label for="student_name">
                        Student Name
                    </label>

                    <input
                        type="text"
                        id="student_name"
                        name="student_name"
                        value="<?= htmlspecialchars($_POST["student_name"] ?? "") ?>"
                        placeholder="Enter student name"
                        required
                    >

                </div>



                <!-- GENDER -->

                <div class="form-group">

                    <label for="gender">
                        Gender
                    </label>

                    <select
                        id="gender"
                        name="gender"
                        required
                    >

                        <option value="">
                            Select Gender
                        </option>

                        <option
                            value="Male"
                            <?= (($_POST["gender"] ?? "") === "Male") ? "selected" : "" ?>
                        >
                            Male
                        </option>

                        <option
                            value="Female"
                            <?= (($_POST["gender"] ?? "") === "Female") ? "selected" : "" ?>
                        >
                            Female
                        </option>

                    </select>

                    <div class="hint">
                        Hostel type will be determined automatically from gender.
                    </div>

                </div>



                <!-- DEPARTMENT -->

                <div class="form-group">

                    <label for="department">
                        Department
                    </label>

                    <input
                        type="text"
                        id="department"
                        name="department"
                        value="<?= htmlspecialchars($_POST["department"] ?? "") ?>"
                        placeholder="Enter department"
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
                        id="email"
                        name="email"
                        value="<?= htmlspecialchars($_POST["email"] ?? "") ?>"
                        placeholder="student@example.com"
                        required
                    >

                </div>



                <!-- PHONE -->

                <div class="form-group">

                    <label for="phone">
                        Phone
                    </label>

                    <input
                        type="tel"
                        id="phone"
                        name="phone"
                        value="<?= htmlspecialchars($_POST["phone"] ?? "") ?>"
                        placeholder="Enter phone number"
                        required
                    >

                </div>



                <!-- PASSWORD SECTION -->

                <div class="password-section">

                    <h2>
                        🔐 Student Login Credentials
                    </h2>


                    <div class="password-grid">


                        <!-- PASSWORD -->

                        <div class="form-group">

                            <label for="password">
                                Password
                            </label>

                            <input
                                type="password"
                                id="password"
                                name="password"
                                minlength="6"
                                placeholder="Enter password"
                                required
                            >

                            <div class="hint">
                                Minimum 6 characters.
                            </div>

                        </div>



                        <!-- CONFIRM PASSWORD -->

                        <div class="form-group">

                            <label for="confirm_password">
                                Confirm Password
                            </label>

                            <input
                                type="password"
                                id="confirm_password"
                                name="confirm_password"
                                minlength="6"
                                placeholder="Re-enter password"
                                required
                            >

                        </div>


                    </div>

                </div>


            </div>



            <!-- BUTTONS -->

            <div class="buttons">


                <button
                    type="submit"
                    class="save-btn"
                >
                    Add Student
                </button>


                <a
                    href="students.php"
                    class="cancel-btn"
                >
                    Cancel
                </a>


            </div>


        </form>


    </div>



    <!-- INFO -->

    <div class="info">

        <strong>Note:</strong>

        The student's password is securely hashed before being
        stored in the database. Hostel type and room number are
        not collected here because hostel allocation is handled
        separately.

    </div>


</div>


</body>

</html>