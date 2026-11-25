
<?php
session_start();

require_once __DIR__ . '/config/database.php';

// Check login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Only students can access this page
if ($_SESSION['role'] !== 'student') {
    header("Location: index.php");
    exit();
}

$studentId = (int) $_SESSION['student_id'];

// Fetch student details
$stmt = $conn->prepare(
    "SELECT student_id, prn, student_name, email,
            phone, admission_year
     FROM student
     WHERE student_id = ?"
);

$stmt->execute([$studentId]);
$student = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$student) {
    die("Student record not found.");
}

// Fetch subjects and results
$sql = "
    SELECT
        sem.semester_id,
        sem.semester_number,
        sem.academic_year,
        sub.subject_code,
        sub.subject_name,
        sub.credits,
        r.internal_marks,
        r.external_marks
    FROM enrollment e
    JOIN semester sem
        ON e.semester_id = sem.semester_id
    JOIN subject sub
        ON e.subject_id = sub.subject_id
    LEFT JOIN result r
        ON e.enrollment_id = r.enrollment_id
    WHERE e.student_id = ?
    ORDER BY sem.semester_number, sub.subject_code
";

$stmt = $conn->prepare($sql);
$stmt->execute([$studentId]);

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Group results by semester
$semesters = [];

foreach ($results as $row) {

    $semesterId = $row['semester_id'];

    if (!isset($semesters[$semesterId])) {
        $semesters[$semesterId] = [
            'number' => $row['semester_number'],
            'year' => $row['academic_year'],
            'subjects' => [],
            'total_marks' => 0,
            'completed' => 0
        ];
    }

    $semesters[$semesterId]['subjects'][] = $row;

    if (
        $row['internal_marks'] !== null &&
        $row['external_marks'] !== null
    ) {
        $total = (float)$row['internal_marks']
               + (float)$row['external_marks'];

        $semesters[$semesterId]['total_marks'] += $total;
        $semesters[$semesterId]['completed']++;
    }
}

$totalSubjects = count($results);
$completedResults = 0;
$totalMarks = 0;

foreach ($semesters as $semester) {
    $completedResults += $semester['completed'];
    $totalMarks += $semester['total_marks'];
}

function e($value) {
    return htmlspecialchars(
        (string)$value,
        ENT_QUOTES,
        'UTF-8'
    );
}
?>

<!DOCTYPE html>
<html lang="en">

<head>

<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">

<title>Student Dashboard | URP</title>

<style>

* {
    margin: 0;
    padding: 0;
    box-sizing: border-box;
}

body {
    font-family: 'Segoe UI', Arial, sans-serif;
    background: #f4f6fb;
    color: #192951;
}

/* SIDEBAR */

.sidebar {
    width: 255px;
    height: 100vh;
    background: #192951;
    position: fixed;
    top: 0;
    left: 0;
    padding: 25px 18px;
    display: flex;
    flex-direction: column;
    z-index: 100;
}

.logo {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 5px 10px 35px;
    color: white;
}

.logo-icon {
    background: #2864ed;
    width: 43px;
    height: 43px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 19px;
    font-weight: bold;
}

.logo h2 {
    font-size: 20px;
}

.logo p {
    font-size: 11px;
    color: #aebbd6;
    margin-top: 3px;
}

.nav-heading {
    color: #8594b3;
    font-size: 11px;
    font-weight: bold;
    letter-spacing: 1.2px;
    padding: 10px 13px;
    margin-bottom: 8px;
}

.nav-link {
    display: flex;
    align-items: center;
    gap: 14px;
    padding: 14px 15px;
    color: #c6d0e4;
    text-decoration: none;
    border-radius: 8px;
    margin-bottom: 7px;
    font-size: 14px;
    transition: 0.2s;
}

.nav-link:hover,
.nav-link.active {
    background: #2864ed;
    color: white;
}

.nav-icon {
    font-size: 19px;
    width: 22px;
    text-align: center;
}

.sidebar-bottom {
    margin-top: auto;
}

.sidebar-bottom .nav-link:hover {
    background: #dc3545;
}

/* MAIN CONTENT */

.main-content {
    margin-left: 255px;
    min-height: 100vh;
}

/* TOPBAR */

.topbar {
    height: 75px;
    background: white;
    display: flex;
    align-items: center;
    justify-content: space-between;
    padding: 0 35px;
    border-bottom: 1px solid #e8ecf4;
    position: sticky;
    top: 0;
    z-index: 50;
}

.topbar h3 {
    font-size: 17px;
}

.user-info {
    display: flex;
    align-items: center;
    gap: 12px;
}

.avatar {
    width: 40px;
    height: 40px;
    background: #e8efff;
    color: #2864ed;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 17px;
}

.user-info p {
    font-size: 14px;
    font-weight: bold;
}

.user-info small {
    color: #8994a8;
}

/* PAGE */

.container {
    max-width: 1400px;
    margin: auto;
    padding: 35px;
}

.welcome {
    margin-bottom: 28px;
}

