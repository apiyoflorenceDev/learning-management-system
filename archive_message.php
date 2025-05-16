<?php
session_start();
include 'student_dbcon.php'; // Include the database connection file

// Check if the message ID is provided
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // Get the student id from the session
    $user_id = $_SESSION['student_id'];

    // Prepare the query to update the message status to archived
    $sql = "UPDATE messages SET is_archived = 1 WHERE id = :message_id AND recipient_id = :user_id";
    $stmt = $conn->prepare($sql);

    // Execute the query
    $stmt->execute([
        'message_id' => $message_id,
        'user_id' => $user_id
    ]);

    // Redirect to the message list page after archiving
    header('Location: messages.php?filter=all');
    exit;
} else {
    // No message ID provided
    echo "No message ID specified.";
    exit;
}
