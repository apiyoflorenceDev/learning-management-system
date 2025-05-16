<?php
include('student_dbcon.php');
session_start();

// Assume the student is logged in and their ID is stored in the session
$student_id = $_SESSION['student_id']; // Example: Replace with the actual session variable

if (!$student_id) {
    echo "You need to be logged in to view your profile.";
    exit();
}

// Query to fetch student's profile
$query = "SELECT name, email FROM students WHERE id = :student_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();

if ($stmt->rowCount() > 0) {
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
   
echo "<p>Name: " . htmlspecialchars($row['name']) . "</p>";
echo "<p>Email: " . htmlspecialchars($row['email']) . "</p>";
echo "<p><a href='student_dashboard.php'>Go Back</a></p>";
} else {
    echo "<p>Profile not found.</p>";
}
?>
