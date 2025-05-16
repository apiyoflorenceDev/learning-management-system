<?php
// enroll.php
include('student_dbcon.php');
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];
$course_id = isset($_GET['course_id']) ? (int)$_GET['course_id'] : 0;

// Validate course_id
if ($course_id <= 0) {
    $_SESSION['error'] = "Invalid course selection";
    header('Location: enrolledcourse.php');
    exit();
}

try {
    // Check if already enrolled
    $check_stmt = $conn->prepare("SELECT * FROM enrollments WHERE student_id = ? AND course_id = ?");
    $check_stmt->execute([$student_id, $course_id]);
    
    if ($check_stmt->rowCount() > 0) {
        $_SESSION['error'] = "You are already enrolled in this course";
        header('Location: enrolled_course.php');
        exit();
    }

    // Enroll the student
    $enroll_stmt = $conn->prepare("INSERT INTO enrollments (student_id, course_id, enrollment_date) VALUES (?, ?, NOW())");
    $enroll_stmt->execute([$student_id, $course_id]);
    
    // Get course name for success message
    $course_stmt = $conn->prepare("SELECT course_name FROM courses WHERE id = ?");
    $course_stmt->execute([$course_id]);
    $course = $course_stmt->fetch(PDO::FETCH_ASSOC);
    
    $_SESSION['success'] = "Successfully enrolled in: " . htmlspecialchars($course['course_name']);
    header('Location: enrolled_course.php');
    exit();

} catch (PDOException $e) {
    $_SESSION['error'] = "Database error: " . $e->getMessage();
    header('Location: enrolled_course.php');
    exit();
}
?>