<?php
include('student_dbcon.php');
session_start();

// Ensure instructor is logged in
$instructor_id = $_SESSION['instructor_id'];
if (!$instructor_id) {
    echo "You must be logged in to update your profile.";
    exit();
}

// Fetch current instructor details
$query = "SELECT * FROM instructors WHERE id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle profile update (name & email)
if (isset($_POST['update_profile'])) {
    $instructor_name = $_POST['instructor_name'];
    $instructor_email = $_POST['instructor_email'];

    // Update instructor name and email in the database
    $update_query = "UPDATE instructors SET name = :name, email = :email WHERE id = :instructor_id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':name', $instructor_name);
    $stmt->bindParam(':email', $instructor_email);
    $stmt->bindParam(':instructor_id', $instructor_id);

    if ($stmt->execute()) {
        echo "Profile updated successfully!";
    } else {
        echo "Error updating profile.";
    }
}

// Handle password change
if (isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_new_password = $_POST['confirm_new_password'];

    // Check if current password is correct
    if (!password_verify($current_password, $instructor['password'])) {
        echo "The current password is incorrect.";
        exit();
    }

    // Check if new password matches the confirmation
    if ($new_password && $new_password === $confirm_new_password) {
        $new_password_hashed = password_hash($new_password, PASSWORD_DEFAULT);

        // Update instructor password in the database
        $update_query = "UPDATE instructors SET password = :password WHERE id = :instructor_id";
        $stmt = $conn->prepare($update_query);
        $stmt->bindParam(':password', $new_password_hashed);
        $stmt->bindParam(':instructor_id', $instructor_id);

        if ($stmt->execute()) {
            echo "Password changed successfully!";
        } else {
            echo "Error changing password.";
        }
    } else {
        echo "The new password and confirmation do not match.";
        exit();
    }
}
?>
