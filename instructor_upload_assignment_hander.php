<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in to create assignments.";
    exit();
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $course_id = $_POST['course_id'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $due_date = $_POST['due_date'];
    $instructor_id = $_SESSION['instructor_id'];

    // File handling
    $upload_dir = "uploads/assignments/";
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    $file_name = $_FILES['file']['name'];
    $file_tmp = $_FILES['file']['tmp_name'];
    $file_ext = pathinfo($file_name, PATHINFO_EXTENSION);
    $allowed_ext = ["pdf", "docx", "txt"];

    if (!in_array($file_ext, $allowed_ext)) {
        echo "Invalid file type! Only PDF, DOCX, and TXT files are allowed.";
        exit();
    }

    $new_file_name = time() . "_" . $file_name;
    $file_path = $upload_dir . $new_file_name;

    if (move_uploaded_file($file_tmp, $file_path)) {
        $query = "INSERT INTO assignments (course_id, title, description, due_date, file_path, instructor_id)
                  VALUES (:course_id, :title, :description, :due_date, :file_path, :instructor_id)";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':title', $title);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':due_date', $due_date);
        $stmt->bindParam(':file_path', $file_path);
        $stmt->bindParam(':instructor_id', $instructor_id);

        if ($stmt->execute()) {
            echo "Assignment created successfully! <a href='instructor_dashboard.php'>Go Back</a>";
        } else {
            echo "Error creating assignment.";
        }
    } else {
        echo "Failed to upload file.";
    }
}
?>
