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

// Fetch topic details
$query = "SELECT * FROM topics WHERE id = :topic_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':topic_id', $topic_id);
$stmt->execute();
$topic = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$topic) {
    echo "Topic not found.";
    exit();
}

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $topic_name = $_POST['topic_name'];
    $topic_description = $_POST['topic_description'];

    $query = "UPDATE topics SET topic_name = :topic_name, topic_description = :topic_description WHERE id = :topic_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':topic_name', $topic_name);
    $stmt->bindParam(':topic_description', $topic_description);
    $stmt->bindParam(':topic_id', $topic_id);

    if ($stmt->execute()) {
        echo "Topic updated successfully! <a href='manage_topics.php?course_id=" . $topic['course_id'] . "'>Go Back</a>";
    } else {
        echo "Error updating topic.";
    }
}
?>
<link rel="stylesheet" href="add_topics.css">


<form method="POST">
    <label>Topic Name:</label>
    <input type="text" name="topic_name" value="<?php echo htmlspecialchars($topic['topic_name']); ?>" required>

    <label>Topic Description:</label>
    <textarea name="topic_description"><?php echo htmlspecialchars($topic['topic_description']); ?></textarea>

    <button type="submit">Update Topic</button>
</form>
