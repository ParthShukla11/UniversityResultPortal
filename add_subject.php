
<?php
require_once "config/database.php";

$message = "";

// Fetch departments
$departments = $conn->query(
    "SELECT dept_id, dept_name FROM Department"
)->fetchAll(PDO::FETCH_ASSOC);

// Fetch faculty members
$faculty = $conn->query(
    "SELECT faculty_id, faculty_name FROM Faculty"
)->fetchAll(PDO::FETCH_ASSOC);

// Insert subject
if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $subject_code = trim($_POST["subject_code"]);
    $subject_name = trim($_POST["subject_name"]);
    $credits = $_POST["credits"];
    $dept_id = $_POST["dept_id"];
    $faculty_id = $_POST["faculty_id"];

    if (
        $subject_code == "" ||
        $subject_name == "" ||
        $credits == "" ||
        $dept_id == "" ||
        $faculty_id == ""
    ) {
        $message = "Please fill all required fields.";
    } else {
        try {
            $sql = "INSERT INTO Subject
                    (subject_code, subject_name, credits, dept_id, faculty_id)
                    VALUES
                    (:code, :name, :credits, :dept, :faculty)";

            $stmt = $conn->prepare($sql);

            $stmt->execute([
                "code" => $subject_code,
                "name" => $subject_name,
                "credits" => $credits,
                "dept" => $dept_id,
                "faculty" => $faculty_id
            ]);

            $message = "Subject added successfully!";

        } catch (PDOException $e) {
            $message = "Unable to add subject. Check whether the subject code already exists.";
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Subject</title>
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
    <a href="subjects.php">Subjects</a>
    <a href="add_subject.php">Add Subject</a>
</div>

<div class="main">

    <h1>Add New Subject</h1>
    <p>Enter the subject details below.</p>

    <?php if ($message != ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <form method="POST">

        <label>Subject Code *</label>
        <input type="text" name="subject_code" required>

        <label>Subject Name *</label>
        <input type="text" name="subject_name" required>

        <label>Credits *</label>
        <input type="number" name="credits" min="1" required>

        <label>Department *</label>
        <select name="dept_id" required>
            <option value="">Select Department</option>

            <?php foreach ($departments as $d): ?>
                <option value="<?php echo $d['dept_id']; ?>">
                    <?php echo htmlspecialchars($d['dept_name']); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <label>Faculty *</label>
        <select name="faculty_id" required>
            <option value="">Select Faculty</option>

            <?php foreach ($faculty as $f): ?>
                <option value="<?php echo $f['faculty_id']; ?>">
                    <?php echo htmlspecialchars($f['faculty_name']); ?>
                </option>
            <?php endforeach; ?>

        </select>

        <button type="submit">Add Subject</button>

    </form>

</div>

</body>
</html>
