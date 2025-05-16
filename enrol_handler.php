

<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to enroll students.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];

    $query = "INSERT INTO enrollments (student_id, course_id)
              VALUES (:student_id, :course_id)";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);

    if ($stmt->execute()) {
        echo "Student enrolled successfully! <a href='instructor_dashboard.php'>Go Back</a>";
    } else {
        echo "Error enrolling student.";
    }
}
?>
