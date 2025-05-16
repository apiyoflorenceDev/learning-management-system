<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in to edit grades.";
    exit();
}

if (isset($_GET['id']) && isset($_GET['course_id'])) {
    $student_id = $_GET['id'];
    $course_id = $_GET['course_id'];

    // Fetch current grade
    $query = "SELECT grade FROM grades WHERE student_id = :student_id AND course_id = :course_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->execute();
    $grade = $stmt->fetch(PDO::FETCH_ASSOC);

    // Handle grade update
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        $new_grade = $_POST['grade'];
        $update_query = "UPDATE grades SET grade = :grade WHERE student_id = :student_id AND course_id = :course_id";
        $update_stmt = $conn->prepare($update_query);
        $update_stmt->bindParam(':grade', $new_grade);
        $update_stmt->bindParam(':student_id', $student_id);
        $update_stmt->bindParam(':course_id', $course_id);
        
        if ($update_stmt->execute()) {
            echo "Grade updated successfully!";
        } else {
            echo "Error updating grade.";
        }
    }
}
?>

<form method="POST">
    <label for="grade">Grade:</label>
    <select name="grade" required>
        <option value="A" <?php if ($grade['grade'] == 'A') echo 'selected'; ?>>A</option>
        <option value="B" <?php if ($grade['grade'] == 'B') echo 'selected'; ?>>B</option>
        <option value="C" <?php if ($grade['grade'] == 'C') echo 'selected'; ?>>C</option>
        <option value="D" <?php if ($grade['grade'] == 'D') echo 'selected'; ?>>D</option>
        <option value="F" <?php if ($grade['grade'] == 'F') echo 'selected'; ?>>F</option>
    </select>
    <button type="submit">Update Grade</button>
</form>
