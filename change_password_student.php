<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to change your password.";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    if ($new_password !== $confirm_new_password) {
        echo "New passwords do not match!<a href='settings_student.php'>Try Again</a> ";
        exit();
    }

    // Fetch current password from the database
    $query = "SELECT password FROM students WHERE id = :student_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $student = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!password_verify($current_password, $student['password'])) {
        echo "Incorrect current password!<a href='settings_student.php'>Try Again</a> ";
        
        exit();
       
    }

    // Hash new password and update in database
    $hashed_password = password_hash($new_password, PASSWORD_BCRYPT);
    $query = "UPDATE students SET password = :password WHERE id = :student_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':password', $hashed_password);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Password changed successfully! <a href='student_dashboard.php'>Go Back</a>";
    } else {
        echo "Error updating password.<a href='settings_student.php'>Try Again</a> ";
    }
}
?>
