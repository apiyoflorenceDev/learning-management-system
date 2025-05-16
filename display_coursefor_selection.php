<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to view submissions.";
    exit();
}

// Fetch courses taught by the instructor
$query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<link rel="stylesheet" href="view_submissions.css">
<h2>View Submissions</h2>

<form method="GET" action="view_submissions.php">
    <label for="course_id">Select Course:</label>
    <select name="course_id" id="course_id" required>
        <option value="">-- Select a Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
        <?php endforeach; ?>
    </select>
    <button type="submit">View Submissions</button>
</form>
