<?php

session_start();

require_once __DIR__ . "/../includes/db.php";

if (
    !isset($_SESSION["role"]) ||
    $_SESSION["role"] !== "warden"
) {
    header("Location: ../index.php");
    exit();
}

$warden_username = $_SESSION["username"] ?? "";
$warden_hostel = $_SESSION["hostel_type"] ?? "";

if ($warden_hostel === "") {

    if ($warden_username === "girlswarden") {
        $warden_hostel = "Girls Hostel";
    } else {
        $warden_hostel = "Boys Hostel";
    }
}

$success = "";
$error = "";

$location_type = "";
$room_number = "";
$floor_number = "";
$category = "";
$description = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $location_type = trim(
        $_POST["location_type"] ?? ""
    );

    $room_number = trim(
        $_POST["room_number"] ?? ""
    );

    $floor_number = trim(
        $_POST["floor_number"] ?? ""
    );

    $category = trim(
        $_POST["category"] ?? ""
    );

    $description = trim(
        $_POST["description"] ?? ""
    );

    if (
        $location_type === "" ||
        $category === "" ||
        $description === ""
    ) {

        $error = "Please fill all required fields.";

    } elseif (
        $location_type === "Room" &&
        $room_number === ""
    ) {

        $error = "Please enter the room number.";

    } elseif (
        (
            $location_type === "Common Bathroom" ||
            $location_type === "Lobby / Corridor"
        ) &&
        $floor_number === ""
    ) {

        $error = "Please select the floor number.";

    } elseif ($warden_hostel === "") {

        $error = "Unable to determine the warden hostel.";

    } elseif ($warden_username === "") {

        $error = "Unable to determine the warden username.";

    } else {

        $complaint_id =
            "FX-" .
            date("ymdHis") .
            rand(10, 99);

        $python =
            "C:\\Users\\SAKTHI S\\AppData\\Local\\Programs\\Python\\Python313\\python.exe";

        $api_path =
            realpath(__DIR__ . "/../ai/api.py");

        if ($api_path === false) {

            $error = "AI API file could not be found.";

        } else {

            $descriptorspec = [
                0 => ["pipe", "r"],
                1 => ["pipe", "w"],
                2 => ["pipe", "w"]
            ];

            $process = proc_open(
                [
                    $python,
                    $api_path,
                    $description,
                    $category
                ],
                $descriptorspec,
                $pipes,
                dirname($api_path)
            );

            if (is_resource($process)) {

                fclose($pipes[0]);

                $ai_output =
                    stream_get_contents($pipes[1]);

                $ai_error =
                    stream_get_contents($pipes[2]);

                fclose($pipes[1]);
                fclose($pipes[2]);

                $return_code =
                    proc_close($process);

                if ($return_code !== 0) {

                    $error =
                        "AI execution failed: " .
                        trim($ai_error);

                } else {

                    $ai_result =
                        json_decode(
                            trim($ai_output),
                            true
                        );

                    if (!is_array($ai_result)) {

                        $error =
                            "AI returned invalid JSON: " .
                            trim($ai_output);

                    } elseif (
                        isset($ai_result["error"])
                    ) {

                        $error =
                            "AI Error: " .
                            $ai_result["error"];

                    } else {

                        $severity =
                            trim(
                                (string)(
                                    $ai_result["severity"]
                                    ?? "Medium"
                                )
                            );

                        $severity_confidence =
                            (float)(
                                $ai_result[
                                    "severity_confidence"
                                ] ?? 0
                            );

                        $similar_count =
                            (int)(
                                $ai_result[
                                    "similar_count"
                                ] ?? 0
                            );

                        $recurring_value =
                            $ai_result["recurring"] ?? false;

                        if (
                            is_bool($recurring_value)
                        ) {

                            $recurring =
                                $recurring_value;

                        } elseif (
                            is_string($recurring_value)
                        ) {

                            $recurring =
                                in_array(
                                    strtolower(
                                        trim(
                                            $recurring_value
                                        )
                                    ),
                                    [
                                        "true",
                                        "1",
                                        "yes",
                                        "y"
                                    ],
                                    true
                                );

                        } elseif (
                            is_numeric($recurring_value
                        )) {

                            $recurring =
                                ((int)$recurring_value === 1);

                        } else {

                            $recurring = false;
                        }

                        $allowed_severities = [
                            "High",
                            "Medium",
                            "Low"
                        ];

                        if (
                            !in_array(
                                $severity,
                                $allowed_severities,
                                true
                            )
                        ) {

                            $severity = "Medium";
                        }

                        if (
                            $severity_confidence < 0
                        ) {

                            $severity_confidence = 0;

                        } elseif (
                            $severity_confidence > 1
                        ) {

                            $severity_confidence = 1;
                        }

                        try {

                            $sql = "
                                INSERT INTO complaints (
                                    complaint_id,
                                    student_id,
                                    hostel,
                                    location_type,
                                    room_number,
                                    floor_number,
                                    category,
                                    description,
                                    severity,
                                    severity_confidence,
                                    recurring,
                                    similar_count
                                )
                                VALUES (
                                    :complaint_id,
                                    :student_id,
                                    :hostel,
                                    :location_type,
                                    :room_number,
                                    :floor_number,
                                    :category,
                                    :description,
                                    :severity,
                                    :severity_confidence,
                                    :recurring,
                                    :similar_count
                                )
                            ";

                            $stmt =
                                $pdo->prepare($sql);

                            $stmt->bindValue(
                                ":complaint_id",
                                $complaint_id,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":student_id",
                                $warden_username,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":hostel",
                                $warden_hostel,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":location_type",
                                $location_type,
                                PDO::PARAM_STR
                            );

                            if ($room_number !== "") {

                                $stmt->bindValue(
                                    ":room_number",
                                    $room_number,
                                    PDO::PARAM_STR
                                );

                            } else {

                                $stmt->bindValue(
                                    ":room_number",
                                    null,
                                    PDO::PARAM_NULL
                                );
                            }

                            if ($floor_number !== "") {

                                $stmt->bindValue(
                                    ":floor_number",
                                    $floor_number,
                                    PDO::PARAM_STR
                                );

                            } else {

                                $stmt->bindValue(
                                    ":floor_number",
                                    null,
                                    PDO::PARAM_NULL
                                );
                            }

                            $stmt->bindValue(
                                ":category",
                                $category,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":description",
                                $description,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":severity",
                                $severity,
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":severity_confidence",
                                $severity_confidence
                            );

                            $stmt->bindValue(
                                ":recurring",
                                $recurring
                                    ? "TRUE"
                                    : "FALSE",
                                PDO::PARAM_STR
                            );

                            $stmt->bindValue(
                                ":similar_count",
                                $similar_count,
                                PDO::PARAM_INT
                            );

                            $stmt->execute();

                            $success =
                                "Complaint submitted successfully!<br>" .
                                "Complaint ID: " .
                                htmlspecialchars(
                                    $complaint_id
                                ) .
                                "<br>" .
                                "Raised By: " .
                                htmlspecialchars(
                                    $warden_username
                                ) .
                                "<br>" .
                                "Hostel: " .
                                htmlspecialchars(
                                    $warden_hostel
                                ) .
                                "<br>" .
                                "Category: " .
                                htmlspecialchars(
                                    $category
                                ) .
                                "<br>" .
                                "Severity: " .
                                htmlspecialchars(
                                    $severity
                                ) .
                                "<br>" .
                                "Recurring: " .
                                (
                                    $recurring
                                    ? "Yes"
                                    : "No"
                                ) .
                                "<br>" .
                                "Similar complaints: " .
                                htmlspecialchars(
                                    (string)$similar_count
                                );

                            $location_type = "";
                            $room_number = "";
                            $floor_number = "";
                            $category = "";
                            $description = "";

                        } catch (PDOException $e) {

                            $error =
                                "Complaint could not be saved: " .
                                $e->getMessage();
                        }
                    }
                }

            } else {

                $error =
                    "Could not start Python AI.";
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
        Raise Complaint - FixNest
    </title>

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

        .navbar-left {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .navbar h2 {
            margin: 0;
            font-size: 22px;
        }

        .hostel-label {
            margin-top: 4px;
            font-size: 13px;
            opacity: 0.9;
        }

        .back-dashboard {
            width: 38px;
            height: 38px;
            border-radius: 50%;
            background: white;
            color: #1d4ed8;
            display: flex;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            font-size: 22px;
            font-weight: bold;
        }

        .back-dashboard:hover {
            background: #eff6ff;
        }

        .logout {
            background: white;
            color: #1d4ed8;
            padding: 9px 15px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .logout:hover {
            background: #eff6ff;
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
            box-shadow:
                0 4px 18px
                rgba(0, 0, 0, 0.06);
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
            box-shadow:
                0 0 0 3px
                rgba(37, 99, 235, 0.12);
        }

        .form-group textarea {
            min-height: 150px;
            resize: vertical;
        }

        .required {
            color: #dc2626;
        }

        .hostel-box {
            margin-bottom: 22px;
            padding: 14px 16px;
            border-radius: 9px;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1e40af;
            font-size: 14px;
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
            margin-top: 5px;
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
                font-size: 16px;
            }

            .navbar-left {
                gap: 10px;
            }

            .back-dashboard {
                width: 34px;
                height: 34px;
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

    <div class="navbar-left">

        <a
            class="back-dashboard"
            href="dashboard.php"
            title="Back to Dashboard"
        >
            ←
        </a>

        <div>

            <h2>
                FixNest Warden
            </h2>

            <div class="hostel-label">
                <?= htmlspecialchars($warden_hostel) ?>
            </div>

        </div>

    </div>

    <a
        class="logout"
        href="../index.php"
    >
        Logout
    </a>

</div>


<div class="container">

    <div class="page-header">

        <h1>
            Raise a Complaint
        </h1>

        <p>
            Submit a maintenance complaint for your hostel.
        </p>

    </div>


    <div class="form-card">

        <?php if (!empty($success)): ?>

            <div class="alert alert-success">
                <?= $success ?>
            </div>

        <?php endif; ?>


        <?php if (!empty($error)): ?>

            <div class="alert alert-error">
                <?= htmlspecialchars($error) ?>
            </div>

        <?php endif; ?>


        <div class="hostel-box">

            <strong>Hostel:</strong>

            <?= htmlspecialchars($warden_hostel) ?>

            <br>

            <strong>Raised By:</strong>

            <?= htmlspecialchars($warden_username) ?>

        </div>


        <form
            method="POST"
            action=""
        >

            <div class="form-row">

                <div class="form-group">

                    <label for="location_type">

                        Complaint Location

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        id="location_type"
                        name="location_type"
                        required
                        onchange="handleLocationChange()"
                    >

                        <option value="">
                            Select complaint location
                        </option>

                        <option value="Room">
                            Room
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
                    style="display:none;"
                >

                    <label for="room_number">

                        Room Number

                        <span class="required">
                            *
                        </span>

                    </label>

                    <input
                        type="text"
                        id="room_number"
                        name="room_number"
                        placeholder="Example: B-204"
                        value="<?= htmlspecialchars(
                            $room_number
                        ) ?>"
                    >

                </div>


                <div
                    class="form-group"
                    id="floorNumberGroup"
                    style="display:none;"
                >

                    <label for="floor_number">

                        Floor Number

                        <span class="required">
                            *
                        </span>

                    </label>

                    <select
                        id="floor_number"
                        name="floor_number"
                    >

                        <option value="">
                            Select floor
                        </option>

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

                    Complaint Category

                    <span class="required">
                        *
                    </span>

                </label>

                <select
                    id="category"
                    name="category"
                    required
                >

                    <option value="">
                        Select complaint category
                    </option>

                    <option value="Electrical">
                        Electrical
                    </option>

                    <option value="Plumbing & Drainage">
                        Plumbing &amp; Drainage
                    </option>

                    <option value="Water Supply">
                        Water Supply
                    </option>

                    <option value="Drinking Water">
                        Drinking Water
                    </option>

                    <option value="Cleaning & Hygiene">
                        Cleaning &amp; Hygiene
                    </option>

                    <option value="Furniture & Room Fixtures">
                        Furniture &amp; Room Fixtures
                    </option>

                    <option value="Building Maintenance">
                        Building Maintenance
                    </option>

                    <option value="Doors & Locks">
                        Doors &amp; Locks
                    </option>

                    <option value="Internet / Network">
                        Internet / Network
                    </option>

                    <option value="Pest Control">
                        Pest Control
                    </option>

                    <option value="Other">
                        Other
                    </option>

                </select>

            </div>


            <div class="form-group">

                <label for="description">

                    Complaint Description

                    <span class="required">
                        *
                    </span>

                </label>

                <textarea
                    id="description"
                    name="description"
                    placeholder="Explain the complaint clearly..."
                    required
                ><?= htmlspecialchars(
                    $description
                ) ?></textarea>

            </div>


            <div class="info-box">

                The complaint will be automatically analyzed
                by the FixNest AI system to determine its
                severity and identify similar or recurring
                complaints.

            </div>


            <div class="button-row">

                <a
                    href="dashboard.php"
                    class="btn btn-secondary"
                >
                    Cancel
                </a>

                <button
                    type="submit"
                    class="btn btn-primary"
                >
                    Submit Complaint
                </button>

            </div>

        </form>

    </div>

</div>


<script>

function handleLocationChange() {

    const locationType =
        document.getElementById(
            "location_type"
        ).value;

    const roomGroup =
        document.getElementById(
            "roomNumberGroup"
        );

    const floorGroup =
        document.getElementById(
            "floorNumberGroup"
        );

    const roomInput =
        document.getElementById(
            "room_number"
        );

    const floorInput =
        document.getElementById(
            "floor_number"
        );

    roomGroup.style.display = "none";

    floorGroup.style.display = "none";

    roomInput.required = false;

    floorInput.required = false;

    if (locationType === "Room") {

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