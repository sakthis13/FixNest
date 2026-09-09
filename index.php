<?php
session_start();

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"] ?? "");
    $password = trim($_POST["password"] ?? "");

    $users = [
        "student" => [
            "password" => "student123",
            "role" => "student",
            "redirect" => "student/dashboard.php"
        ],

        "admin" => [
            "password" => "admin123",
            "role" => "admin",
            "redirect" => "admin/dashboard.php"
        ],

        "boyswarden" => [
            "password" => "boys123",
            "role" => "warden",
            "hostel_type" => "Boys Hostel",
            "redirect" => "warden/dashboard.php"
        ],

        "girlswarden" => [
            "password" => "girls123",
            "role" => "warden",
            "hostel_type" => "Girls Hostel",
            "redirect" => "warden/dashboard.php"
        ],

        "maintenance" => [
            "password" => "maintenance123",
            "role" => "maintenance",
            "redirect" => "maintenance/dashboard.php"
        ],
        "management" => [
            "password" => "management123",
             "role" => "management",
             "redirect" => "management/dashboard.php"
        ]
    ];

    if (
        isset($users[$username]) &&
        $users[$username]["password"] === $password
    ) {
        $_SESSION["username"] = $username;
        $_SESSION["role"] = $users[$username]["role"];

        if (isset($users[$username]["hostel_type"])) {
            $_SESSION["hostel_type"] = $users[$username]["hostel_type"];
        }

        header("Location: " . $users[$username]["redirect"]);
        exit;
    } else {
        $error = "Invalid username or password.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | FixNest</title>

    <link rel="stylesheet" href="assets/style.css">

    <style>
        .login-button {
            width: 100%;
            margin-top: 12px;
            padding: 14px 20px;
            border: none;
            border-radius: 12px;
            background: linear-gradient(135deg, #2563eb, #1d4ed8);
            color: #ffffff;
            font-size: 16px;
            font-weight: 700;
            cursor: pointer;
            transition: all 0.25s ease;
            box-shadow: 0 8px 18px rgba(37, 99, 235, 0.22);
        }

        .login-button:hover {
            background: linear-gradient(135deg, #1d4ed8, #1e40af);
            transform: translateY(-2px);
            box-shadow: 0 12px 24px rgba(37, 99, 235, 0.30);
        }

        .login-button:active {
            transform: translateY(0);
        }

        .login-button:focus {
            outline: 3px solid rgba(37, 99, 235, 0.25);
            outline-offset: 3px;
        }

        .login-heading {
            margin-bottom: 24px;
        }

        .login-heading h2 {
            margin-bottom: 8px;
        }

        .login-heading p {
            color: #64748b;
            font-size: 14px;
        }

        .login-error {
            margin-bottom: 18px;
            padding: 12px 14px;
            border-radius: 10px;
            background: #fee2e2;
            color: #b91c1c;
            font-size: 14px;
            font-weight: 600;
        }

        .login-footer {
            margin-top: 22px;
            text-align: center;
            color: #94a3b8;
            font-size: 13px;
        }
    </style>
</head>

<body class="login-page">

    <div class="login-container">

        <div class="login-brand">
            <div class="login-logo">F</div>
            <h1>FixNest</h1>
            <p>Hostel Complaint Management System</p>
        </div>

        <div class="login-card">

            <div class="login-heading">
                <h2>Welcome Back</h2>
                <p>Login to manage your hostel complaints.</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="login-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-group">
                    <label for="username">Username</label>

                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="form-control"
                        placeholder="Enter your username"
                        required
                    >
                </div>

                <div class="form-group">
                    <label for="password">Password</label>

                    <input
                        type="password"
                        id="password"
                        name="password"
                        class="form-control"
                        placeholder="Enter your password"
                        required
                    >
                </div>

                <button type="submit" class="login-button">
                    Login
                </button>

            </form>

        </div>

        <p class="login-footer">
            © 2026 FixNest. Frontend Demo Version.
        </p>

    </div>

</body>
</html>