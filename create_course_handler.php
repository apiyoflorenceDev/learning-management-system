<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to create a course.";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $description = $_POST['description'];
    $course_image = $_FILES['course_image'];

    // Step 1: Check if the course code already exists
    $checkQuery = "SELECT * FROM courses WHERE course_code = :course_code";
    $stmt = $conn->prepare($checkQuery);
    $stmt->bindParam(':course_code', $course_code);
    $stmt->execute();

    if ($stmt->rowCount() > 0) {
        // If course code exists, shows error
        echo "The course code '$course_code' is already in use. Please choose a different one.<br><br><a href='create_course.php'>Enter Another One</a>";
    } else {
        // Step 2: Process the image upload if any
        $image_path = null;
        if ($course_image['error'] == 0) {
            // Check for image file size and type (optional)
            $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
            if (in_array($course_image['type'], $allowed_types)) {
                // Set image path
                $upload_dir = 'uploads/images/';
                $image_path = $upload_dir . basename($course_image['name']);
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0777, true); // Create the directory with read/write permissions
                }

                // Move uploaded image to the specified directory
                if (move_uploaded_file($course_image['tmp_name'], $image_path)) {
                    echo "Image uploaded successfully.<br>";
                } else {
                    echo "Error uploading image.<br>";
                }
            } else {
                echo "Only JPEG, PNG, and GIF images are allowed.<br>";
            }
        }

        // Step 3: Insert the new course if the course code is unique
        $insertQuery = "INSERT INTO courses (course_name, course_code, description, instructor_id, course_image)
                        VALUES (:course_name, :course_code, :description, :instructor_id, :course_image)";
        
        $stmt = $conn->prepare($insertQuery);
        $stmt->bindParam(':course_name', $course_name);
        $stmt->bindParam(':course_code', $course_code);
        $stmt->bindParam(':description', $description);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->bindParam(':course_image', $image_path);

        if ($stmt->execute()) {
            echo "Course created successfully! <a href='instructor_dashboard.php'>Go Back</a>";
        } else {
            echo "Error creating course. <a href='create_course.php'>Go Back</a>";
        }
    }
}
?>
