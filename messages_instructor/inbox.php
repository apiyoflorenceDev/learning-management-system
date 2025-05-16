<?php
session_start();
include '../student_dbcon.php'; // Include the database connection

// Check if the instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    echo "Please log in first!";
    exit;
}

$instructor_id = $_SESSION['instructor_id'];

// Set default filter to 'all'
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'all';
$search = isset($_GET['search']) ? $_GET['search'] : '';

// Build SQL query based on filter and search
$sql = "SELECT m.*, i.name AS sender_name
        FROM messages m
        JOIN instructors i ON m.sender_id = i.id
        WHERE m.recipient_id = :instructor_id";  // Removed 'is_deleted' condition

if ($filter == 'archived') {
    $sql .= " AND m.is_archived = 1";
}

if ($search) {
    $sql .= " AND (m.subject LIKE :search OR m.recipient_id IN (SELECT id FROM students WHERE first_name LIKE :search OR last_name LIKE :search))";
}

$sql .= " ORDER BY m.created_at DESC";

// Prepare the query
$stmt = $conn->prepare($sql);

// Define the parameters array
$params = ['instructor_id' => $instructor_id];

if ($search) {
    $params['search'] = '%' . $search . '%';
}

// Execute the query with the appropriate parameters
$stmt->execute($params);

// Fetch messages
$messages = $stmt->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sent Messages</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<h2>Sent Messages</h2>

<!-- Search and Filter Options -->
<form method="GET" action="inbox.php">
    <select name="filter">
        <option value="all" <?= $filter == 'all' ? 'selected' : '' ?>>All Messages</option>
        <option value="archived" <?= $filter == 'archived' ? 'selected' : '' ?>>Archived</option>
    </select>

    <input type="text" name="search" placeholder="Search by subject or student" value="<?= htmlspecialchars($search) ?>">
    <button type="submit">Search</button>
</form>

<!-- Display the sent messages -->
<table>
    <thead>
        <tr>
            <th>Subject</th>
            <th>Recipient</th>
            <th>Date</th>
            <th>Actions</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($messages as $message): ?>
            <tr>
                <td><?= htmlspecialchars($message['subject']) ?></td>
                <td>
                    <?php
                    // Get recipient name
                    $stmt = $conn->prepare("SELECT * FROM students WHERE id = :id");
                    $stmt->execute(['id' => $message['recipient_id']]);
                    $student = $stmt->fetch();
                    echo htmlspecialchars($student['first_name']) . ' ' . htmlspecialchars($student['last_name']);
                    ?>
                </td>
                <td><?= htmlspecialchars($message['created_at']) ?></td>
                <td>
                    <a href="view_sent_message.php?id=<?= $message['id'] ?>">View</a> |
                    <a href="archive_sent_message.php?id=<?= $message['id'] ?>">Archive</a> |
                    <a href="delete_sent_message.php?id=<?= $message['id'] ?>" onclick="return confirm('Are you sure you want to delete this message?')">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>
