<?php
session_start(); // Start the session before anything else
include('student_dbcon.php'); // Include your database connection file

// Check if student_id exists in session
if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to view your courses.";
    exit();
}

$student_id = $_SESSION['student_id']; // Retrieve student ID from session

// Fetch courses for the student
$query = "SELECT courses.course_name, courses.course_code, enrollments.enrollment_date
          FROM courses
          JOIN enrollments ON courses.id = enrollments.course_id
          WHERE enrollments.student_id = :student_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(":student_id", $student_id);
$stmt->execute();
$result = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Display courses
if ($result) {
    echo "<h3>Your Courses</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Course Name</th><th>Course Code</th><th>Enrollment Date</th></tr>";

    foreach ($result as $row) {
        echo "<tr><td>" . htmlspecialchars($row['course_name']) . "</td>
                  <td>" . htmlspecialchars($row['course_code']) . "</td>
                  <td>" . htmlspecialchars($row['enrollment_date']) . "</td></tr>";
    }

    echo "</table>";
} else {
    echo "<p>You are not enrolled in any courses.</p>";
}

?>
