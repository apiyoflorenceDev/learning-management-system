<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to view submissions.";
    exit();
}

// Fetch courses taught by the instructor
$query_courses = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt_courses = $conn->prepare($query_courses);
$stmt_courses->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
$stmt_courses->execute();
$courses = $stmt_courses->fetchAll(PDO::FETCH_ASSOC);

// Fetch topics based on the selected course
$topics = [];
if (isset($_GET['course_id'])) {
    $course_id = $_GET['course_id'];

    $query_topics = "SELECT id, topic_name FROM topics WHERE course_id = :course_id";
    $stmt_topics = $conn->prepare($query_topics);
    $stmt_topics->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt_topics->execute();
    $topics = $stmt_topics->fetchAll(PDO::FETCH_ASSOC);
}

// Fetch submitted assignments if course and topic are selected
$submissions = [];
if (isset($_GET['course_id']) && isset($_GET['topic_id'])) {
    $topic_id = $_GET['topic_id'];

    $query_submissions = "SELECT a.id AS assignment_id, a.title, a.due_date, 
                                 s.name AS student_name, s.email, 
                                 sub.file_path, sub.submission_date 
                          FROM submissionss sub
                          JOIN assignment a ON sub.assignment_id = a.id
                          JOIN students s ON sub.student_id = s.id
                          WHERE a.course_id = :course_id AND a.topic_id = :topic_id";
    
    $stmt_submissions = $conn->prepare($query_submissions);
    $stmt_submissions->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt_submissions->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
    $stmt_submissions->execute();
    $submissions = $stmt_submissions->fetchAll(PDO::FETCH_ASSOC);
}
?>
<link rel="stylesheet" href="view_submissions.css">
<h2>Submitted Assignments</h2>

<form method="GET" action="">
    <label for="course_id">Select Course:</label>
    <select name="course_id" id="course_id" required onchange="this.form.submit()">
        <option value="">-- Select a Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>" <?php if (isset($course_id) && $course_id == $course['id']) echo 'selected'; ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>

    <?php if (!empty($topics)) : ?>
        <label for="topic_id">Select Topic:</label>
        <select name="topic_id" id="topic_id" required>
            <option value="">-- Select a Topic --</option>
            <?php foreach ($topics as $topic): ?>
                <option value="<?php echo $topic['id']; ?>" <?php if (isset($topic_id) && $topic_id == $topic['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($topic['topic_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">View Submissions</button>
    <?php endif; ?>
</form>

<?php if (isset($submissions) && empty($submissions)): ?>
    <p>No submissions found for the selected course and topic.</p>
<?php elseif (isset($submissions)): ?>
    <table border="1">
        <thead>
            <tr>
                <th>Student Name</th>
                <th>Email</th>
                <th>Assignment Title</th>
                <th>Submission Date</th>
                <th>File</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($submissions as $submission): ?>
                <tr>
                    <td><?php echo htmlspecialchars($submission['student_name']); ?></td>
                    <td><?php echo htmlspecialchars($submission['email']); ?></td>
                    <td><?php echo htmlspecialchars($submission['title']); ?></td>
                    <td><?php echo htmlspecialchars($submission['submission_date']); ?></td>
                    <td>
                        <?php 
                        $file_path = "uploads/submissions/" . basename($submission['file_path']);
                        if (file_exists($file_path) && is_readable($file_path)): ?>
                            <a href="<?php echo htmlspecialchars($file_path); ?>" download>Download</a>
                        <?php else: ?>
                            <span style="color:red;">File not found or permission denied.</span>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>
