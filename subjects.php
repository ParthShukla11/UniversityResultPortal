
<?php
session_start();
require_once "config/database.php";

// Check faculty login
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['faculty_id']) ||
    $_SESSION['role'] !== 'faculty'
) {
    header("Location: login.php");
    exit();
}

$faculty_id = $_SESSION['faculty_id'];

// Fetch only subjects assigned to this faculty
$sql = "
    SELECT
        s.subject_id,
        s.subject_code,
        s.subject_name,
        s.credits,
        d.dept_name,
        COUNT(DISTINCT e.student_id) AS student_count
    FROM Subject s
    LEFT JOIN Department d
        ON s.dept_id = d.dept_id
    LEFT JOIN Enrollment e
        ON s.subject_id = e.subject_id
    WHERE s.faculty_id = ?
    GROUP BY
        s.subject_id,
        s.subject_code,
        s.subject_name,
        s.credits,
        d.dept_name
    ORDER BY s.subject_id
";

$stmt = $conn->prepare($sql);
$stmt->execute([$faculty_id]);

$subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>

    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Subjects | Faculty Portal</title>

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

        .summary {
            display: flex;
            gap: 20px;
            margin-bottom: 28px;
        }

        .summary-card {
            background: white;
            padding: 22px;
            border-radius: 10px;
            width: 230px;
            border-left: 4px solid #2864ed;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
        }

        .summary-card h3 {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 12px;
        }

        .summary-card h2 {
            font-size: 28px;
            color: #192951;
        }

        .table-container {
            background: white;
            padding: 25px;
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        .table-container h2 {
            margin-bottom: 22px;
            color: #192951;
            font-size: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            padding: 16px 12px;
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

        @media (max-width: 700px) {
            .sidebar {
                width: 190px;
            }

            .main {
                margin-left: 190px;
                padding: 20px;
            }

            .summary {
                flex-direction: column;
            }
        }

    </style>

</head>

<body>

    <!-- Sidebar -->

    <div class="sidebar">

        <h2>University Result Portal</h2>

        <a href="index.php">Dashboard</a>
        <a href="subjects.php" class="active">My Subjects</a>
        <a href="students.php">Student Records</a>
        <a href="results.php">Manage Results</a>
        <a href="add_result.php">Enter Marks</a>
        <a href="logout.php">Logout</a>

    </div>

    <!-- Main Content -->

    <div class="main">

        <h1>My Subjects</h1>

        <p class="subtitle">
            View all subjects assigned to your faculty account.
        </p>

        <!-- Subject Count -->

        <div class="summary">

            <div class="summary-card">

                <h3>Total Assigned Subjects</h3>

                <h2><?php echo count($subjects); ?></h2>

            </div>

        </div>

        <!-- Subjects Table -->

        <div class="table-container">

            <h2>Assigned Subject Details</h2>

            <table>

                <thead>

                    <tr>
                        <th>Subject ID</th>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Credits</th>
                        <th>Department</th>
                        <th>Students</th>
                    </tr>

                </thead>

                <tbody>

                    <?php if (count($subjects) > 0): ?>

                        <?php foreach ($subjects as $s): ?>

                            <tr>

                                <td>
                                    <?php echo htmlspecialchars(
                                        (string)$s['subject_id']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $s['subject_code']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $s['subject_name']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        (string)$s['credits']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $s['dept_name'] ?? 'N/A'
                                    ); ?>
                                </td>

                                <td>
                                    <span class="badge">
                                        <?php echo $s['student_count']; ?>
                                    </span>
                                </td>

                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="6" class="empty">
                                No subjects are currently assigned to you.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>