.welcome h1 {
    font-size: 28px;
    margin-bottom: 9px;
}

.welcome p {
    color: #7a879d;
    font-size: 14px;
}

/* STATISTICS */

.cards {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 20px;
    margin-bottom: 30px;
}

.card {
    background: white;
    padding: 23px;
    border-radius: 12px;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    border: 1px solid #edf0f6;
}

.card-label {
    color: #7b879c;
    font-size: 13px;
    margin-bottom: 17px;
}

.card-value {
    color: #2864ed;
    font-size: 27px;
    font-weight: bold;
}

.card-note {
    color: #98a2b3;
    font-size: 12px;
    margin-top: 8px;
}

/* SECTIONS */

.section {
    background: white;
    padding: 27px;
    border-radius: 12px;
    margin-bottom: 25px;
    border: 1px solid #edf0f6;
    box-shadow: 0 3px 12px rgba(0,0,0,0.04);
    scroll-margin-top: 95px;
}

.section-title {
    font-size: 19px;
    margin-bottom: 23px;
    color: #192951;
}

/* PROFILE */

.profile-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 25px;
}

.profile-item label {
    display: block;
    color: #8994a8;
    font-size: 12px;
    margin-bottom: 8px;
}

.profile-item p {
    font-size: 15px;
    font-weight: 600;
    color: #263653;
}

/* RESULTS */

.semester-title {
    color: #2864ed;
    font-size: 16px;
    margin: 25px 0 15px;
}

.semester-title:first-child {
    margin-top: 0;
}

.table-wrapper {
    width: 100%;
    overflow-x: auto;
}

table {
    width: 100%;
    min-width: 720px;
    border-collapse: collapse;
}

th {
    background: #edf2ff;
    color: #263653;
    font-size: 13px;
    font-weight: 600;
    text-align: left;
    padding: 15px 13px;
}

td {
    padding: 15px 13px;
    font-size: 13px;
    border-bottom: 1px solid #edf0f6;
}

tbody tr:hover {
    background: #f8faff;
}

.status {
    background: #dcfce7;
    color: #15803d;
    padding: 5px 10px;
    border-radius: 20px;
    font-size: 11px;
    font-weight: bold;
}

.pending {
    background: #fff3cd;
    color: #a16207;
}

.semester-total {
    text-align: right;
    margin-top: 15px;
    font-size: 14px;
    color: #475569;
}

.no-data {
    color: #8994a8;
    padding: 15px 0;
}

/* FOOTER */

.footer {
    padding: 20px;
    text-align: center;
    color: #8994a8;
    font-size: 12px;
}

/* RESPONSIVE */

@media(max-width: 1100px) {
    .cards {
        grid-template-columns: repeat(2, 1fr);
    }

    .profile-grid {
        grid-template-columns: repeat(2, 1fr);
    }
}

@media(max-width: 750px) {

    .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        padding: 15px;
    }

    .logo {
        padding: 5px 10px 15px;
    }

    .nav-heading,
    .sidebar-bottom {
        display: none;
    }

    .sidebar nav {
        display: flex;
        flex-wrap: wrap;
        gap: 5px;
    }

    .nav-link {
        padding: 10px;
        margin: 0;
        font-size: 12px;
    }

    .main-content {
        margin-left: 0;
    }

    .topbar {
        position: relative;
        padding: 0 18px;
    }

    .container {
        padding: 20px 15px;
    }

    .welcome h1 {
        font-size: 23px;
    }
}

@media(max-width: 480px) {
    .cards {
        grid-template-columns: 1fr;
    }

    .profile-grid {
        grid-template-columns: 1fr;
    }

    .user-info p {
        font-size: 12px;
    }

    .section {
        padding: 18px;
    }
}

</style>

</head>

<body>

<!-- SIDEBAR -->

<aside class="sidebar">

    <div class="logo">

        <div class="logo-icon">URP</div>

        <div>
            <h2>University</h2>
            <p>RESULT PORTAL</p>
        </div>

    </div>

    <p class="nav-heading">MAIN MENU</p>

    <nav>

        <a href="#dashboard" class="nav-link active">
            <span class="nav-icon">⌂</span>
            Dashboard
        </a>

        <a href="#profile" class="nav-link">
            <span class="nav-icon">♙</span>
            My Profile
        </a>

        <a href="#results" class="nav-link">
            <span class="nav-icon">▤</span>
            My Results
        </a>

    </nav>

    <div class="sidebar-bottom">

        <p class="nav-heading">ACCOUNT</p>

        <a href="logout.php" class="nav-link">
            <span class="nav-icon">⇥</span>
            Logout
        </a>

    </div>

</aside>

<!-- MAIN CONTENT -->

