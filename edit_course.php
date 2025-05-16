<?php
include('student_dbcon.php');
session_start();

$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to edit a course.";
    exit();
}

if (!isset($_GET['id'])) {
    echo "Course ID is missing.";
    exit();
}

$course_id = $_GET['id'];

// Fetch the course details
$query = "SELECT * FROM courses WHERE id = :course_id AND instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
$stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);
$stmt->execute();
$course = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    echo "Course not found.";
    exit();
}

// Handle the update process
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $course_name = $_POST['course_name'];
    $course_code = $_POST['course_code'];
    $description = $_POST['description'];

    $updateQuery = "UPDATE courses SET course_name = :course_name, course_code = :course_code, description = :description 
                    WHERE id = :course_id AND instructor_id = :instructor_id";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bindParam(':course_name', $course_name);
    $stmt->bindParam(':course_code', $course_code);
    $stmt->bindParam(':description', $description);
    $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
    $stmt->bindParam(':instructor_id', $instructor_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Course updated successfully! <a href='instructor_dashboard.php'>Go Back</a>";
    } else {
        echo "Error updating course.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Course</title>
    <link rel="stylesheet" href="courses.css"> <!-- Link to CSS file -->
</head>
<body>

    <h2>Edit Course</h2>

    <form method="POST">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required><br>

        <label for="course_code">Course Code</label>
        <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required><br>

        <label for="description">Description</label>
        <textarea name="description" required><?php echo htmlspecialchars($course['description']); ?></textarea><br>

        <input type="submit" value="Update Course">
    </form>
    
</body>
</html>
