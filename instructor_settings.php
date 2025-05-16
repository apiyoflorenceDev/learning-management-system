<?php
include('student_dbcon.php'); // Database connection
session_start();

// Get instructor ID
$instructor_id = $_SESSION['instructor_id'] ?? null;

if (!$instructor_id) {
    echo "<p>You need to be logged in to manage courses.</p>";
    exit();
}

// Fetch courses assigned to the instructor
$query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<style>
    .settings-container {
        background-color: #f9f9f9;
        padding: 20px;
        border-radius: 10px;
        box-shadow: 0 0 5px rgba(0,0,0,0.1);
        max-width: 700px;
    }

    .settings-container h2, h3 {
        color: #2c3e50;
    }

    .settings-container form {
        display: flex;
        flex-direction: column;
    }

    .settings-container label {
        margin-top: 15px;
        font-weight: bold;
    }

    .settings-container select,
    .settings-container input,
    .settings-container textarea {
        padding: 10px;
        margin-top: 5px;
        border: 1px solid #ccc;
        border-radius: 5px;
    }

    .settings-container button {
        margin-top: 20px;
        padding: 10px 20px;
        background-color: #1abc9c;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        font-weight: bold;
    }

    .settings-container button:hover {
        background-color: #16a085;
    }

    .message {
        padding: 10px;
        background-color: #ffe0e0;
        color: #c0392b;
        border-radius: 5px;
        margin-top: 10px;
    }
</style>

<div class="settings-container">
    <h2>Update Course Content</h2>

    <form method="GET" action="instructor_settings.php">
        <label for="course_id">Select Course:</label>
        <select name="course_id" id="course_id" required>
            <option value="">-- Select a Course --</option>
            <?php foreach ($courses as $course): ?>
                <option value="<?php echo $course['id']; ?>" <?php echo (isset($_GET['course_id']) && $_GET['course_id'] == $course['id']) ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($course['course_name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
        <button type="submit">Edit Course</button>
    </form>

    <?php
    if (isset($_GET['course_id'])) {
        $course_id = $_GET['course_id'];

        $query = "SELECT * FROM courses WHERE id = :course_id AND instructor_id = :instructor_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':course_id', $course_id);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();
        $course = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($course): ?>
            <form method="POST" action="instructor_settings_handler.php">
                <h3>Edit Course Details</h3>

                <label for="course_name">Course Name</label>
                <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>

                <label for="course_code">Course Code</label>
                <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>

                <label for="description">Course Description</label>
                <textarea name="description" rows="4" required><?php echo htmlspecialchars($course['description']); ?></textarea>

                <input type="hidden" name="course_id" value="<?php echo $course['id']; ?>" />

                <button type="submit">Update Course</button>
            </form>
        <?php else: ?>
            <div class="message">Course not found or you're not authorized to edit this course.</div>
        <?php endif;
    }
    ?>
</div>
