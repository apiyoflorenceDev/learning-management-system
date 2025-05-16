<?php
// Include database connection
include('db_connection.php');

// Start the session to get instructor ID
session_start();
$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to manage students.";
    exit();
}


// Handle Remove Student
if (isset($_POST['remove_student_id']) && $_POST['action'] == 'remove') {
    $student_id = $_POST['remove_student_id'];
    $course_id = $_POST['course_id']; // Hidden field for course ID

    // Prepare SQL to remove student from course
    $query = "DELETE FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);

    if ($stmt->execute()) {
        echo "Student removed successfully!<p><a href='instructor_dashboard.php'>Go Back</a></p>";
    } else {
        echo "Error removing student.<p><a href='instructor_dashboard.php'>Go Back</a></p>";
    }
}
?>
