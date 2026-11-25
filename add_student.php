
<?php
require_once "config/database.php";

$message = "";

// Fetch departments for dropdown
$departments = $conn->query(
    "SELECT dept_id, dept_name FROM Department"
)->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $prn = trim($_POST['prn']);
    $name = trim($_POST['student_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $year = $_POST['admission_year'];
    $dept_id = $_POST['dept_id'];

    if ($prn === "" || $name === "" || $year === "" || $dept_id === "") {
        $message = "Please fill all required fields.";
    } else {
        try {
            $sql = "INSERT INTO Student
                    (prn, student_name, email, phone, admission_year, dept_id)
                    VALUES
                    (:prn, :name, :email, :phone, :year, :dept_id)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                'prn' => $prn,
                'name' => $name,
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'year' => $year,
                'dept_id' => $dept_id
            ]);

            $message = "Student registered successfully!";

        } catch (PDOException $e) {
            $message = "Registration failed. Check whether the PRN or email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Student - University Result Portal</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="sidebar">
    <h2>URP</h2>
    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="add_student.php">Add Student</a>
    <a href="#">Faculty</a>
    <a href="#">Subjects</a>
    <a href="#">Results</a>
</div>

<div class="main">

    <h1>Register New Student</h1>
    <p>Enter the student's details below.</p>

    <?php if ($message !== ""): ?>
        <p><strong><?= htmlspecialchars($message) ?></strong></p>
    <?php endif; ?>

    <form method="POST" class="student-form">

        <label>PRN *</label>
        <input type="text" name="prn" required>

        <label>Student Name *</label>
        <input type="text" name="student_name" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Phone</label>
        <input type="text" name="phone">

        <label>Admission Year *</label>
        <input type="number" name="admission_year"
               min="1901" max="2155" required>

        <label>Department *</label>
        <select name="dept_id" required>
            <option value="">Select Department</option>

            <?php foreach ($departments as $department): ?>
                <option value="<?= $department['dept_id'] ?>">
                    <?= htmlspecialchars($department['dept_name']) ?>
                </option>
            <?php endforeach; ?>

        </select>

        <button type="submit">Register Student</button>

    </form>

</div>

</body>
</html>
