
<?php
require_once "config/database.php";

$student = null;
$results = [];
$message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {

    $prn = trim($_POST["prn"]);

    // Find student
    $stmt = $conn->prepare(
        "SELECT s.student_id, s.prn, s.student_name,
                s.email, d.dept_name
         FROM Student s
         JOIN Department d ON s.dept_id = d.dept_id
         WHERE s.prn = :prn"
    );

    $stmt->execute(["prn" => $prn]);
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($student) {

        // Fetch student's results
        $sql = "SELECT
                    sub.subject_code,
                    sub.subject_name,
                    sub.credits,
                    sem.semester_number,
                    sem.academic_year,
                    r.internal_marks,
                    r.external_marks,
                    (r.internal_marks + r.external_marks)
                    AS total_marks
                FROM Result r
                JOIN Enrollment e
                    ON r.enrollment_id = e.enrollment_id
                JOIN Subject sub
                    ON e.subject_id = sub.subject_id
                JOIN Semester sem
                    ON e.semester_id = sem.semester_id
                WHERE e.student_id = :student_id
                ORDER BY sem.semester_number, sub.subject_name";

        $stmt = $conn->prepare($sql);
        $stmt->execute([
            "student_id" => $student["student_id"]
        ]);

        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (count($results) == 0) {
            $message = "No results available for this student yet.";
        }

    } else {
        $message = "Student not found. Please check your PRN.";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Student Result Report Card</title>
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
    <a href="student_result.php">Student Report Card</a>
</div>

<div class="main">

    <h1>Student Result Report Card</h1>

    <p>Enter your PRN to view your academic results.</p>

    <form method="POST">

        <label>Enter PRN</label>

        <input
            type="text"
            name="prn"
            placeholder="Enter your PRN"
            required
        >

        <button type="submit">View Result</button>

    </form>

    <?php if ($message != ""): ?>
        <p><?php echo htmlspecialchars($message); ?></p>
    <?php endif; ?>

    <?php if ($student && count($results) > 0): ?>

        <h2>Student Details</h2>

        <p><strong>Name:</strong>
            <?= htmlspecialchars($student['student_name']) ?>
        </p>

        <p><strong>PRN:</strong>
            <?= htmlspecialchars($student['prn']) ?>
        </p>

        <p><strong>Email:</strong>
            <?= htmlspecialchars($student['email'] ?? '') ?>
        </p>

        <p><strong>Department:</strong>
            <?= htmlspecialchars($student['dept_name']) ?>
        </p>

        <h2>Academic Results</h2>

        <div class="table-container">

            <table>
                <tr>
                    <th>Semester</th>
                    <th>Subject Code</th>
                    <th>Subject Name</th>
                    <th>Credits</th>
                    <th>Internal</th>
                    <th>External</th>
                    <th>Total</th>
                </tr>

                <?php foreach ($results as $r): ?>

                <tr>
                    <td>
                        <?= htmlspecialchars($r['semester_number']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['subject_code']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['subject_name']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['credits']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['internal_marks']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['external_marks']) ?>
                    </td>

                    <td>
                        <?= htmlspecialchars($r['total_marks']) ?>
                    </td>
                </tr>

                <?php endforeach; ?>

            </table>

        </div>

    <?php endif; ?>

</div>

</body>
</html>
