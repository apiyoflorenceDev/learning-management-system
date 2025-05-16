<?php
include('student_dbcon.php');
session_start();

// Ensure the student is logged in
if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to access assignments. <a href='student_login.php'>Login</a>";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch assignments for courses the student is enrolled in
$query = "SELECT assignments.id, assignments.title, assignments.description, assignments.due_date, assignments.file_path, courses.course_name 
          FROM assignments 
          JOIN courses ON assignments.course_id = courses.id 
          JOIN enrollments ON courses.id = enrollments.course_id 
          WHERE enrollments.student_id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$assignments = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Assignments</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <h2>Assignments</h2>
    
    <table>
        <tr>
            <th>Course</th>
            <th>Title</th>
            <th>Description</th>
            <th>Due Date</th>
            <th>Download</th>
            <th>Submit</th>
        </tr>
        <?php foreach ($assignments as $assignment): ?>
            <tr>
                <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                <td>
                    <?php if ($assignment['file_path']): ?>
                        <a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download>Download</a>
                    <?php else: ?>
                        No file
                    <?php endif; ?>
                </td>
                <td>
                    <form action="submit_assignment.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                        <input type="file" name="assignment_file" required>
                        <button type="submit">Submit</button>
                    </form>
                </td>
            </tr>
        <?php endforeach; ?>
    </table>
</body>
</html>
