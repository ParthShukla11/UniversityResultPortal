
<?php
require_once "config/database.php";

$sql = "SELECT f.faculty_id, f.faculty_name, f.email, d.dept_name
        FROM Faculty f
        JOIN Department d ON f.dept_id = d.dept_id
        ORDER BY f.faculty_id";

$stmt = $conn->query($sql);
$faculty = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html>
<head>
    <title>Faculty Management</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
    <h2>University Portal</h2>
    <a href="add_faculty.php">Add Faculty</a>
    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="add_student.php">Add Student</a>
    <a href="faculty.php">Faculty</a>
</div>

<div class="main">
    <h1>Faculty Management</h1>
    <p>View all registered faculty members.</p>

    <div class="table-container">
        <table>
            <tr>
                <th>Faculty ID</th>
                <th>Faculty Name</th>
                <th>Email</th>
                <th>Department</th>
            </tr>

            <?php foreach ($faculty as $f): ?>
            <tr>
                <td><?= htmlspecialchars($f['faculty_id']) ?></td>
                <td><?= htmlspecialchars($f['faculty_name']) ?></td>
                <td><?= htmlspecialchars($f['email'] ?? '') ?></td>
                <td><?= htmlspecialchars($f['dept_name']) ?></td>
            </tr>
            <?php endforeach; ?>

        </table>
    </div>
</div>

</body>
</html>
