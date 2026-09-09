<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Students - FixNest Warden</title>

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
            background: #0f766e;
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
            color: #0f766e;
            padding: 9px 14px;
            border-radius: 7px;
            text-decoration: none;
            font-weight: bold;
        }

        .container {
            padding: 30px;
            max-width: 1400px;
            margin: auto;
        }

        .top-section {
            display: flex;
            justify-content: space-between;
            align-items: end;
            gap: 20px;
            flex-wrap: wrap;
            margin-bottom: 22px;
        }

        .title-area h1 {
            margin: 0 0 8px;
            font-size: 30px;
        }

        .title-area p {
            margin: 0;
            color: #6b7280;
            font-size: 14px;
        }

        .filter-section {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
            align-items: end;
        }

        .filter-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .filter-group label {
            font-size: 13px;
            font-weight: bold;
            color: #374151;
        }

        input,
        select {
            padding: 11px 13px;
            min-width: 220px;
            border: 1px solid #d1d5db;
            border-radius: 7px;
            background: white;
            color: #111827;
            outline: none;
        }

        input:focus,
        select:focus {
            border-color: #0f766e;
            box-shadow: 0 0 0 3px rgba(15, 118, 110, 0.12);
        }

        .clear-btn {
            border: none;
            background: #e5e7eb;
            color: #374151;
            padding: 11px 15px;
            border-radius: 7px;
            cursor: pointer;
            font-weight: bold;
        }

        .clear-btn:hover {
            background: #d1d5db;
        }

        .table-box {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.08);
            overflow-x: auto;
        }

        table {
            width: 100%;
            min-width: 850px;
            border-collapse: collapse;
        }

        th,
        td {
            padding: 14px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
        }

        th {
            background: #ccfbf1;
            color: #115e59;
            font-size: 13px;
            white-space: nowrap;
        }

        td {
            font-size: 14px;
            color: #374151;
        }

        tbody tr:hover {
            background: #f9fafb;
        }

        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }

        .active {
            background: #dcfce7;
            color: #166534;
        }

        .inactive {
            background: #fee2e2;
            color: #991b1b;
        }

        .view-btn {
            padding: 8px 12px;
            border: none;
            border-radius: 6px;
            background: #0f766e;
            color: white;
            cursor: pointer;
            font-weight: bold;
        }

        .view-btn:hover {
            background: #115e59;
        }

        .empty {
            display: none;
            text-align: center;
            padding: 30px;
            color: #6b7280;
            font-size: 14px;
        }

        .result-count {
            margin-top: 15px;
            color: #6b7280;
            font-size: 13px;
        }

        @media (max-width: 700px) {
            .navbar {
                padding: 16px 18px;
            }

            .navbar h2 {
                font-size: 18px;
            }

            .container {
                padding: 20px 15px;
            }

            .title-area h1 {
                font-size: 25px;
            }

            .filter-section {
                width: 100%;
            }

            .filter-group {
                width: 100%;
            }

            input,
            select {
                width: 100%;
                min-width: 0;
            }

            .clear-btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<div class="navbar">
    <h2>FixNest Warden</h2>
    <a class="logout" href="../index.php">Logout</a>
</div>

<div class="container">

    <div class="top-section">

        <div class="title-area">
            <h1>Student Details</h1>
            <p>View and search hostel student information.</p>
        </div>

        <div class="filter-section">

            <div class="filter-group">
                <label for="searchInput">Search Student</label>

                <input
                    type="text"
                    id="searchInput"
                    placeholder="Name or student ID..."
                >
            </div>

            <div class="filter-group">
                <label for="blockFilter">Filter by Block</label>

                <select id="blockFilter">
                    <option value="">All Blocks</option>
                    <option value="Block A">Block A</option>
                    <option value="Block B">Block B</option>
                    <option value="Block C">Block C</option>
                </select>
            </div>

            <button
                type="button"
                class="clear-btn"
                onclick="clearFilters()"
            >
                Clear
            </button>

        </div>

    </div>

    <div class="table-box">

        <table id="studentTable">

            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Student Name</th>
                    <th>Email</th>
                    <th>Room Number</th>
                    <th>Block</th>
                    <th>Complaints</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>
            </thead>

            <tbody>

                <tr>
                    <td>21CS1045</td>
                    <td>Arun Kumar</td>
                    <td>arun@example.com</td>
                    <td>B-204</td>
                    <td>Block B</td>
                    <td>4</td>
                    <td>
                        <span class="status active">Active</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="view-btn"
                            onclick="viewStudent('Arun Kumar', 'B-204', 'Block B', '4')"
                        >
                            View
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>21IT1088</td>
                    <td>Priya S</td>
                    <td>priya@example.com</td>
                    <td>A-112</td>
                    <td>Block A</td>
                    <td>2</td>
                    <td>
                        <span class="status active">Active</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="view-btn"
                            onclick="viewStudent('Priya S', 'A-112', 'Block A', '2')"
                        >
                            View
                        </button>
                    </td>
                </tr>

                <tr>
                    <td>22ME2031</td>
                    <td>Vignesh R</td>
                    <td>vignesh@example.com</td>
                    <td>C-306</td>
                    <td>Block C</td>
                    <td>6</td>
                    <td>
                        <span class="status inactive">Inactive</span>
                    </td>
                    <td>
                        <button
                            type="button"
                            class="view-btn"
                            onclick="viewStudent('Vignesh R', 'C-306', 'Block C', '6')"
                        >
                            View
                        </button>
                    </td>
                </tr>

            </tbody>

        </table>

        <div id="emptyMessage" class="empty">
            No students found.
        </div>

        <div id="resultCount" class="result-count"></div>

    </div>

</div>

<script>
    const searchInput = document.getElementById("searchInput");
    const blockFilter = document.getElementById("blockFilter");
    const studentRows = document.querySelectorAll("#studentTable tbody tr");
    const emptyMessage = document.getElementById("emptyMessage");
    const resultCount = document.getElementById("resultCount");

    function searchStudents() {
        const searchText = searchInput.value.toLowerCase().trim();
        const selectedBlock = blockFilter.value.toLowerCase();

        let visibleRows = 0;

        studentRows.forEach(function(row) {
            const rowText = row.innerText.toLowerCase();
            const matchesSearch = rowText.includes(searchText);
            const matchesBlock =
                selectedBlock === "" || rowText.includes(selectedBlock);

            if (matchesSearch && matchesBlock) {
                row.style.display = "";
                visibleRows++;
            } else {
                row.style.display = "none";
            }
        });

        emptyMessage.style.display =
            visibleRows === 0 ? "block" : "none";

        resultCount.innerText =
            visibleRows + " student(s) found";
    }

    function clearFilters() {
        searchInput.value = "";
        blockFilter.value = "";
        searchStudents();
    }

    function viewStudent(name, room, block, complaints) {
        alert(
            "Student Name: " + name +
            "\nRoom Number: " + room +
            "\nBlock: " + block +
            "\nTotal Complaints: " + complaints
        );
    }

    searchInput.addEventListener("input", searchStudents);
    blockFilter.addEventListener("change", searchStudents);

    searchStudents();
</script>

</body>
</html>