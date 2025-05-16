<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in.";
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch courses assigned to the instructor
$query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle course selection
$course_id = isset($_GET['course_id']) ? $_GET['course_id'] : null;

$topics = [];
if ($course_id) {
    $query = "SELECT * FROM topics WHERE course_id = :course_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<h2>Manage Topics</h2>
<form method="GET">
    <label for="course_id">Select Course:</label>
    <select name="course_id" onchange="this.form.submit()">
        <option value="">-- Select a Course --</option>
        <?php foreach ($courses as $course): ?>
            <option value="<?php echo $course['id']; ?>" <?php echo ($course['id'] == $course_id) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($course['course_name']); ?>
            </option>
        <?php endforeach; ?>
    </select>
</form>

<?php if ($course_id && count($topics) > 0): ?>
    <h3>Topics under this Course</h3>
    <table border="1">
        <tr>
            <th>Topic Name</th>
            <th>Description</th>
            <th>Actions</th>
        </tr>
        <?php foreach ($topics as $topic): ?>
            <tr>
                <td><?php echo htmlspecialchars($topic['topic_name']); ?></td>
                <td><?php echo htmlspecialchars($topic['topic_description']); ?></td>
                <td>
                    <a href="edit_topic.php?topic_id=<?php echo $topic['id']; ?>">Edit</a> |
                    <a href="delete_topic.php?topic_id=<?php echo $topic['id']; ?>" onclick="return confirm('Are you sure?');">Delete</a>
                    
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
    <a href="instructor_dashboard.php">Go Back </a>
<?php elseif ($course_id): ?>
    <p>No topics found for this course.</p>
<?php endif; ?>
