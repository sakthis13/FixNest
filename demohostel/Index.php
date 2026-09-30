<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $error = "Please enter username and password.";
    }

    /*
     * Add your database login verification here.
     */
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login - FixNest</title>

    <link rel="icon" type="image/png" href="images/image.png">

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }


        body {

            min-height: 100vh;

            background: #f4f7fa;

            display: flex;

            align-items: center;

            justify-content: center;

            padding: 20px;
        }


        /* ================= LOGIN BOX ================= */

        .login-container {

            width: 380px;

            background: white;

            padding: 40px;

            border-radius: 10px;

            box-shadow:
                0 5px 25px rgba(0, 0, 0, 0.08);
        }


        /* ================= LOGO ================= */

        .logo-area {

            display: flex;

            align-items: center;

            justify-content: center;

            gap: 10px;

            margin-bottom: 10px;
        }


        .logo-area img {

            width: 55px;

            height: 55px;

            object-fit: contain;
        }


        .logo-text {

            font-size: 30px;

            font-weight: bold;

            color: #075894;
        }


        .logo-text span {

            color: #ffb511;
        }


        /* ================= SUBTITLE ================= */

        .subtitle {

            text-align: center;

            color: #777;

            font-size: 14px;

            margin-bottom: 30px;
        }


        /* ================= ERROR ================= */

        .error {

            background: #ffecec;

            color: #c0392b;

            padding: 10px;

            border-radius: 5px;

            font-size: 13px;

            margin-bottom: 20px;

            text-align: center;
        }


        /* ================= FORM ================= */

        .form-group {

            margin-bottom: 20px;
        }


        label {

            display: block;

            font-size: 14px;

            font-weight: bold;

            color: #444;

            margin-bottom: 8px;
        }


        input {

            width: 100%;

            padding: 12px;

            border: 1px solid #ddd;

            border-radius: 5px;

            font-size: 14px;

            outline: none;
        }


        input:focus {

            border-color: #075894;
        }


        /* ================= LOGIN BUTTON ================= */

        .login-btn {

            width: 100%;

            padding: 13px;

            background: #075894;

            color: white;

            border: none;

            border-radius: 5px;

            font-size: 15px;

            cursor: pointer;
        }


        .login-btn:hover {

            background: #064879;
        }


        /* ================= BACK HOME ================= */

        .back-home {

            display: block;

            text-align: center;

            margin-top: 25px;

            color: #075894;

            text-decoration: none;

            font-size: 14px;
        }


        .back-home:hover {

            text-decoration: underline;
        }


        /* ================= MOBILE ================= */

        @media (max-width: 500px) {

            .login-container {

                width: 100%;

                padding: 30px 25px;
            }

        }

    </style>

</head>


<body>


    <!-- ================= LOGIN CONTAINER ================= -->

    <div class="login-container">


        <!-- ================= LOGO ================= -->

        <div class="logo-area">

            <img
                src="images/image.png"
                alt="FixNest Logo"
            >

            <div class="logo-text">
                Fix<span>Nest</span>
            </div>

        </div>


        <!-- ================= SUBTITLE ================= -->

        <p class="subtitle">

            Hostel Complaint Management System

        </p>


        <!-- ================= ERROR MESSAGE ================= -->

        <?php if ($error !== ""): ?>

            <div class="error">

                <?= htmlspecialchars($error) ?>

            </div>

        <?php endif; ?>


        <!-- ================= LOGIN FORM ================= -->

        <form method="POST" action="">


            <!-- USERNAME -->

            <div class="form-group">

                <label for="username">
                    Username
                </label>

                <input
                    type="text"
                    id="username"
                    name="username"
                    placeholder="Enter your username"
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
                    id="password"
                    name="password"
                    placeholder="Enter your password"
                    required
                >

            </div>


            <!-- LOGIN BUTTON -->

            <button
                type="submit"
                class="login-btn"
            >
                Login
            </button>


        </form>


        <!-- ================= BACK TO HOME ================= -->

        <a
            href="Home.php"
            class="back-home"
        >
            ← Back to Home
        </a>


    </div>


</body>

</html>