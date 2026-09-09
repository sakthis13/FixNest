<?php
session_start();

if (!isset($_SESSION["role"]) || $_SESSION["role"] !== "student") {
    header("Location: ../index.php");
    exit();
}

$success = "";
$error = "";

$location_type = "";
$room_number = "";
$floor_number = "";
$category = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $location_type = trim($_POST["location_type"] ?? "");
    $room_number = trim($_POST["room_number"] ?? "");
    $floor_number = trim($_POST["floor_number"] ?? "");
    $category = trim($_POST["category"] ?? "");
    $description = trim($_POST["description"] ?? "");

    if (empty($location_type) || empty($category) || empty($description)) {
        $error = "Please fill all required fields.";
    } elseif ($location_type === "Room Number" && empty($room_number)) {
        $error = "Please enter your room number.";
    } elseif (
        ($location_type === "Common Bathroom" ||
        $location_type === "Lobby / Corridor") &&
        empty($floor_number)
    ) {
        $error = "Please select the floor number.";
    } else {
        $complaint_id = "FX-" . rand(1000, 9999);

        $success = "Complaint submitted successfully! Your Complaint ID is " . $complaint_id;

        $location_type = "";
        $room_number = "";
        $floor_number = "";
        $category = "";
        $description = "";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raise Complaint - FixNest</title>

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

        .navbar h2 {
            margin: 0;
            font-size: 22px;
        }

        .logout {
            background: white;
            color: #1d4ed8;
            padding: 9px 15px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            max-width: 850px;
            margin: auto;
            padding: 35px 20px;
        }

        .page-header {
            margin-bottom: 25px;
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

        .form-card {
            background: white;
            padding: 28px;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0, 0, 0, 0.06);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 18px;
        }

        .form-group {
            margin-bottom: 20px;
        }

        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-size: 14px;
            font-weight: bold;
            color: #374151;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            background: white;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: #2563eb;
            box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .required {
            color: #dc2626;
        }

        .alert {
            padding: 14px 16px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 14px;
            line-height: 1.5;
        }

        .alert-success {
            background: #dcfce7;
            border: 1px solid #86efac;
            color: #166534;
        }

        .alert-error {
            background: #fee2e2;
            border: 1px solid #fca5a5;
            color: #991b1b;
        }

        .info-box {
            margin-top: 10px;
            padding: 15px;
            border-radius: 9px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 13px;
            line-height: 1.6;
        }

        .button-row {
            display: flex;
            justify-content: flex-end;
            gap: 12px;
            margin-top: 25px;
        }

        .btn {
            border: none;
            border-radius: 8px;
            padding: 12px 20px;
            font-size: 14px;
            font-weight: bold;
            text-decoration: none;
            cursor: pointer;
        }

        .btn-primary {
            background: #1d4ed8;
            color: white;
        }

        .btn-primary:hover {
            background: #1e40af;
        }

        .btn-secondary {
            background: #e5e7eb;
            color: #374151;
        }

        .btn-secondary:hover {
            background: #d1d5db;
        }

        @media (max-width: 600px) {
            .navbar {
                padding: 16px 18px;
            }

            .navbar h2 {
                font-size: 18px;
            }

            .container {
                padding: 25px 15px;
            }

            .page-header h1 {
                font-size: 25px;
            }

            .form-card {
                padding: 20px;
            }

            .form-row {
                grid-template-columns: 1fr;
                gap: 0;
            }

            .button-row {
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

    <div class="navbar">
        <h2>FixNest Student</h2>
        <a class="logout" href="../index.php">Logout</a>
    </div>

    <div class="container">

        <div class="page-header">
            <h1>Raise a Complaint</h1>
            <p>Submit your hostel maintenance complaint to the concerned team.</p>
        </div>

        <div class="form-card">

            <?php if (!empty($success)): ?>
                <div class="alert alert-success">
                    <?php echo htmlspecialchars($success); ?>
                </div>
            <?php endif; ?>

            <?php if (!empty($error)): ?>
                <div class="alert alert-error">
                    <?php echo htmlspecialchars($error); ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">

                <div class="form-row">

                    <div class="form-group">
                        <label for="location_type">
                            Complaint Location <span class="required">*</span>
                        </label>

                        <select
                            id="location_type"
                            name="location_type"
                            required
                            onchange="handleLocationChange()"
                        >
                            <option value="">Select complaint location</option>

                            <option value="Room Number">
                                Room Number
                            </option>

                            <option value="Common Bathroom">
                                Common Bathroom
                            </option>

                            <option value="Lobby / Corridor">
                                Lobby / Corridor
                            </option>

                            <option value="Study Hall">
                                Study Hall
                            </option>
                        </select>
                    </div>

                    <div
                        class="form-group"
                        id="roomNumberGroup"
                        style="display: none;"
                    >
                        <label for="room_number">
                            Room Number <span class="required">*</span>
                        </label>

                        <input
                            type="text"
                            id="room_number"
                            name="room_number"
                            placeholder="Example: B-204"
                            value="<?php echo htmlspecialchars($room_number); ?>"
                        >
                    </div>

                    <div
                        class="form-group"
                        id="floorNumberGroup"
                        style="display: none;"
                    >
                        <label for="floor_number">
                            Floor Number <span class="required">*</span>
                        </label>

                        <select id="floor_number" name="floor_number">
                            <option value="">Select floor</option>

                            <option value="Ground Floor">
                                Ground Floor
                            </option>

                            <option value="1st Floor">
                                1st Floor
                            </option>

                            <option value="2nd Floor">
                                2nd Floor
                            </option>

                            <option value="3rd Floor">
                                3rd Floor
                            </option>
                        </select>
                    </div>

                </div>

                <div class="form-group">
                    <label for="category">
                        Complaint Category <span class="required">*</span>
                    </label>

                    <select id="category" name="category" required>
                        <option value="">Select complaint category</option>

                        <option value="Electrical">Electrical</option>
                        <option value="Plumbing & Drainage">Plumbing &amp; Drainage</option>
                        <option value="Water Supply">Water Supply</option>
                        <option value="Drinking Water">Drinking Water</option>
                        <option value="Cleaning & Hygiene">Cleaning &amp; Hygiene</option>
                        <option value="Furniture & Room Fixtures">Furniture &amp; Room Fixtures</option>
                        <option value="Building Maintenance">Building Maintenance</option>
                        <option value="Doors & Locks">Doors &amp; Locks</option>
                        <option value="Internet / Network">Internet / Network</option>
                        <option value="Pest Control">Pest Control</option>
                        <option value="Other">Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="description">
                        Complaint Description <span class="required">*</span>
                    </label>

                    <textarea
                        id="description"
                        name="description"
                        placeholder="Explain your complaint clearly..."
                        required
                    ><?php echo htmlspecialchars($description); ?></textarea>
                </div>

                <div class="info-box">
                    Please select the correct complaint location and category.
                    This will help the warden and maintenance team assign your
                    complaint to the right department.
                </div>

                <div class="button-row">
                    <a href="dashboard.php" class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        Submit Complaint
                    </button>
                </div>

            </form>

        </div>

    </div>

    <script>
        function handleLocationChange() {
            const locationType =
                document.getElementById("location_type").value;

            const roomGroup =
                document.getElementById("roomNumberGroup");

            const floorGroup =
                document.getElementById("floorNumberGroup");

            const roomInput =
                document.getElementById("room_number");

            const floorInput =
                document.getElementById("floor_number");

            roomGroup.style.display = "none";
            floorGroup.style.display = "none";

            roomInput.required = false;
            floorInput.required = false;

            if (locationType === "Room Number") {
                roomGroup.style.display = "block";
                roomInput.required = true;
            }

            if (
                locationType === "Common Bathroom" ||
                locationType === "Lobby / Corridor"
            ) {
                floorGroup.style.display = "block";
                floorInput.required = true;
            }
        }

        document.addEventListener(
            "DOMContentLoaded",
            handleLocationChange
        );
    </script>

</body>
</html>