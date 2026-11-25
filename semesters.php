
<?php
require_once "config/database.php";

$sql = "SELECT * FROM Semester ORDER BY semester_number";
$semesters = $conn->query($sql)->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Semester Management</title>
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
    <h1>Semester Management</h1>
    <p>View all academic semesters.</p>

    <a href="add_semester.php">Add New Semester</a>

    <div class="table-container">
        <table>
            <tr>
                <th>Semester ID</th>
                <th>Semester Number</th>
                <th>Academic Year</th>
            </tr>

            <?php foreach ($semesters as $s): ?>
            <tr>
                <td><?= htmlspecialchars($s['semester_id']) ?></td>
                <td><?= htmlspecialchars($s['semester_number']) ?></td>
                <td><?= htmlspecialchars($s['academic_year']) ?></td>
            </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

</body>
</html>
