<?php
include('student_dbcon.php'); // Database connection
session_start();

// Get instructor ID
$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to update courses.";
    exit();
}

// Check if the form is submitted
if (isset($_POST['course_id'], $_POST['course_name'], $_POST['course_code'], $_POST['description'])) {
    $course_id = $_POST['course_id'];
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $description = $_POST['description'];

    // Prepare update SQL statement
    $query = "UPDATE courses SET course_name = :course_name, course_code = :course_code, description = :description 
              WHERE id = :course_id AND instructor_id = :instructor_id";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':course_name', $course_name);
    $stmt->bindParam(':course_code', $course_code);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':instructor_id', $instructor_id);

    // Execute the query
    if ($stmt->execute()) {
        echo "Course updated successfully! <a href='instructor_dashboard.php'>Go Back</a>";
    } else {
        echo "Error updating course. <a href='instructor_settings.php'>Go Back</a>";
    }
} else {
    echo "Invalid request.";
}
?>
