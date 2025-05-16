<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You need to be logged in to grade students.";
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch courses assigned to the instructor
$query = "SELECT c.id AS course_id, c.course_name, s.id AS student_id, s.name AS student_name 
          FROM courses c
          JOIN enrollments e ON c.id = e.course_id
          JOIN students s ON e.student_id = s.id
          WHERE c.instructor_id = :instructor_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$students = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Handle grade submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $student_id = $_POST['student_id'];
    $course_id = $_POST['course_id'];
    $grade = $_POST['grade'];

    $query = "INSERT INTO grades (student_id, course_id, grade) 
              VALUES (:student_id, :course_id, :grade) 
              ON DUPLICATE KEY UPDATE grade = :grade";

    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id);
    $stmt->bindParam(':course_id', $course_id);
    $stmt->bindParam(':grade', $grade);

    if ($stmt->execute()) {
        echo '<div class="alert success">Grade assigned successfully!</div>';
    } else {
        echo '<div class="alert error">Error assigning grade.</div>';
    }
}
?>

<style>
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
        padding: 20px;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 20px;
    }

    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #ffffff;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        border-radius: 10px;
        overflow: hidden;
    }

    thead {
        background-color: #2c3e50;
        color: #ffffff;
    }

    th, td {
        padding: 12px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }

    tr:hover {
        background-color: #f1f1f1;
    }

    form {
        display: flex;
        align-items: center;
        gap: 10px;
    }

    select {
        padding: 6px 10px;
        border-radius: 5px;
        border: 1px solid #ccc;
        background-color: #fafafa;
    }

    button {
        padding: 6px 12px;
        background-color: #3498db;
        color: white;
        border: none;
        border-radius: 5px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    button:hover {
        background-color: #2980b9;
    }

    a {
        color: #3498db;
        text-decoration: none;
        font-weight: 500;
    }

    a:hover {
        text-decoration: underline;
    }

    .alert {
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        width: 100%;
        max-width: 600px;
    }

    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 5px solid #dc3545;
    }
</style>

<h2>Grade Students</h2>

<table>
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Course</th>
            <th>Grade</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($students as $student): ?>
            <tr>
                <td><?php echo htmlspecialchars($student['student_name']); ?></td>
                <td><?php echo htmlspecialchars($student['course_name']); ?></td>
                <td>
                    <?php
                    $grade_query = "SELECT grade FROM grades WHERE student_id = :student_id AND course_id = :course_id";
                    $grade_stmt = $conn->prepare($grade_query);
                    $grade_stmt->bindParam(':student_id', $student['student_id']);
                    $grade_stmt->bindParam(':course_id', $student['course_id']);
                    $grade_stmt->execute();
                    $grade = $grade_stmt->fetch(PDO::FETCH_ASSOC);
                    $current_grade = isset($grade['grade']) ? $grade['grade'] : '';
                    ?>
                    <form method="POST" action="grading.php">
                        <input type="hidden" name="student_id" value="<?php echo $student['student_id']; ?>">
                        <input type="hidden" name="course_id" value="<?php echo $student['course_id']; ?>">
                        <select name="grade" required>
                            <option value="">-- Select Grade --</option>
                            <option value="A" <?php if ($current_grade == 'A') echo 'selected'; ?>>A</option>
                            <option value="B" <?php if ($current_grade == 'B') echo 'selected'; ?>>B</option>
                            <option value="C" <?php if ($current_grade == 'C') echo 'selected'; ?>>C</option>
                            <option value="D" <?php if ($current_grade == 'D') echo 'selected'; ?>>D</option>
                            <option value="F" <?php if ($current_grade == 'F') echo 'selected'; ?>>F</option>
                        </select>
                        <button type="submit">Assign</button>
                    </form>
                </td>
                <td>
                    <a href="edit_grade.php?id=<?php echo $student['student_id']; ?>&course_id=<?php echo $student['course_id']; ?>">Edit</a> |
                    <a href="delete_grade.php?id=<?php echo $student['student_id']; ?>&course_id=<?php echo $student['course_id']; ?>" onclick="return confirm('Are you sure you want to delete this grade?');">Delete</a>
                </td>
            </tr>
        <?php endforeach; ?>
    </tbody>
</table>
