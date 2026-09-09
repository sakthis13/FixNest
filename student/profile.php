<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

$username = $_SESSION["username"] ?? "student";
$hostel_type = $_SESSION["hostel_type"] ?? "Hostel information not available";

$student_name = $_SESSION["student_name"] ?? "Student User";
$student_id = $_SESSION["student_id"] ?? "STU2026001";
$department = $_SESSION["department"] ?? "Computer Science";
$email = $_SESSION["email"] ?? "student@fixnest.com";
$phone = $_SESSION["phone"] ?? "9876543210";

$avatar_letter = strtoupper(substr($student_name, 0, 1));
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Profile - FixNest</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .navbar {
            background: #1d4ed8;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .brand {
            font-size: 23px;
            font-weight: bold;
        }

        .nav-right {
            display: flex;
            align-items: center;
            gap: 18px;
        }

        .welcome {
            font-size: 14px;
        }

        .logout {
            background: white;
            color: #1d4ed8;
            padding: 9px 15px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
            font-size: 13px;
        }

        .container {
            max-width: 1100px;
            margin: auto;
            padding: 35px 20px;
        }

        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            margin-bottom: 28px;
        }

        .page-header h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .page-header p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .back-btn {
            display: inline-block;
            background: #1d4ed8;
            color: white;
            padding: 12px 18px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 14px;
            font-weight: bold;
        }

        .back-btn:hover {
            background: #1e40af;
        }

        .profile-layout {
            display: grid;
            grid-template-columns: 310px 1fr;
            gap: 24px;
            align-items: start;
        }

        .card {
            background: white;
            border-radius: 16px;
            padding: 28px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .profile-card {
            text-align: center;
        }

        .profile-avatar {
            width: 100px;
            height: 100px;
            margin: 0 auto 18px;
            display: flex;
            justify-content: center;
            align-items: center;
            border-radius: 50%;
            background: #dbeafe;
            color: #1d4ed8;
            font-size: 42px;
            font-weight: bold;
        }

        .profile-card h2 {
            margin: 0 0 8px;
            font-size: 23px;
        }

        .profile-card p {
            margin: 0 0 18px;
            color: #6b7280;
            font-size: 14px;
        }

        .status {
            display: inline-block;
            padding: 8px 14px;
            background: #dcfce7;
            color: #166534;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .hostel-box {
            margin-top: 24px;
            padding: 14px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            border-radius: 10px;
            font-size: 14px;
            font-weight: bold;
        }

        .card-header {
            margin-bottom: 24px;
            padding-bottom: 18px;
            border-bottom: 1px solid #e5e7eb;
        }

        .card-header h2 {
            margin: 0 0 7px;
            font-size: 21px;
        }

        .card-header p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
        }

        .profile-details {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .profile-detail-item {
            padding: 16px;
            background: #f9fafb;
            border: 1px solid #e5e7eb;
            border-radius: 10px;
        }

        .profile-detail-item span {
            display: block;
            margin-bottom: 8px;
            color: #6b7280;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }

        .profile-detail-item strong {
            display: block;
            color: #111827;
            font-size: 15px;
            word-break: break-word;
        }

        .bottom-card {
            margin-top: 24px;
            background: white;
            padding: 22px 28px;
            border-radius: 16px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .bottom-card h3 {
            margin: 0 0 8px;
            font-size: 18px;
        }

        .bottom-card p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
            line-height: 1.6;
        }

        @media (max-width: 800px) {
            .profile-layout {
                grid-template-columns: 1fr;
            }

            .profile-details {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 18px;
            }

            .brand {
                font-size: 19px;
            }

            .welcome {
                display: none;
            }

            .container {
                padding: 25px 15px;
            }

            .page-header {
                align-items: flex-start;
                flex-direction: column;
            }

            .page-header h1 {
                font-size: 26px;
            }

            .back-btn {
                width: 100%;
                text-align: center;
            }

            .card,
            .bottom-card {
                padding: 22px 18px;
            }
        }
    </style>
</head>

<body>

    <div class="navbar">
        <div class="brand">FixNest</div>

        <div class="nav-right">
            <div class="welcome">
                Welcome, <?php echo htmlspecialchars($username); ?>
            </div>

            <a href="../index.php" class="logout">Logout</a>
        </div>
    </div>

    <div class="container">

        <div class="page-header">
            <div>
                <h1>My Profile</h1>
                <p>View your registered student account details.</p>
            </div>

            <a href="dashboard.php" class="back-btn">
                Back to Dashboard
            </a>
        </div>

        <div class="profile-layout">

            <div class="card profile-card">

                <div class="profile-avatar">
                    <?php echo htmlspecialchars($avatar_letter); ?>
                </div>

                <h2>
                    <?php echo htmlspecialchars($student_name); ?>
                </h2>

                <p>Hostel Student</p>

                <span class="status">Active Account</span>

                <div class="hostel-box">
                    Hostel:
                    <?php echo htmlspecialchars($hostel_type); ?>
                </div>

            </div>

            <div class="card">

                <div class="card-header">
                    <h2>Personal Information</h2>
                    <p>Your registered student details.</p>
                </div>

                <div class="profile-details">

                    <div class="profile-detail-item">
                        <span>Full Name</span>
                        <strong>
                            <?php echo htmlspecialchars($student_name); ?>
                        </strong>
                    </div>

                    <div class="profile-detail-item">
                        <span>Username</span>
                        <strong>
                            <?php echo htmlspecialchars($username); ?>
                        </strong>
                    </div>

                    <div class="profile-detail-item">
                        <span>Student ID</span>
                        <strong>
                            <?php echo htmlspecialchars($student_id); ?>
                        </strong>
                    </div>

                    <div class="profile-detail-item">
                        <span>Department</span>
                        <strong>
                            <?php echo htmlspecialchars($department); ?>
                        </strong>
                    </div>

                    <div class="profile-detail-item">
                        <span>Email</span>
                        <strong>
                            <?php echo htmlspecialchars($email); ?>
                        </strong>
                    </div>

                    <div class="profile-detail-item">
                        <span>Phone</span>
                        <strong>
                            <?php echo htmlspecialchars($phone); ?>
                        </strong>
                    </div>

                </div>

            </div>

        </div>

        <div class="bottom-card">
            <h3>Profile Information</h3>

            <p>
                Your profile details are displayed from your registered
                student information. Profile editing will be connected with
                the backend later.
            </p>
        </div>

    </div>

</body>
</html>