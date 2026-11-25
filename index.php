
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

// Fetch faculty details
$stmt = $conn->prepare("
    SELECT faculty_id, faculty_name, email, dept_id
    FROM Faculty
    WHERE faculty_id = ?
");
$stmt->execute([$faculty_id]);
$faculty = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$faculty) {
    die("Faculty record not found.");
}

// Count assigned subjects
$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM Subject
    WHERE faculty_id = ?
");
$stmt->execute([$faculty_id]);
$total_subjects = $stmt->fetchColumn();

// Count students enrolled in assigned subjects
$stmt = $conn->prepare("
    SELECT COUNT(DISTINCT e.student_id)
    FROM Enrollment e
    INNER JOIN Subject s
        ON e.subject_id = s.subject_id
    WHERE s.faculty_id = ?
");
$stmt->execute([$faculty_id]);
$total_students = $stmt->fetchColumn();

// Count results entered for assigned subjects
$stmt = $conn->prepare("
    SELECT COUNT(r.result_id)
    FROM Result r
    INNER JOIN Enrollment e
        ON r.enrollment_id = e.enrollment_id
    INNER JOIN Subject s
        ON e.subject_id = s.subject_id
    WHERE s.faculty_id = ?
");
$stmt->execute([$faculty_id]);
$total_results = $stmt->fetchColumn();

// Count pending results
$stmt = $conn->prepare("
    SELECT COUNT(*)
    FROM Enrollment e
    INNER JOIN Subject s
        ON e.subject_id = s.subject_id
    LEFT JOIN Result r
        ON e.enrollment_id = r.enrollment_id
    WHERE s.faculty_id = ?
      AND r.result_id IS NULL
");
$stmt->execute([$faculty_id]);
$pending_results = $stmt->fetchColumn();

// Fetch assigned subjects
$stmt = $conn->prepare("
    SELECT
        s.subject_id,
        s.subject_code,
        s.subject_name,
        s.credits,
        COUNT(DISTINCT e.student_id) AS student_count
    FROM Subject s
    LEFT JOIN Enrollment e
        ON s.subject_id = e.subject_id
    WHERE s.faculty_id = ?
    GROUP BY
        s.subject_id,
        s.subject_code,
        s.subject_name,
        s.credits
    ORDER BY s.subject_name
");
$stmt->execute([$faculty_id]);
$assigned_subjects = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Faculty Dashboard | University Result Portal</title>

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

        .topbar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
            gap: 15px;
        }

        .topbar h1 {
            font-size: 27px;
            color: #192951;
        }

        .profile {
            background: white;
            padding: 12px 18px;
            border-radius: 9px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.04);
        }

        .welcome {
            background: linear-gradient(120deg, #192951, #2864ed);
            color: white;
            padding: 28px;
            border-radius: 12px;
            margin-bottom: 28px;
        }

        .welcome h2 {
            font-size: 23px;
            margin-bottom: 10px;
        }

        .welcome p {
            color: #e0eaff;
            font-size: 14px;
        }

        .cards {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            margin-bottom: 35px;
        }

        .card {
            background: white;
            padding: 23px;
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            border-left: 4px solid #2864ed;
        }

        .card h3 {
            font-size: 14px;
            color: #64748b;
            margin-bottom: 15px;
        }

        .card h2 {
            font-size: 30px;
            color: #192951;
        }

        .section {
            background: white;
            padding: 25px;
            border-radius: 11px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
        }

        .section h2 {
            color: #192951;
            font-size: 20px;
            margin-bottom: 22px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
        }

        th, td {
            text-align: left;
            padding: 15px 12px;
            border-bottom: 1px solid #edf0f5;
            font-size: 14px;
        }

        th {
            color: #64748b;
            background: #f8faff;
        }

        td {
            color: #334155;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
        }

        .badge {
            background: #e8efff;
            color: #2864ed;
            padding: 6px 10px;
            border-radius: 20px;
            font-size: 12px;
        }

        .logout {
            margin-top: 35px;
        }

        @media (max-width: 1000px) {
            .cards {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media (max-width: 700px) {
            .sidebar {
                width: 190px;
            }

            .main {
                margin-left: 190px;
                padding: 20px;
            }

            .cards {
                grid-template-columns: 1fr;
            }

            .topbar {
                align-items: flex-start;
                flex-direction: column;
            }

            .section {
                overflow-x: auto;
            }
        }
    </style>
</head>

<body>

    <!-- Sidebar -->
    <div class="sidebar">

        <h2>University Result Portal</h2>

        <a href="index.php" class="active">Dashboard</a>
        <a href="subjects.php">My Subjects</a>
        <a href="students.php">Student Records</a>
        <a href="results.php">Manage Results</a>
        <a href="add_result.php">Enter Marks</a>

        <div class="logout">
            <a href="logout.php">Logout</a>
        </div>

    </div>

    <!-- Main Content -->
    <div class="main">

        <div class="topbar">

            <h1>Faculty Dashboard</h1>

            <div class="profile">
                Faculty ID: <?php echo htmlspecialchars(
                    (string)$faculty['faculty_id']
                ); ?>
            </div>

        </div>

        <!-- Welcome Section -->
        <div class="welcome">

            <h2>
                Welcome,
                <?php echo htmlspecialchars($faculty['faculty_name']); ?>!
            </h2>

            <p>
                Manage your assigned subjects, students and examination
                results from your faculty dashboard.
            </p>

        </div>

        <!-- Statistics -->
        <div class="cards">

            <div class="card">
                <h3>My Students</h3>
                <h2><?php echo $total_students; ?></h2>
            </div>

            <div class="card">
                <h3>Assigned Subjects</h3>
                <h2><?php echo $total_subjects; ?></h2>
            </div>

            <div class="card">
                <h3>Results Entered</h3>
                <h2><?php echo $total_results; ?></h2>
            </div>

            <div class="card">
                <h3>Pending Results</h3>
                <h2><?php echo $pending_results; ?></h2>
            </div>

        </div>

        <!-- Assigned Subjects -->
        <div class="section">

            <h2>My Assigned Subjects</h2>

            <table>

                <thead>
                    <tr>
                        <th>Subject Code</th>
                        <th>Subject Name</th>
                        <th>Credits</th>
                        <th>Students</th>
                    </tr>
                </thead>

                <tbody>

                    <?php if (count($assigned_subjects) > 0): ?>

                        <?php foreach ($assigned_subjects as $subject): ?>

                            <tr>
                                <td>
                                    <?php echo htmlspecialchars(
                                        $subject['subject_code']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        $subject['subject_name']
                                    ); ?>
                                </td>

                                <td>
                                    <?php echo htmlspecialchars(
                                        (string)$subject['credits']
                                    ); ?>
                                </td>

                                <td>
                                    <span class="badge">
                                        <?php echo $subject['student_count']; ?>
                                    </span>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    <?php else: ?>

                        <tr>
                            <td colspan="4" class="empty">
                                No subjects have been assigned to you yet.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </div>

    </div>

</body>
</html>
