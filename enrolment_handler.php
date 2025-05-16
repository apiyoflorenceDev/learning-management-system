<?php
include('student_dbcon.php'); // Database connection
session_start();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $instructor_id = $_SESSION['instructor_id'];

    // Check if student exists in the database
    $checkStudentQuery = "SELECT id FROM students WHERE id = :student_id";
    $stmt = $conn->prepare($checkStudentQuery);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$student) {
        echo " The student does not exist. Please verify the Student ID and try again.<p><a href='enrolment.php'>Go Back</a></p>";
        exit();
    }

    // Check if the student is already enrolled
    $checkQuery = "SELECT * FROM enrollments WHERE student_id = :student_id AND course_id = :course_id";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();

    

    if ($stmt->rowCount() > 0) {
        echo "Student is already enrolled in this course.<p><a href='enrolment.php'>Go Back</a></p>";
        exit();
    } else {
        // Enroll student
        $query = "INSERT INTO enrollments (student_id, course_id, enrollment_date) VALUES (:student_id, :course_id, NOW())";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id);
        $stmt->bindParam(':course_id', $course_id);

        if ($stmt->execute()) {
            echo "Student enrolled successfully!<p><a href='instructor_dashboard.php'>Go Back</a></p>";
        } else {
            echo "Error enrolling student.<p><a href='enrolment.php'>Try Again</a></p>";
        }
    }
}
?>
