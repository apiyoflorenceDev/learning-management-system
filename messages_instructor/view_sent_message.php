<?php
session_start();
include 'instructor_dbcon.php'; // Include the database connection

// Check if message ID is provided
if (isset($_GET['id'])) {
    $message_id = $_GET['id'];
    $instructor_id = $_SESSION['instructor_id'];

    // Fetch message details
    $sql = "SELECT * FROM messages WHERE id = :message_id AND sender_id = :instructor_id";
    $stmt = $conn->prepare($sql);
    $stmt->execute([
        'message_id' => $message_id,
        'instructor_id' => $instructor_id
    ]);
    $message = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$message) {
        echo "Message not found.";
        exit;
    }
} else {
    echo "No message ID specified.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>View Sent Message</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>View Message</h2>

<p><strong>Subject:</strong> <?= htmlspecialchars($message['subject']) ?></p>
<p><strong>Message:</strong> <?= nl2br(htmlspecialchars($message['message_body'])) ?></p>
<p><strong>Sent To:</strong> 
    <?php
    // Get recipient name
    $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
    $stmt->execute(['id' => $message['recipient_id']]);
    $student = $stmt->fetch();
    echo htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']);
    ?>
</p>

<p><strong>Date Sent:</strong> <?= $message['created_at'] ?></p>

<a href="inbox.php">Back to Sent Messages</a>

</body>
</html>
