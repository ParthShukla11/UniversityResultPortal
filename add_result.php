
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

$facultyId = $_SESSION['faculty_id'];
$message = "";
$messageType = "";
$selectedEnrollmentId = "";

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $selectedEnrollmentId = $_POST['enrollment_id'] ?? "";
    $internal = $_POST['internal_marks'] ?? "";
    $external = $_POST['external_marks'] ?? "";

    if (
        !filter_var($selectedEnrollmentId, FILTER_VALIDATE_INT) ||
        !is_numeric($internal) ||
        !is_numeric($external) ||
        (float)$internal < 0 ||
        (float)$internal > 100 ||
        (float)$external < 0 ||
        (float)$external > 100
    ) {
        $message = "Please select a valid enrollment and enter marks between 0 and 100.";
        $messageType = "error";

    } else {
        try {
            // Verify that this enrollment belongs to a subject assigned to this faculty
            $checkSql = "SELECT e.enrollment_id
                         FROM Enrollment e
                         JOIN Subject sub
                         ON e.subject_id = sub.subject_id
                         WHERE e.enrollment_id = :enrollment_id
                         AND sub.faculty_id = :faculty_id";

            $checkStmt = $conn->prepare($checkSql);
            $checkStmt->execute([
                ':enrollment_id' => $selectedEnrollmentId,
                ':faculty_id' => $facultyId
            ]);

            if (!$checkStmt->fetch()) {
                $message = "You are not authorized to enter marks for this enrollment.";
                $messageType = "error";

            } else {
                // Insert a new result or update an existing result
                $sql = "INSERT INTO Result
                        (enrollment_id, internal_marks, external_marks)
                        VALUES (:enrollment_id, :internal_marks, :external_marks)
                        ON DUPLICATE KEY UPDATE
                        internal_marks = VALUES(internal_marks),
                        external_marks = VALUES(external_marks)";

                $stmt = $conn->prepare($sql);

                $stmt->execute([
                    ':enrollment_id' => $selectedEnrollmentId,
                    ':internal_marks' => $internal,
                    ':external_marks' => $external
                ]);

                $message = "Result saved successfully!";
                $messageType = "success";
            }

        } catch (PDOException $e) {
            $message = "Something went wrong while saving the result.";
            $messageType = "error";
        }
    }
}

// Fetch only enrollments belonging to this faculty's subjects
$sql = "SELECT
            e.enrollment_id,
            s.student_name,
            s.prn,
            sub.subject_name,
            sub.subject_code,
            sem.semester_number,
            r.internal_marks,
            r.external_marks
        FROM Enrollment e
        JOIN Student s
            ON e.student_id = s.student_id
        JOIN Subject sub
            ON e.subject_id = sub.subject_id
        JOIN Semester sem
            ON e.semester_id = sem.semester_id
        LEFT JOIN Result r
            ON e.enrollment_id = r.enrollment_id
        WHERE sub.faculty_id = :faculty_id
        ORDER BY sub.subject_name, s.student_name";

