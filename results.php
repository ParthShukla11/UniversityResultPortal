
<?php
session_start();
require_once "config/database.php";

// Faculty authentication
if (
    !isset($_SESSION['user_id']) ||
    !isset($_SESSION['faculty_id']) ||
    !isset($_SESSION['role']) ||
    $_SESSION['role'] !== 'faculty'
) {
    header("Location: login.php");
    exit();
}

$facultyId = $_SESSION['faculty_id'];
$message = "";
$messageType = "";

// CSRF protection
if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Check that a result belongs to this faculty
function facultyOwnsResult($conn, $resultId, $facultyId) {
    $sql = "SELECT r.result_id
            FROM Result r
            JOIN Enrollment e ON r.enrollment_id = e.enrollment_id
            JOIN Subject sub ON e.subject_id = sub.subject_id
            WHERE r.result_id = :result_id
            AND sub.faculty_id = :faculty_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':result_id' => $resultId,
        ':faculty_id' => $facultyId
    ]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

// Handle edit and delete requests
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $token = $_POST['csrf_token'] ?? '';

    if (!hash_equals($_SESSION['csrf_token'], $token)) {
        $message = "Invalid request. Please refresh the page.";
        $messageType = "error";

    } else {

        $action = $_POST['action'] ?? '';
        $resultId = $_POST['result_id'] ?? '';

        if (!filter_var($resultId, FILTER_VALIDATE_INT)) {
            $message = "Invalid result selected.";
            $messageType = "error";

        } elseif (!facultyOwnsResult($conn, $resultId, $facultyId)) {
            $message = "You are not authorized to modify this result.";
            $messageType = "error";

        } else {

            if ($action === "update") {

                $internal = $_POST['internal_marks'] ?? '';
                $external = $_POST['external_marks'] ?? '';

                if (
                    !is_numeric($internal) ||
                    !is_numeric($external) ||
                    (float)$internal < 0 ||
                    (float)$internal > 100 ||
                    (float)$external < 0 ||
                    (float)$external > 100
                ) {
                    $message = "Marks must be between 0 and 100.";
                    $messageType = "error";

                } else {
                    $sql = "UPDATE Result
                            SET internal_marks = :internal,
                                external_marks = :external
                            WHERE result_id = :result_id";

                    $stmt = $conn->prepare($sql);
                    $stmt->execute([
                        ':internal' => $internal,
                        ':external' => $external,
                        ':result_id' => $resultId
                    ]);

                    header("Location: results.php?message=updated");
                    exit();
                }

            } elseif ($action === "delete") {

                $sql = "DELETE FROM Result
                        WHERE result_id = :result_id";

                $stmt = $conn->prepare($sql);
                $stmt->execute([':result_id' => $resultId]);

                header("Location: results.php?message=deleted");
                exit();

            }
        }
    }
}

// Success messages after redirect
if (isset($_GET['message'])) {
    if ($_GET['message'] === 'updated') {
        $message = "Result updated successfully!";
        $messageType = "success";
    } elseif ($_GET['message'] === 'deleted') {
        $message = "Result deleted successfully!";
        $messageType = "success";
    }
}

// Search functionality
$search = trim($_GET['search'] ?? '');

// Fetch faculty-specific results
$sql = "SELECT
            r.result_id,
            s.student_name,
            s.prn,
            sub.subject_name,
            sub.subject_code,
            sem.semester_number,
            r.internal_marks,
            r.external_marks,
            (r.internal_marks + r.external_marks) AS total_marks
        FROM Result r
        JOIN Enrollment e ON r.enrollment_id = e.enrollment_id
        JOIN Student s ON e.student_id = s.student_id
        JOIN Subject sub ON e.subject_id = sub.subject_id
        JOIN Semester sem ON e.semester_id = sem.semester_id
        WHERE sub.faculty_id = :faculty_id";

$params = [':faculty_id' => $facultyId];

if ($search !== '') {
    $sql .= " AND (
                s.student_name LIKE :search_name
                OR s.prn LIKE :search_prn
              )";

    $params[':search_name'] = "%$search%";
    $params[':search_prn'] = "%$search%";
}

$sql .= " ORDER BY r.result_id DESC";

$stmt = $conn->prepare($sql);
$stmt->execute($params);
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch selected result for editing
$editResult = null;

