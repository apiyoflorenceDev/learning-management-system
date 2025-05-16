<?php
session_start();
require_once '../student_dbcon.php'; // Include the database connection

// Check if instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    echo "Please log in first!";
    exit;
}

$instructor_id = $_SESSION['instructor_id'];

// Handle send message form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['send_message'])) {
    $subject = $_POST['subject'];
    $message_body = $_POST['message_body'];

    // Get all student IDs
    $sql = "SELECT id FROM students";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Insert the message into the database for all students (removed is_deleted column)
    $sql = "INSERT INTO messages (sender_id, recipient_id, subject, message_body, is_read, is_archived)
            VALUES (:sender_id, :recipient_id, :subject, :message_body, 0, 0)";
    $stmt = $conn->prepare($sql);

    foreach ($students as $student) {
        $stmt->execute([
            'sender_id' => $instructor_id,
            'recipient_id' => $student['id'],
            'subject' => $subject,
            'message_body' => $message_body
        ]);
    }

    $message_sent = "Message sent successfully to all students!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Send Message to All Students</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h1>Send New Message to All Students</h1>

<?php if (isset($message_sent)) { echo "<p>$message_sent</p>"; } ?>

<form action="send_message.php" method="POST">
    <label for="subject">Subject:</label>
    <input type="text" name="subject" required><br><br>

    <label for="message_body">Message:</label><br>
    <textarea name="message_body" rows="5" cols="50" required></textarea><br><br>

    <button type="submit" name="send_message">Send Message</button>
</form>

</body>
</html>
