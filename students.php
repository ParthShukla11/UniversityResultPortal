
<?php
session_start();
require_once "config/database.php";

// Check faculty authentication
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['faculty_id']) ||
    $_SESSION['role'] !== 'faculty'
) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Get search input
$search = trim($_GET['search'] ?? '');

// Fetch students enrolled in this faculty's subjects
$sql = "
    SELECT
        s.student_id,
        s.prn,
        s.student_name,
        s.email,
        s.phone,
        s.admission_year,
        d.dept_name,
        COUNT(DISTINCT sub.subject_id) AS subject_count,
        GROUP_CONCAT(
            DISTINCT CONCAT(sub.subject_code, ' - ', sub.subject_name)
            ORDER BY sub.subject_name
            SEPARATOR ', '
        ) AS subjects
    FROM Student s

    INNER JOIN Enrollment e
        ON s.student_id = e.student_id

    INNER JOIN Subject sub
        ON e.subject_id = sub.subject_id

    LEFT JOIN Department d
        ON s.dept_id = d.dept_id

    WHERE sub.faculty_id = :faculty_id
";

// Apply search filter
if ($search !== '') {
    $sql .= "
        AND (
            s.student_name LIKE :search_name
            OR s.prn LIKE :search_prn
        )
    ";
}

$sql .= "
    GROUP BY
        s.student_id,
        s.prn,
        s.student_name,
        s.email,
        s.phone,
        s.admission_year,
        d.dept_name
    ORDER BY s.student_id
";

$stmt = $conn->prepare($sql);

$params = [
    'faculty_id' => $faculty_id
];

if ($search !== '') {
    $params['search_name'] = "%$search%";
    $params['search_prn'] = "%$search%";
}

$stmt->execute($params);

$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Student Records | Faculty Portal</title>

    <style>

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background: #f3f6fc;
            color: #25334d;
        }

        /* Sidebar */

        .sidebar {
            width: 255px;
            height: 100vh;
            background: #192951;
            position: fixed;
            left: 0;
            top: 0;
            padding: 25px 15px;
            color: white;
        }

        .sidebar h2 {
            font-size: 19px;
            line-height: 1.5;
            margin-bottom: 35px;
            padding: 0 10px;
        }

        .sidebar a {
            display: block;
            text-decoration: none;
            color: #cbd5e1;
            padding: 14px 15px;
            margin-bottom: 7px;
            border-radius: 7px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2864ed;
            color: white;
        }

        /* Main Content */

        .main {
            margin-left: 255px;
            padding: 35px;
        }

        .main h1 {
            font-size: 27px;
            color: #192951;
            margin-bottom: 10px;
        }

        .subtitle {
            color: #64748b;
            margin-bottom: 28px;
        }

        /* Summary Card */

        .summary-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            width: 240px;
            border-left: 4px solid #2864ed;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }

        .summary-card h3 {
            color: #64748b;
            font-size: 14px;
            margin-bottom: 12px;
        }

        .summary-card h2 {
            font-size: 30px;
            color: #192951;
        }

        /* Search */

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 25px;
            flex-wrap: wrap;
        }

        .search-form input {
            padding: 12px 15px;
            width: 300px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        .search-form button {
            background: #2864ed;
            color: white;
            border: none;
            padding: 12px 22px;
            border-radius: 7px;
            cursor: pointer;
        }

        .search-form button:hover {
            background: #194fc5;
        }

        .search-form a {
            background: #e2e8f0;
            color: #334155;
            padding: 12px 18px;
            text-decoration: none;
            border-radius: 7px;
        }

        /* Table */

        .table-container {
            background: white;
            padding: 25px;
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        .table-container h2 {
            color: #192951;
            font-size: 20px;
            margin-bottom: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 15px 12px;
            text-align: left;
            border-bottom: 1px solid #edf0f5;
            font-size: 14px;
        }

        th {
            background: #f8faff;
            color: #64748b;
        }

        td {
            color: #334155;
        }

        .badge {
            background: #e8efff;
            color: #2864ed;
            padding: 6px 11px;
            border-radius: 20px;
            font-size: 12px;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #64748b;
        }

        .total {
            margin-top: 20px;
            color: #64748b;
            font-size: 14px;
        }

        @media (max-width: 700px) {

            .sidebar {
                width: 190px;
            }

            .main {
                margin-left: 190px;
                padding: 20px;
            }

            .search-form input {
                width: 100%;
            }

        }

    </style>

</head>

<body>

    <!-- Sidebar -->

    <div class="sidebar">

        <h2>University Result Portal</h2>

        <a href="index.php">Dashboard</a>
        <a href="subjects.php">My Subjects</a>
        <a href="students.php" class="active">Student Records</a>
        <a href="results.php">Manage Results</a>
        <a href="add_result.php">Enter Marks</a>
        <a href="logout.php">Logout</a>

    </div>

    <!-- Main Content -->

    <div class="main">

        <h1>Student Records</h1>

        <p class="subtitle">
            View students enrolled in your assigned subjects.
        </p>

        <!-- Total Students -->

        <div class="summary-card">

            <h3>Total Students</h3>

            <h2><?php echo count($students); ?></h2>

        </div>

        <!-- Search Form -->

        <form method="GET" action="students.php" class="search-form">

            <input
                type="text"
                name="search"
                placeholder="Search by student name or PRN..."
                value="<?php echo htmlspecialchars($search); ?>"
            >

            <button type="submit">Search</button>

            <a href="students.php">Clear</a>

        </form>

        <!-- Student Table -->

        <div class="table-container">

            <h2>My Students</h2>

            <table>

                <thead>

                    <tr>
                        <th>Student ID</th>
                        <th>PRN</th>
                        <th>Student Name</th>
                        <th>Email</th>
                        <th>Phone</th>
                        <th>Admission Year</th>
                        <th>Department</th>
                        <th>Subjects</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (count($students) > 0): ?>

                        <?php foreach ($students as $student): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars(
                                        (string)$student['student_id']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $student['prn']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $student['student_name']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $student['email'] ?? ''
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $student['phone'] ?? ''
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        (string)$student['admission_year']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $student['dept_name'] ?? 'N/A'
                                    ); ?>
                                </td>

                                <td>
                                    <span class="badge">
                                        <?php echo $student['subject_count']; ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="8" class="empty">
                                No students found in your assigned subjects.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

        <p class="total">
            Total Students Found:
            <strong><?php echo count($students); ?></strong>
        </p>

    </div>

</body>
</html>
