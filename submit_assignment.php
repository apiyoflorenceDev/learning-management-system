<?php
// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database and session initialization
require_once('student_dbcon.php');
session_start();

// Authentication check
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

$student_id = $_SESSION['student_id'];

// Function to get student assignments
function getStudentAssignments($conn, $student_id) {
    $query = "SELECT a.id, a.title, a.description, a.due_date, a.file_path, c.course_name
              FROM assignment a
              JOIN courses c ON a.course_id = c.id 
              JOIN enrollments e ON e.course_id = c.id 
              WHERE e.student_id = :student_id
              ORDER BY a.due_date ASC";
    
    try {
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (PDOException $e) {
        error_log("Database error: " . $e->getMessage());
        return [];
    }
}

// Function to handle assignment submission
function handleAssignmentSubmission($conn, $student_id, $assignment_id, $file) {
    // Validate assignment belongs to student
    $valid_assignments = getStudentAssignments($conn, $student_id);
    $is_valid = false;
    
    foreach ($valid_assignments as $assignment) {
        if ($assignment['id'] == $assignment_id) {
            $is_valid = true;
            break;
        }
    }
    
    if (!$is_valid) {
        return "Invalid assignment!";
    }

    // File upload validation
    $allowed_types = ['application/pdf', 'application/msword', 
                     'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                     'text/plain', 'application/zip'];
    $max_size = 5 * 1024 * 1024; // 5MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return "File upload error: " . $file['error'];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return "Only PDF, DOC, DOCX, TXT, or ZIP files are allowed.";
    }
    
    if ($file['size'] > $max_size) {
        return "File size exceeds 5MB limit.";
    }

    // Prepare upload directory
    $target_dir = "uploads/submissions/";
    if (!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    // Generate safe filename
    $file_ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $new_filename = sprintf("%d_%d_%s.%s", 
        $student_id, 
        $assignment_id, 
        bin2hex(random_bytes(4)), 
        $file_ext);
    $file_path = $target_dir . $new_filename;

    // Process submission
    try {
        $conn->beginTransaction();
        
        // Move uploaded file
        if (!move_uploaded_file($file['tmp_name'], $file_path)) {
            throw new Exception("Failed to move uploaded file.");
        }

        // Check for existing submission
        $existing = $conn->prepare("SELECT id FROM submissionss 
                                  WHERE student_id = ? AND assignment_id = ?");
        $existing->execute([$student_id, $assignment_id]);
        $submission_id = $existing->fetchColumn();

        if ($submission_id) {
            // Update existing submission
            $update = $conn->prepare("UPDATE submissionss 
                                    SET file_path = ?, submission_date = NOW() 
                                    WHERE id = ?");
            $update->execute([$file_path, $submission_id]);
            $message = "Assignment updated successfully!";
        } else {
            // Create new submission
            $insert = $conn->prepare("INSERT INTO submissionss 
                                    (student_id, assignment_id, file_path, submission_date) 
                                    VALUES (?, ?, ?, NOW())");
            $insert->execute([$student_id, $assignment_id, $file_path]);
            $message = "Assignment submitted successfully!";
        }
        
        $conn->commit();
        return $message;
    } catch (Exception $e) {
        $conn->rollBack();
        if (file_exists($file_path)) {
            unlink($file_path);
        }
        error_log("Submission error: " . $e->getMessage());
        return "An error occurred during submission. Please try again.";
    }
}

// Main logic flow
$assignments = getStudentAssignments($conn, $student_id);
$message = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['assignment_id'], $_FILES['submission_file'])) {
    $message = handleAssignmentSubmission(
        $conn,
        $student_id,
        $_POST['assignment_id'],
        $_FILES['submission_file']
    );
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Assignments</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        table { width: 100%; border-collapse: collapse; margin-top: 20px; }
        th, td { border: 1px solid #ddd; padding: 8px; text-align: left; }
        th { background-color: #f2f2f2; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .success { background-color: #dff0d8; color: #3c763d; }
        .error { background-color: #f2dede; color: #a94442; }
    </style>
</head>
<body>
    <h2>My Assignments</h2>
    
    <?php if ($message): ?>
        <div class="alert <?php echo strpos($message, 'error') !== false ? 'error' : 'success'; ?>">
            <?php echo htmlspecialchars($message); ?>
        </div>
    <?php endif; ?>

    <?php if (empty($assignments)): ?>
        <p>No assignments found for your courses.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Course</th>
                    <th>Title</th>
                    <th>Description</th>
                    <th>Due Date</th>
                    <th>Assignment File</th>
                    <th>Submit Solution</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($assignments as $assignment): 
                    // Check submission status
                    $status = $conn->prepare("SELECT submission_date FROM submissionss 
                                             WHERE student_id = ? AND assignment_id = ?");
                    $status->execute([$student_id, $assignment['id']]);
                    $submission = $status->fetch(PDO::FETCH_ASSOC);
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($assignment['course_name']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['title']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['description']); ?></td>
                        <td><?php echo htmlspecialchars($assignment['due_date']); ?></td>
                        <td>
                            <?php if ($assignment['file_path']): ?>
                                <a href="<?php echo htmlspecialchars($assignment['file_path']); ?>" download>
                                    Download Assignment
                                </a>
                            <?php else: ?>
                                No file attached
                            <?php endif; ?>
                        </td>
                        <td>
                            <form method="POST" action="submit_assignment.php" enctype="multipart/form-data">
                                <input type="hidden" name="assignment_id" value="<?php echo $assignment['id']; ?>">
                                <input type="file" name="submission_file" required>
                                <button type="submit">Upload Solution</button>
                            </form>
                        </td>
                        <td>
                            <?php if ($submission): ?>
                                Submitted on <?php echo htmlspecialchars($submission['submission_date']); ?>
                            <?php else: ?>
                                Not submitted
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</body>
</html>