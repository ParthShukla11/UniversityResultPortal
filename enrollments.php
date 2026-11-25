
<?php
require_once "config/database.php";

$sql = "SELECT
            e.enrollment_id,
            s.student_name,
            s.prn,
            sub.subject_name,
            sem.semester_number,
            sem.academic_year
        FROM Enrollment e
        JOIN Student s ON e.student_id = s.student_id
        JOIN Subject sub ON e.subject_id = sub.subject_id
        JOIN Semester sem ON e.semester_id = sem.semester_id
        ORDER BY e.enrollment_id";

$enrollments = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Enrollment Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
    <h2>Centralised Student Result Management Portal</h2>
    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="faculty.php">Faculty</a>
    <a href="subjects.php">Subjects</a>
    <a href="semesters.php">Semesters</a>
    <a href="enrollments.php">Enrollments</a>
    <a href="results.php">Results</a>
</div>

<div class="main">
    <h1>Enrollment Management</h1>
    <p>View student subject enrollments.</p>

    <a href="add_enrollment.php">Add Enrollment</a>

    <div class="table-container">
        <table>
            <tr>
                <th>Enrollment ID</th>
                <th>Student Name</th>
                <th>PRN</th>
                <th>Subject</th>
                <th>Semester</th>
                <th>Academic Year</th>
            </tr>

            <?php foreach ($enrollments as $e): ?>
            <tr>
                <td><?= htmlspecialchars($e['enrollment_id']) ?></td>
                <td><?= htmlspecialchars($e['student_name']) ?></td>
                <td><?= htmlspecialchars($e['prn']) ?></td>
                <td><?= htmlspecialchars($e['subject_name']) ?></td>
                <td><?= htmlspecialchars($e['semester_number']) ?></td>
                <td><?= htmlspecialchars($e['academic_year']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