if (isset($_GET['edit']) && filter_var($_GET['edit'], FILTER_VALIDATE_INT)) {

    $editId = $_GET['edit'];

    $sql = "SELECT
                r.result_id,
                s.student_name,
                s.prn,
                sub.subject_name,
                r.internal_marks,
                r.external_marks
            FROM Result r
            JOIN Enrollment e ON r.enrollment_id = e.enrollment_id
            JOIN Student s ON e.student_id = s.student_id
            JOIN Subject sub ON e.subject_id = sub.subject_id
            WHERE r.result_id = :result_id
            AND sub.faculty_id = :faculty_id";

    $stmt = $conn->prepare($sql);
    $stmt->execute([
        ':result_id' => $editId,
        ':faculty_id' => $facultyId
    ]);

    $editResult = $stmt->fetch(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Results | Faculty Dashboard</title>

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
            margin-bottom: 25px;
        }

        .header h1 {
            font-size: 28px;
            margin-bottom: 8px;
        }

        .header p {
            color: #64748b;
            font-size: 14px;
        }

        .message {
            padding: 13px 16px;
            border-radius: 7px;
            margin-bottom: 20px;
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

        .card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            margin-bottom: 25px;
        }

        .card h2 {
            font-size: 19px;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
            max-width: 400px;
            padding: 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            font-size: 14px;
        }

        button, .button {
            border: none;
            padding: 10px 16px;
            border-radius: 6px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
        }

        .primary {
            background: #2563eb;
            color: white;
        }

        .primary:hover {
            background: #1d4ed8;
        }

        .edit {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .delete {
            background: #fee2e2;
            color: #b91c1c;
        }

        .cancel {
            background: #e2e8f0;
            color: #334155;
        }

        .table-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 3px 12px rgba(0,0,0,0.04);
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 950px;
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

        .actions {
            display: flex;
            gap: 7px;
            align-items: center;
        }

        .actions form {
            display: inline;
        }

        .edit-form {
            max-width: 600px;
        }

        .edit-form label {
            display: block;
            font-weight: 600;
            font-size: 14px;
            margin-bottom: 8px;
        }

        .edit-form input {
            width: 100%;
            padding: 12px;
            margin-bottom: 18px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
        }

        .edit-form .buttons {
            display: flex;
            gap: 10px;
        }

        .total {
            font-weight: bold;
            color: #166534;
        }

        .empty {
            text-align: center;
            padding: 25px;
            color: #64748b;
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

            .search-form {
                flex-direction: column;
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
    <a href="results.php" class="active">Manage Results</a>
    <a href="add_result.php">Enter Marks</a>
    <a href="logout.php">Logout</a>
</div>

<div class="main">

    <div class="header">
        <h1>Manage Results</h1>
        <p>View, search, update and delete results for your assigned subjects.</p>
    </div>

    <?php if ($message !== ''): ?>
        <div class="message <?= $messageType ?>">
            <?= htmlspecialchars($message) ?>
        </div>
    <?php endif; ?>

    <?php if (isset($_GET['edit']) && !$editResult): ?>
        <div class="message error">
            Result not found or you are not authorized to edit it.
        </div>
    <?php endif; ?>

    <?php if ($editResult): ?>

        <div class="card">

            <h2>Edit Student Result</h2>

            <p style="margin-bottom:20px; color:#64748b;">
                <strong>Student:</strong>
                <?= htmlspecialchars($editResult['student_name']) ?>
                (<?= htmlspecialchars($editResult['prn']) ?>)
                <br>
                <strong>Subject:</strong>
                <?= htmlspecialchars($editResult['subject_name']) ?>
            </p>

            <form method="POST" class="edit-form">

                <input type="hidden" name="csrf_token"
                       value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                <input type="hidden" name="action" value="update">

                <input type="hidden" name="result_id"
                       value="<?= htmlspecialchars($editResult['result_id']) ?>">

                <label>Internal Marks (Out of 100)</label>
                <input type="number" name="internal_marks"
                       min="0" max="100" step="0.01"
                       value="<?= htmlspecialchars($editResult['internal_marks']) ?>"
                       required>

                <label>External Marks (Out of 100)</label>
                <input type="number" name="external_marks"
                       min="0" max="100" step="0.01"
                       value="<?= htmlspecialchars($editResult['external_marks']) ?>"
                       required>

                <div class="buttons">
                    <button type="submit" class="primary">Update Result</button>
                    <a href="results.php" class="button cancel">Cancel</a>
                </div>

            </form>

        </div>

    <?php endif; ?>

    <div class="table-card">

        <h2>Student Examination Results</h2>

        <form method="GET" class="search-form">

            <input type="text" name="search"
                   placeholder="Search by student name or PRN..."
                   value="<?= htmlspecialchars($search) ?>">

            <button type="submit" class="primary">Search</button>

            <a href="results.php" class="button cancel">Clear</a>

        </form>

        <table>

            <thead>
                <tr>
                    <th>Result ID</th>
                    <th>Student Name</th>
                    <th>PRN</th>
                    <th>Subject</th>
                    <th>Semester</th>
                    <th>Internal</th>
                    <th>External</th>
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>

            <tbody>

                <?php if (count($results) > 0): ?>

                    <?php foreach ($results as $r): ?>

                        <tr>
                            <td><?= htmlspecialchars($r['result_id']) ?></td>

                            <td><?= htmlspecialchars($r['student_name']) ?></td>

                            <td><?= htmlspecialchars($r['prn']) ?></td>

                            <td>
                                <?= htmlspecialchars($r['subject_code']) ?>
                                - <?= htmlspecialchars($r['subject_name']) ?>
                            </td>

                            <td><?= htmlspecialchars($r['semester_number']) ?></td>

                            <td><?= htmlspecialchars($r['internal_marks']) ?></td>

                            <td><?= htmlspecialchars($r['external_marks']) ?></td>

                            <td class="total">
                                <?= htmlspecialchars($r['total_marks']) ?>
                            </td>

                            <td>
                                <div class="actions">

                                    <a class="button edit"
                                       href="results.php?edit=<?= urlencode($r['result_id']) ?>">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          onsubmit="return confirm('Are you sure you want to delete this result?');">

                                        <input type="hidden" name="csrf_token"
                                               value="<?= htmlspecialchars($_SESSION['csrf_token']) ?>">

                                        <input type="hidden" name="action" value="delete">

                                        <input type="hidden" name="result_id"
                                               value="<?= htmlspecialchars($r['result_id']) ?>">

                                        <button type="submit" class="delete">
                                            Delete
                                        </button>

                                    </form>

                                </div>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr>
                        <td colspan="9" class="empty">
                            No results found.
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

</body>
</html>