$stmt = $conn->prepare($sql);
$stmt->execute([':faculty_id' => $facultyId]);
$enrollments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Enter Marks | Faculty Dashboard</title>

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: Arial, sans-serif;
        }

        body {
            background: #f4f6fb;
            color: #1e293b;
        }

        .sidebar {
            width: 250px;
            height: 100vh;
            background: #111827;
            position: fixed;
            padding: 25px 18px;
            color: white;
        }

        .sidebar h2 {
            font-size: 19px;
            margin-bottom: 35px;
            line-height: 1.5;
        }

        .sidebar a {
            display: block;
            color: #cbd5e1;
            text-decoration: none;
            padding: 13px 15px;
            margin-bottom: 8px;
            border-radius: 7px;
            font-size: 14px;
        }

        .sidebar a:hover,
        .sidebar a.active {
            background: #2563eb;
            color: white;
        }

        .main {
            margin-left: 250px;
            padding: 35px;
        }

        .header {
            margin-bottom: 28px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        .card {
            background: white;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            max-width: 850px;
            margin-bottom: 25px;
        }

        .card h2 {
            font-size: 20px;
            margin-bottom: 22px;
        }

        label {
            display: block;
            font-size: 14px;
            font-weight: 600;
            margin-bottom: 9px;
        }

        select,
        input {
            width: 100%;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            margin-bottom: 20px;
            font-size: 14px;
            background: white;
        }

        select:focus,
        input:focus {
            outline: none;
            border-color: #2563eb;
        }

        .marks-row {
            display: flex;
            gap: 20px;
        }

        .marks-row div {
            flex: 1;
        }

        button {
            background: #2563eb;
            color: white;
            border: none;
            padding: 13px 24px;
            border-radius: 7px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
        }

        button:hover {
            background: #1d4ed8;
        }

        .message {
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
            max-width: 850px;
            font-size: 14px;
        }

        .success {
            background: #dcfce7;
            color: #166534;
        }

        .error {
            background: #fee2e2;
            color: #991b1b;
        }

        .info {
            color: #64748b;
            font-size: 13px;
            margin-top: -8px;
            margin-bottom: 20px;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        .table-card h2 {
            font-size: 19px;
            margin-bottom: 20px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 650px;
        }

        th, td {
            padding: 14px 12px;
            text-align: left;
            border-bottom: 1px solid #e5e7eb;
            font-size: 13px;
        }

        th {
            background: #f8fafc;
            color: #475569;
        }

        .status {
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            background: #dcfce7;
            color: #166534;
        }

        .pending {
            background: #fef3c7;
            color: #92400e;
        }

        @media(max-width: 800px) {
            .sidebar {
                width: 190px;
            }

            .main {
                margin-left: 190px;
                padding: 20px;
            }
        }

        @media(max-width: 600px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }

            .main {
                margin-left: 0;
            }

            .marks-row {
                flex-direction: column;
                gap: 0;
            }
        }
    </style>
</head>

<body>

<div class="sidebar">
    <h2>University Result Portal</h2>

    <a href="index.php">Dashboard</a>
    <a href="subjects.php">My Subjects</a>
    <a href="students.php">Student Records</a>
    <a href="results.php">Manage Results</a>
    <a href="add_result.php" class="active">Enter Marks</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <div class="header">
        <h1>Enter Student Marks</h1>
        <p>Add or update examination marks for students enrolled in your subjects.</p>
    </div>

    <?php if ($message !== ""): ?>
        <div class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <div class="card">

        <h2>Result Entry Form</h2>

        <?php if (count($enrollments) > 0): ?>

            <form method="POST">

                <label>Select Student Enrollment</label>

                <select name="enrollment_id" required>
                    <option value="">Select Student</option>

                    <?php foreach ($enrollments as $e): ?>
                        <option
                            value="<?= htmlspecialchars($e['enrollment_id']) ?>"
                            <?= (string)$selectedEnrollmentId === (string)$e['enrollment_id'] ? 'selected' : '' ?>
                        >
                            <?= htmlspecialchars($e['student_name']) ?>
                            (<?= htmlspecialchars($e['prn']) ?>) -
                            <?= htmlspecialchars($e['subject_code']) ?> -
                            <?= htmlspecialchars($e['subject_name']) ?> -
                            Semester <?= htmlspecialchars($e['semester_number']) ?>
                        </option>
                    <?php endforeach; ?>

                </select>

                <div class="marks-row">

                    <div>
                        <label>Internal Marks (Out of 100)</label>
                        <input
                            type="number"
                            name="internal_marks"
                            min="0"
                            max="100"
                            step="0.01"
                            required
                        >
                    </div>

                    <div>
                        <label>External Marks (Out of 100)</label>
                        <input
                            type="number"
                            name="external_marks"
                            min="0"
                            max="100"
                            step="0.01"
                            required
                        >
                    </div>

                </div>

                <p class="info">
                    If a result already exists, submitting new marks will update it.
                </p>

                <button type="submit">Save Result</button>

            </form>

        <?php else: ?>

            <p>No student enrollments are currently available for your assigned subjects.</p>

        <?php endif; ?>

    </div>

    <div class="table-card">

        <h2>Student Result Overview</h2>

        <table>
            <thead>
                <tr>
                    <th>Student Name</th>
                    <th>PRN</th>
                    <th>Subject</th>
                    <th>Semester</th>
                    <th>Internal</th>
                    <th>External</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>

                <?php foreach ($enrollments as $e): ?>
                    <tr>
                        <td><?= htmlspecialchars($e['student_name']) ?></td>
                        <td><?= htmlspecialchars($e['prn']) ?></td>
                        <td><?= htmlspecialchars($e['subject_name']) ?></td>
                        <td><?= htmlspecialchars($e['semester_number']) ?></td>

                        <td>
                            <?= $e['internal_marks'] !== null
                                ? htmlspecialchars($e['internal_marks'])
                                : '-' ?>
                        </td>

                        <td>
                            <?= $e['external_marks'] !== null
                                ? htmlspecialchars($e['external_marks'])
                                : '-' ?>
                        </td>

                        <td>
                            <?php if ($e['internal_marks'] !== null): ?>
                                <span class="status">Completed</span>
                            <?php else: ?>
                                <span class="status pending">Pending</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>

                <?php if (count($enrollments) === 0): ?>
                    <tr>
                        <td colspan="7">No records found.</td>
                    </tr>
                <?php endif; ?>

            </tbody>
        </table>

    </div>

</div>

</body>
</html>
