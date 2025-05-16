<form action="instructor_upload_assignment_hander.php" method="POST" enctype="multipart/form-data">
    <label for="course_id">Select Course:</label>
    <select name="course_id" required>
        <option value="">-- Select Course --</option>
        <?php
        include('student_dbcon.php');
        session_start();

        $instructor_id = $_SESSION['instructor_id'];
        $query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
        $stmt = $conn->prepare($query);
        $stmt->bindParam(':instructor_id', $instructor_id);
        $stmt->execute();
        $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

        foreach ($courses as $course) {
            echo "<option value='{$course['id']}'>" . htmlspecialchars($course['course_name']) . "</option>";
        }
        ?>
    </select><br>

    <label for="title">Assignment Title:</label>
    <input type="text" name="title" required><br>

    <label for="description">Description:</label>
    <textarea name="description" required></textarea><br>

    <label for="due_date">Due Date:</label>
    <input type="date" name="due_date" required><br>

    <label for="file">Upload File:</label>
    <input type="file" name="file" accept=".pdf, .docx, .txt" required><br>

    <input type="submit" value="Create Assignment">
</form>
