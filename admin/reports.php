<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reports - FixNest Admin</title>

    <style>
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #f3f4f6;
            color: #111827;
        }

        .navbar {
            background: #3730a3;
            color: white;
            padding: 18px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .navbar h2 {
            margin: 0;
        }

        .logout {
            background: white;
            color: #3730a3;
            padding: 8px 14px;
            border-radius: 6px;
            text-decoration: none;
        }

        .container {
            padding: 30px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-top: 20px;
        }

        .card {
            background: white;
            padding: 22px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .card h3 {
            margin: 0;
            color: #6b7280;
            font-size: 15px;
        }

        .card p {
            margin: 12px 0 0;
            font-size: 30px;
            font-weight: bold;
            color: #3730a3;
        }

        .report-box {
            background: white;
            padding: 25px;
            margin-top: 30px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.08);
        }

        .bar-row {
            margin: 20px 0;
        }

        .bar-label {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }

        .bar {
            width: 100%;
            height: 18px;
            background: #e5e7eb;
            border-radius: 20px;
            overflow: hidden;
        }

        .bar-fill {
            height: 100%;
            border-radius: 20px;
        }

        .pending-fill {
            width: 35%;
            background: #f59e0b;
        }

        .progress-fill {
            width: 45%;
            background: #3b82f6;
        }

        .resolved-fill {
            width: 75%;
            background: #22c55e;
        }

        @media (max-width: 800px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 500px) {
            .container {
                padding: 15px;
            }

            .cards {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>

<div class="navbar">
    <h2>FixNest Admin</h2>
    <a class="logout" href="../index.php">Logout</a>
</div>

<div class="container">
    <h1>Complaint Reports</h1>

    <div class="cards">
        <div class="card">
            <h3>Total Complaints</h3>
            <p>120</p>
        </div>

        <div class="card">
            <h3>Pending</h3>
            <p>35</p>
        </div>

        <div class="card">
            <h3>In Progress</h3>
            <p>45</p>
        </div>

        <div class="card">
            <h3>Resolved</h3>
            <p>40</p>
        </div>
    </div>

    <div class="report-box">
        <h2>Complaint Status Overview</h2>

        <div class="bar-row">
            <div class="bar-label">
                <span>Pending Complaints</span>
                <strong>35%</strong>
            </div>
            <div class="bar">
                <div class="bar-fill pending-fill"></div>
            </div>
        </div>

        <div class="bar-row">
            <div class="bar-label">
                <span>In Progress Complaints</span>
                <strong>45%</strong>
            </div>
            <div class="bar">
                <div class="bar-fill progress-fill"></div>
            </div>
        </div>

        <div class="bar-row">
            <div class="bar-label">
                <span>Resolved Complaints</span>
                <strong>75%</strong>
            </div>
            <div class="bar">
                <div class="bar-fill resolved-fill"></div>
            </div>
        </div>
    </div>
</div>

</body>
</html>