<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to manage courses.";
    exit();
}

// Fetch courses created by the instructor
$query = "SELECT * FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle course deletion
if (isset($_GET['delete_id'])) {
    $course_id = $_GET['delete_id'];
    $deleteQuery = "DELETE FROM courses WHERE id = :course_id";
    $stmt = $conn->prepare($deleteQuery);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    if ($stmt->execute()) {
        echo "Course deleted successfully!";
    } else {
        echo "Error deleting course.";
    }
    header("Location: manage_courses.php");
    exit();
}
?>

 <h3>Your Courses</h3>
    <table border="1">
        <thead>
            <tr>
                <th>Course Name</th>
                <th>Course Code</th>
                <th>Description</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($courses as $course): ?>
                <tr>
                    <td><?php echo htmlspecialchars($course['course_name']); ?></td>
                    <td><?php echo htmlspecialchars($course['course_code']); ?></td>
                    <td><?php echo htmlspecialchars($course['description']); ?></td>
                    <td>
                        <a href="edit_course.php?id=<?php echo $course['id']; ?>">Edit</a> |
                        <a href="delete_course.php?id=<?php echo $course['id']; ?>" onclick="return confirm('Are you sure you want to delete this course?');">Delete</a>

                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
