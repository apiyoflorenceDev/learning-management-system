<?php
session_start();
include 'student_dbcon.php'; // Database connection

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['profile_pic'])) {
    $userId = $_SESSION['user_id'];
    $profilePic = $_FILES['profile_pic'];

    // Ensure the file is an image
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    if (in_array($profilePic['type'], $allowedTypes)) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($profilePic['name']);

        // Move the uploaded file
        if (move_uploaded_file($profilePic['tmp_name'], $uploadFile)) {
            // Update the profile picture in the database
            $stmt = $pdo->prepare("UPDATE users SET profile_pic = :profile_pic WHERE id = :userId");
            $stmt->execute(['profile_pic' => $profilePic['name'], 'userId' => $userId]);

            header("Location: dashboard.php");
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Only image files are allowed.";
    }
}
?>
