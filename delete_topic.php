<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in.";
    exit();
}

$topic_id = $_GET['topic_id'] ?? null;
if (!$topic_id) {
    echo "Invalid request.";
    exit();
}

// Delete topic
$query = "DELETE FROM topics WHERE id = :topic_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':topic_id', $topic_id);

if ($stmt->execute()) {
    echo "Topic deleted successfully! <a href='manage_topics.php'>Go Back</a>";
} else {
    echo "Error deleting topic.";
}
?>
