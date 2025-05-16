<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to delete your profile.";
    exit();
}

$student_id = $_SESSION['student_id'];

$query = "DELETE FROM students WHERE id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

if ($stmt->execute()) {
    session_destroy();
    echo "Profile deleted successfully. <a href='index.php'> Go back</a>";
} else {
    echo "Error deleting profile. <a href='delete_student_profile.php'>Go Back to Delete again</a>";
}
?>
