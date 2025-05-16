<?php
session_start();
include 'student_dbcon.php'; // Include the database connection file

// Get the student id from the session
$user_id = $_SESSION['student_id'];

// Set the filter default to 'all'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build the SQL query based on the filter and search
$sql = "SELECT m.*, i.name AS sender_name FROM messages m
        JOIN instructors i ON m.sender_id = i.id
        WHERE m.recipient_id = :user_id";

if ($filter == 'unread') {
    $sql .= " AND m.is_read = 0 AND m.is_archived = 0";
} elseif ($filter == 'archived') {
    $sql .= " AND m.is_archived = 1";
} else {
    $sql .= " AND m.is_archived = 0";
}

if ($search) {
    $sql .= " AND (m.subject LIKE :search OR i.name LIKE :search OR m.message_body LIKE :search)";
}

$sql .= " ORDER BY m.created_at DESC";

// Prepare the query
$messagesStmt = $conn->prepare($sql);

// Bind parameters manually
$messagesStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
if ($search) {
    $searchParam = '%' . $search . '%';
    $messagesStmt->bindParam(':search', $searchParam, PDO::PARAM_STR);
}

$messagesStmt->execute();

$messages = $messagesStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!-- HTML for displaying messages -->
<div class="message-sidebar">
    <h3>Your Messages</h3>

    <!-- Filter and Search options -->
    <form method="GET" action="messages.php">
        <div class="filter-options">
            <select name="filter" onchange="this.form.submit()">
                <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Messages</option>
                <option value="unread" <?= $filter == 'unread' ? 'selected' : '' ?>>Unread</option>
                <option value="archived" <?= $filter == 'archived' ? 'selected' : '' ?>>Archived</option>
            </select>
            <input type="text" name="search" placeholder="Search messages..." value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>" />
            <button type="submit">Search</button>
        </div>
    </form>

    <!-- Message list -->
    <div id="message-list">
        <?php if ($messages): ?>
            <ul>
            <?php foreach ($messages as $message): ?>
                <li style="background-color: <?= $message['is_read'] ? 'white' : '#f5f5f5' ?>;">
                    <strong><?= htmlspecialchars($message['sender_name']) ?></strong> - 
                    <a href="view_message.php?id=<?= $message['id'] ?>"><?= htmlspecialchars($message['subject']) ?></a>
                    <small> <?= $message['created_at'] ?> </small>
                    <?php if (!$message['is_read']): ?>
                        <span>Unread</span>
                    <?php endif; ?>

                    <!-- Archive and Delete Actions -->
                    <a href="archive_message.php?id=<?= $message['id'] ?>" style="color: blue;">Archive</a> | 
                    <a href="delete_message.php?id=<?= $message['id'] ?>" style="color: red;" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                </li>
            <?php endforeach; ?>
            </ul>
        <?php else: ?>
            <p>No messages available based on the selected filter.</p>
        <?php endif; ?>
    </div>
</div>
