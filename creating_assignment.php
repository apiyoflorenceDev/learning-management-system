<?php
// Enable error reporting
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('student_dbcon.php');
session_start();

// Check instructor login
if (!isset($_SESSION['instructor_id'])) {
    header("Location: login.php");
    exit();
}

$instructor_id = $_SESSION['instructor_id'];
$message = '';

// Set upload directory path
$upload_dir = 'uploads/assignments/';

// Create upload directory if it doesn't exist
if (!is_dir($upload_dir)) {
    if (!mkdir($upload_dir, 0755, true)) { // More secure permission (0755)
        die("Failed to create upload directory.");
    }
}

// Verify directory is writable
if (!is_writable($upload_dir)) {
    die("Upload directory is not writable.");
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Validate inputs
    $course_id = filter_input(INPUT_POST, 'course_id', FILTER_VALIDATE_INT);
    $topic_id = filter_input(INPUT_POST, 'topic_id', FILTER_VALIDATE_INT);
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $due_date = $_POST['due_date'];
    
    if (!$course_id || !$topic_id || empty($title) || empty($description) || empty($due_date)) {
        $message = "Please fill all required fields properly.";
    } else {
        // Initialize file_path as null (for database)
        $file_path = null;
        
        // Process file upload if present
        if (!empty($_FILES['assignment_file']['name'])) {
            $file = $_FILES['assignment_file'];
            
            // File validation
            $allowed_types = [
                'pdf' => 'application/pdf',
                'doc' => 'application/msword',
                'docx' => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
            ];
            $max_size = 10 * 1024 * 1024; // 10MB
            
            $file_ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
            
            // Validate file
            if ($file['error'] !== UPLOAD_ERR_OK) {
                $message = "File upload error occurred.";
            } elseif ($file['size'] > $max_size) {
                $message = "File size exceeds 10MB limit.";
            } elseif (!array_key_exists($file_ext, $allowed_types) || $file['type'] !== $allowed_types[$file_ext]) {
                $message = "Only PDF, DOC, and DOCX files are allowed.";
            } else {
                // Generate unique filename
                $new_filename = uniqid('assign_', true) . '.' . $file_ext;
                $destination = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file['tmp_name'], $destination)) {
                    $file_path = $upload_dir . $new_filename;
                } else {
                    $message = "Error saving uploaded file.";
                }
            }
        }

        // Insert into database if no errors
        if (empty($message)) {
            try {
                $conn->beginTransaction();
                
                $query = "INSERT INTO assignment 
                         (course_id, topic_id, instructor_id, title, description, due_date, file_path) 
                         VALUES (:course_id, :topic_id, :instructor_id, :title, :description, :due_date, :file_path)";
                
                $stmt = $conn->prepare($query);
                $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
                $stmt->bindParam(':topic_id', $topic_id, PDO::PARAM_INT);
                $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
                $stmt->bindParam(':title', $title);
                $stmt->bindParam(':description', $description);
                $stmt->bindParam(':due_date', $due_date);
                $stmt->bindParam(':file_path', $file_path);
                
                if ($stmt->execute()) {
                    $conn->commit();
                    $message = "Assignment created successfully!";
                    // Clear form
                    $_POST = [];
                } else {
                    throw new Exception("Database execute failed");
                }
                
            } catch (PDOException $e) {
                $conn->rollBack();
                // Clean up file if database failed
                if (isset($destination) && file_exists($destination)) {
                    unlink($destination);
                }
                $message = "Database error: " . $e->getMessage();
            }
        }
    }
}

// Get courses for dropdown
$courses = [];
try {
    $query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
    $stmt->execute();
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading courses: " . $e->getMessage();
}

// Get topics for dropdown
$topics = [];
try {
    $query = "SELECT id, topic_name FROM topics";
    $stmt = $conn->prepare($query);
    $stmt->execute();
    $topics = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    $message = "Error loading topics: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Assignment</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .container { max-width: 800px; margin: 0 auto; }
        .alert { padding: 10px; margin: 10px 0; border-radius: 4px; }
        .alert-success { background-color: #dff0d8; color: #3c763d; }
        .alert-error { background-color: #f2dede; color: #a94442; }
        .form-group { margin-bottom: 15px; }
        label { display: block; margin-bottom: 5px; font-weight: bold; }
        input[type="text"], input[type="date"], select, textarea {
            width: 100%; padding: 8px; border: 1px solid #ddd; border-radius: 4px;
        }
        textarea { height: 100px; }
        input[type="submit"] {
            background-color: #4CAF50; color: white; padding: 10px 15px;
            border: none; border-radius: 4px; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Create New Assignment</h1>
        
        <?php if (!empty($message)): ?>
            <div class="alert <?php echo strpos($message, 'success') !== false ? 'alert-success' : 'alert-error'; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <form method="POST"  action="creating_assignment.php" enctype="multipart/form-data">
            <div class="form-group">
                <label for="course_id">Course:</label>
                <select name="course_id" id="course_id" required>
                    <option value="">Select Course</option>
                    <?php foreach ($courses as $course): ?>
                        <option value="<?php echo $course['id']; ?>"
                            <?php echo isset($_POST['course_id']) && $_POST['course_id'] == $course['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($course['course_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="topic_id">Topic:</label>
                <select name="topic_id" id="topic_id" required>
                    <option value="">Select Topic</option>
                    <?php foreach ($topics as $topic): ?>
                        <option value="<?php echo $topic['id']; ?>"
                            <?php echo isset($_POST['topic_id']) && $_POST['topic_id'] == $topic['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($topic['topic_name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label for="title">Title:</label>
                <input type="text" name="title" id="title" required
                    value="<?php echo isset($_POST['title']) ? htmlspecialchars($_POST['title']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description" id="description" required><?php 
                    echo isset($_POST['description']) ? htmlspecialchars($_POST['description']) : ''; 
                ?></textarea>
            </div>

            <div class="form-group">
                <label for="due_date">Due Date:</label>
                <input type="date" name="due_date" id="due_date" required
                    value="<?php echo isset($_POST['due_date']) ? htmlspecialchars($_POST['due_date']) : ''; ?>">
            </div>

            <div class="form-group">
                <label for="assignment_file">Upload File (optional):</label>
                <input type="file" name="assignment_file" id="assignment_file">
                <small>Accepted formats: PDF, DOC, DOCX (Max 10MB)</small>
            </div>

            <div class="form-group">
                <input type="submit" value="Create Assignment">
            </div>
        </form>
    </div>
</body>
</html>