<?php
include('student_dbcon.php'); // Database connection
session_start();

if (isset($_GET['student_id'])) {
    $student_id = $_GET['student_id'];

    // Delete student enrollment
    $query = "DELETE FROM enrollments WHERE student_id = :student_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);

    if ($stmt->execute()) {
        echo "Student unenrolled successfully!<p><a href='instructor_dashboard.php'>Go Back</a></p>";
    } else {
        echo "Error unenrolling student.<p><a href='enrolment.php'>Try Again</a></p>";
    }
}
?>
