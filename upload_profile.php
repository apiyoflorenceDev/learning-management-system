<?php
session_start();
require 'student_dbcon.php'; // Database connection file

// Ensure the user is logged in
if (!isset($_SESSION['student_id'])) {
    echo json_encode(["error" => "User not authenticated!"]);
    exit();
}

$student_id = $_SESSION['student_id'];

// Check if file is uploaded
if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
    
    $file_name = $_FILES['profile_pic']['name'];
    $file_tmp = $_FILES['profile_pic']['tmp_name'];
    $file_size = $_FILES['profile_pic']['size'];
    $file_type = mime_content_type($file_tmp);
    
    // Allowed file types
    $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];

    if (!in_array($file_type, $allowed_types)) {
        echo json_encode(["error" => "Only JPG, PNG, and GIF files are allowed."]);
        exit();
    }

    // Ensure uploads directory exists
    $upload_dir = 'uploads/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Create a unique file name
    $new_file_name = "profile_" . $student_id . "_" . time() . "." . pathinfo($file_name, PATHINFO_EXTENSION);
    $upload_path = $upload_dir . $new_file_name;

    // Move the uploaded file
    if (move_uploaded_file($file_tmp, $upload_path)) {
        try {
            // Update the profile picture in the database
            $sql = "UPDATE students SET profile_pic = :profile_pic WHERE id = :student_id";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':profile_pic', $upload_path, PDO::PARAM_STR);
            $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(["success" => "Profile picture updated successfully!", "profile_pic" => $upload_path]);
            } else {
                echo json_encode(["error" => "Database update failed!"]);
            }
        } catch (PDOException $e) {
            echo json_encode(["error" => "Database error: " . $e->getMessage()]);
        }
    } else {
        echo json_encode(["error" => "File upload failed!"]);
    }
} else {
    echo json_encode(["error" => "No file uploaded!"]);
}
?>
