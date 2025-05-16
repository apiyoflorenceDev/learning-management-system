<?php
include('student_dbcon.php');
session_start();

// Check if the instructor is logged in
if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in to add topics.";
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch courses assigned to the instructor
$query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $topic_name = $_POST['topic_name'];
    $topic_description = $_POST['topic_description'];

    // Handling the uploaded files
    $upload_dir = 'uploads/learning_materials/';
    $uploaded_files = [];

    // Create the 'learning_materials' folder if it does not exist
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true); // Create the directory with read/write permissions
    }

    if (isset($_FILES['learning_materials']) && $_FILES['learning_materials']['error'][0] != UPLOAD_ERR_NO_FILE) {
        foreach ($_FILES['learning_materials']['tmp_name'] as $key => $tmp_name) {
            $file_name = $_FILES['learning_materials']['name'][$key];
            $file_tmp = $_FILES['learning_materials']['tmp_name'][$key];
            $file_error = $_FILES['learning_materials']['error'][$key];

            // Validate file (check if no errors, file type, and size)
            if ($file_error === UPLOAD_ERR_OK) {
                $file_path = $upload_dir . basename($file_name);
                if (move_uploaded_file($file_tmp, $file_path)) {
                    $uploaded_files[] = $file_path; // Store file path for later insertion into database
                } else {
                    echo "Error uploading file: $file_name. Please try again.";
                    exit();
                }
            } else {
                echo "Error with file upload.";
                exit();
            }
        }
    }

    // Insert topic and learning materials into the database
    if (!empty($course_id) && !empty($topic_name)) {
        $query = "INSERT INTO topics (course_id, topic_name, topic_description) VALUES (:course_id, :topic_name, :topic_description)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':topic_name', $topic_name);
        $stmt->bindParam(':topic_description', $topic_description);
        
        if ($stmt->execute()) {
            $topic_id = $conn->lastInsertId();  // Get the ID of the newly created topic

            // Insert file paths for learning materials
            foreach ($uploaded_files as $file_path) {
                $file_query = "INSERT INTO learning_materials (topic_id, file_path) VALUES (:topic_id, :file_path)";
                $file_stmt = $conn->prepare($file_query);
                $file_stmt->bindParam(':topic_id', $topic_id);
                $file_stmt->bindParam(':file_path', $file_path);
                $file_stmt->execute();
            }

            echo "Topic added successfully along with learning materials! <a href='instructor_dashboard.php'>Go Back</a>";
        } else {
            echo "Error adding topic. <a href='add_topic.php'>Try Again</a>";
        }
    } else {
        echo "Please fill in all required fields. <a href='add_topic.php'>Try Again</a>";
    }
}
?>
