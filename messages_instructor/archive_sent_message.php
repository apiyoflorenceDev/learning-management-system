<?php
session_start();
include '../student_dbcon.php'; // Include the database connection

// Check if message ID is provided
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    $instructor_id = $_SESSION['instructor_id'];

    // Archive the message by updating the 'is_archived' flag
    $sql = "UPDATE messages SET is_archived = 1 WHERE id = :message_id AND sender_id = :instructor_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'message_id' => $message_id,
        'instructor_id' => $instructor_id
    ]);

    // Redirect to inbox
    header("Location: inbox.php");
    exit;
}
