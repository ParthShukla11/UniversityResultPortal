
<?php
require_once "config/database.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $number = $_POST['semester_number'];
    $year = trim($_POST['academic_year']);

    try {
        $sql = "INSERT INTO Semester
                (semester_number, academic_year)
                VALUES (:number, :year)";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            'number' => $number,
            'year' => $year
        ]);

        $message = "Semester added successfully!";

    } catch (PDOException $e) {
        $message = "Unable to add semester. Please check the details.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Semester</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>

<div class="sidebar">
    <h2>Centralised Student Result Management Portal</h2>
    <a href="index.php">Dashboard</a>
    <a href="semesters.php">Semesters</a>
</div>

<div class="main">
    <h1>Add New Semester</h1>

    <p><?= htmlspecialchars($message) ?></p>

    <form method="POST">

        <label>Semester Number</label>
        <input type="number" name="semester_number"
               min="1" max="8" required>

        <label>Academic Year</label>
        <input type="text" name="academic_year"
               placeholder="2026-27" required>

        <button type="submit">Add Semester</button>

    </form>
</div>

</body>
</html>
