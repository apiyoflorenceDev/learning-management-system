<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to delete a course.";
    exit();
}

// Ensure course_id is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "Course ID is missing.";
    exit();
}

$course_id = $_GET['id'];

try {
    // Start transaction (ensures both deletions happen safely)
    $conn->beginTransaction();

    // Step 1: Delete related records from the grades table
    $deleteGradesQuery = "DELETE FROM grades WHERE course_id = :course_id";
    $stmtGrades = $conn->prepare($deleteGradesQuery);
    $stmtGrades->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmtGrades->execute();

    // Step 2: Delete related records from the topics table
    $deleteTopicsQuery = "DELETE FROM topics WHERE course_id = :course_id";
    $stmtTopics = $conn->prepare($deleteTopicsQuery);
    $stmtTopics->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmtTopics->execute();

    // Step 3: Delete related records from the enrollments table
    $deleteEnrollmentsQuery = "DELETE FROM enrollments WHERE course_id = :course_id";
    $stmtEnrollments = $conn->prepare($deleteEnrollmentsQuery);
    $stmtEnrollments->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmtEnrollments->execute();

    // Step 4: Now, delete the course from the courses table
    $deleteCourseQuery = "DELETE FROM courses WHERE id = :course_id AND instructor_id = :instructor_id";
    $stmtCourse = $conn->prepare($deleteCourseQuery);
    $stmtCourse->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmtCourse->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
    $stmtCourse->execute();

    // Commit transaction (finalize the changes)
    $conn->commit();

    // Redirect back to course management page with success message
    header("Location: course_management.php?success=1");
    exit();
} catch (PDOException $e) {
    // Rollback transaction if an error occurs
    $conn->rollBack();
    echo "Error: " . $e->getMessage();
}
?>
