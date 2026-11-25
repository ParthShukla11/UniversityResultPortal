
<?php
require_once "config/database.php";

$message = "";

// Fetch departments for dropdown
$departments = $conn->query(
    "SELECT dept_id, dept_name FROM Department"
)->fetchAll(PDO::FETCH_ASSOC);

// Add faculty when form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $faculty_name = trim($_POST["faculty_name"]);
    $email = trim($_POST["email"]);
    $dept_id = $_POST["dept_id"];

    if ($faculty_name == "" || $dept_id == "") {
        $message = "Please fill all required fields.";
    } else {
        try {
            $sql = "INSERT INTO Faculty
                    (faculty_name, email, dept_id)
                    VALUES (:name, :email, :dept)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                "name" => $faculty_name,
                "email" => $email == "" ? null : $email,
                "dept" => $dept_id
            ]);

            $message = "Faculty added successfully!";

        } catch (PDOException $e) {
            $message = "Unable to add faculty. Check whether the email already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Faculty - Centralised Student Result Management Portal</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

<div class="sidebar">
    <h2>Centralised Student Result Management Portal</h2>

    <a href="index.php">Dashboard</a>
    <a href="students.php">Students</a>
    <a href="add_student.php">Add Student</a>
    <a href="faculty.php">Faculty</a>
    <a href="add_faculty.php">Add Faculty</a>
</div>

<div class="main">

    <h1>Add New Faculty</h1>

    <p>Enter faculty details below.</p>

    <?php if ($message != ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Faculty Name *</label>
        <input type="text" name="faculty_name" required>

        <label>Email</label>
        <input type="email" name="email">

        <label>Department *</label>
        <select name="dept_id" required>
            <option value="">Select Department</option>

            <?php foreach ($departments as $d): ?>
                <option value="<?php echo $d['dept_id']; ?>">
                    <?php echo htmlspecialchars($d['dept_name']); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <button type="submit">Add Faculty</button>

    </form>

</div>

</body>
</html>
