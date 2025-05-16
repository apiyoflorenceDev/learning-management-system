<?php
include('student_dbcon.php');
session_start();

// Check if the instructor is logged in
if (!isset($_SESSION['instructor_id']) || empty($_SESSION['instructor_id'])) {
    echo "You need to be logged in to delete grades.";
    exit();
}

// Sanitize and validate inputs
if (isset($_GET['id']) && isset($_GET['course_id'])) {
    $student_id = filter_var($_GET['id'], FILTER_SANITIZE_NUMBER_INT);
    $course_id = filter_var($_GET['course_id'], FILTER_SANITIZE_NUMBER_INT);

    // Check if the values are valid numbers
    if (!is_numeric($student_id) || !is_numeric($course_id)) {
        echo "Invalid input.";
        exit();
    }

    // Prepare and execute delete query
    try {
        $query = "DELETE FROM grades WHERE student_id = :student_id AND course_id = :course_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);

        if ($stmt->execute()) {
            // Set session message before redirect
            $_SESSION['message'] = "Grade deleted successfully!";
            // Redirect to grades page with success message
            header("Location: grades.php");
            exit();
        } else {
            echo "Error deleting grade. Please try again.";
        }
    } catch (PDOException $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Invalid request. Missing parameters.";
}
?>