<main class="main-content">

    <!-- TOPBAR -->

    <header class="topbar">

        <h3>Student Portal</h3>

        <div class="user-info">

            <div class="avatar">
                <?php echo e(strtoupper(substr($student['student_name'], 0, 1))); ?>
            </div>

            <div>
                <p><?php echo e($student['student_name']); ?></p>
                <small>Student Account</small>
            </div>

        </div>

    </header>

    <div class="container">

        <!-- DASHBOARD -->

        <section id="dashboard">

            <div class="welcome">

                <h1>
                    Welcome, <?php echo e($student['student_name']); ?>!
                </h1>

                <p>
                    Here's an overview of your academic activities
                    and examination results.
                </p>

            </div>

            <!-- STATISTICS -->

            <div class="cards">

                <div class="card">

                    <p class="card-label">PRN Number</p>

                    <div class="card-value" style="font-size:23px;">
                        <?php echo e($student['prn']); ?>
                    </div>

                    <p class="card-note">Your student identification</p>

                </div>

                <div class="card">

                    <p class="card-label">Enrolled Subjects</p>

                    <div class="card-value">
                        <?php echo $totalSubjects; ?>
                    </div>

                    <p class="card-note">Total registered subjects</p>

                </div>

                <div class="card">

                    <p class="card-label">Results Available</p>

                    <div class="card-value">
                        <?php echo $completedResults; ?>
                    </div>

                    <p class="card-note">Subjects with marks entered</p>

                </div>

                <div class="card">

                    <p class="card-label">Total Marks Obtained</p>

                    <div class="card-value">
                        <?php echo number_format($totalMarks, 2); ?>
                    </div>

                    <p class="card-note">Across available results</p>

                </div>

            </div>

        </section>

        <!-- PROFILE -->

        <section class="section" id="profile">

            <h2 class="section-title">My Profile</h2>

            <div class="profile-grid">

                <div class="profile-item">
                    <label>Student Name</label>
                    <p><?php echo e($student['student_name']); ?></p>
                </div>

                <div class="profile-item">
                    <label>PRN Number</label>
                    <p><?php echo e($student['prn']); ?></p>
                </div>

                <div class="profile-item">
                    <label>Email Address</label>
                    <p><?php echo e($student['email'] ?: 'Not provided'); ?></p>
                </div>

                <div class="profile-item">
                    <label>Phone Number</label>
                    <p><?php echo e($student['phone'] ?: 'Not provided'); ?></p>
                </div>

                <div class="profile-item">
                    <label>Admission Year</label>
                    <p><?php echo e($student['admission_year'] ?: 'Not provided'); ?></p>
                </div>

            </div>

        </section>

        <!-- RESULTS -->

        <section class="section" id="results">

            <h2 class="section-title">My Academic Results</h2>

            <?php if (empty($semesters)): ?>

                <p class="no-data">
                    No enrolled subjects found.
                </p>

            <?php else: ?>

                <?php foreach ($semesters as $semester): ?>

                    <h3 class="semester-title">

                        Semester <?php echo e($semester['number']); ?>

                        (<?php echo e($semester['year']); ?>)

                    </h3>

                    <div class="table-wrapper">

                        <table>

                            <thead>

                                <tr>
                                    <th>Subject Code</th>
                                    <th>Subject Name</th>
                                    <th>Credits</th>
                                    <th>Internal</th>
                                    <th>External</th>
                                    <th>Total</th>
                                    <th>Status</th>
                                </tr>

                            </thead>

                            <tbody>

                                <?php foreach ($semester['subjects'] as $subject): ?>

                                    <tr>

                                        <td>
                                            <?php echo e($subject['subject_code']); ?>
                                        </td>

                                        <td>
                                            <?php echo e($subject['subject_name']); ?>
                                        </td>

                                        <td>
                                            <?php echo e($subject['credits']); ?>
                                        </td>

                                        <?php if (
                                            $subject['internal_marks'] !== null &&
                                            $subject['external_marks'] !== null
                                        ): ?>

                                            <td>
                                                <?php echo e($subject['internal_marks']); ?>
                                            </td>

                                            <td>
                                                <?php echo e($subject['external_marks']); ?>
                                            </td>

                                            <td>
                                                <?php
                                                echo number_format(
                                                    (float)$subject['internal_marks'] +
                                                    (float)$subject['external_marks'],
                                                    2
                                                );
                                                ?>
                                            </td>

                                            <td>
                                                <span class="status">Available</span>
                                            </td>

                                        <?php else: ?>

                                            <td>-</td>
                                            <td>-</td>
                                            <td>-</td>

                                            <td>
                                                <span class="status pending">
                                                    Pending
                                                </span>
                                            </td>

                                        <?php endif; ?>

                                    </tr>

                                <?php endforeach; ?>

                            </tbody>

                        </table>

                    </div>

                    <div class="semester-total">

                        <strong>Total Marks Obtained:</strong>

                        <?php
                        echo number_format(
                            $semester['total_marks'],
                            2
                        );
                        ?>

                    </div>

                <?php endforeach; ?>

            <?php endif; ?>

        </section>

    </div>

    <footer class="footer">

        University Result Management Portal
        © <?php echo date('Y'); ?>

    </footer>

</main>

</body>
</html>
