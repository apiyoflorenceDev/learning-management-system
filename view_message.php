<?php
session_start();
include 'student_dbcon.php'; // Include the database connection file

// Check if the message ID is provided
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];

    // Get the student id from the session
    $user_id = $_SESSION['student_id'];

    // Prepare the query to fetch the message from the database, including sender's name
    $sql = "SELECT m.*, i.name AS sender_name 
            FROM messages m 
            JOIN instructors i ON m.sender_id = i.id
            WHERE m.id = :message_id AND m.recipient_id = :user_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'message_id' => $message_id,
        'user_id' => $user_id
    ]);

    // Fetch the message details
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        // Message not found or doesn't belong to the current user
        echo "Message not found.";
        exit;
    }

    // Mark the message as read (if it is unread)
    if ($message['is_read'] == 0) {
        $updateSql = "UPDATE messages SET is_read = 1 WHERE id = :message_id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->execute(['message_id' => $message_id]);
    }
} else {
    // No message ID provided
    echo "No message ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Message</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Styling for the message page */
        .message-container {
            width: 60%;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #ddd;
            border-radius: 10px;
            background-color: #f9f9f9;
        }
        .message-container h2 {
            text-align: center;
        }
        .message-container .message-info {
            margin-bottom: 20px;
        }
        .message-container .message-info span {
            font-weight: bold;
        }
        .message-container .message-body {
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
            border-radius: 5px;
        }
        .message-actions {
            margin-top: 20px;
            text-align: center;
        }
        .message-actions a {
            margin: 0 10px;
            padding: 10px;
            text-decoration: none;
            border-radius: 5px;
        }
        .archive-btn {
            background-color: #4CAF50;
            color: white;
        }
        .delete-btn {
            background-color: #f44336;
            color: white;
        }
    </style>
</head>
<body>

<div class="message-container">
    <h2>Message Details</h2>

    <!-- Message Info -->
    <div class="message-info">
        <p><span>From:</span> <?= htmlspecialchars($message['sender_name']) ?></p>
        <p><span>Subject:</span> <?= htmlspecialchars($message['subject']) ?></p>
        <p><span>Date:</span> <?= $message['created_at'] ?></p>
        <p><span>Status:</span> <?= $message['is_read'] ? 'Read' : 'Unread' ?></p>
    </div>

    <!-- Message Body -->
    <div class="message-body">
        <p><?= nl2br(htmlspecialchars($message['message_body'])) ?></p>
    </div>

    <!-- Message Actions -->
    <div class="message-actions">
        <a href="archive_message.php?id=<?= $message['id'] ?>" class="archive-btn">Archive</a>
        <a href="delete_message.php?id=<?= $message['id'] ?>" class="delete-btn" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
    </div>
</div>

</body>
</html>
