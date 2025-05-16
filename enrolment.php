<?php
include('student_dbcon.php'); // Database connection
session_start();

// Get instructor ID from session
$instructor_id = $_SESSION['instructor_id'];

if (!$instructor_id) {
    echo "You need to be logged in to manage courses.";
    exit();
}

// Fetch courses assigned to the instructor from the database
$query = "SELECT id, course_name FROM courses WHERE instructor_id = :instructor_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':instructor_id', $instructor_id);
$stmt->execute();
$courses = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Instructor Enrollment</title>
    <link rel="stylesheet" href="enrolment.css">
</head>
<body>
    <div class="enrollment-container">
        <h2>Student Enrollment</h2>

        <!-- Enroll Student Form -->
        <form method="POST" action="enrolment_handler.php">
            <label for="student_id">Student ID:</label>
            <input type="text"  name="student_id" placeholder="Enter Student ID" required>

            <label for="course_id">Select Course:</label>
            <select  name="course_id" required>
                <option value="">-- Select a Course --</option>
                <?php foreach ($courses as $course): ?>
                    <option value="<?php echo $course['id']; ?>"><?php echo htmlspecialchars($course['course_name']); ?></option>
                <?php endforeach; ?>
            </select>

            <button type="submit">Enroll Student</button>
        </form>

        <h3>Enrolled Students</h3>
        <input type="text" id="searchStudent" placeholder="Search Student...">

        <table>
            <thead>
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Enrollment Date</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="studentList">
                <?php
                $query = "SELECT students.id, students.name, students.email, enrollments.enrollment_date 
          FROM enrollments 
          JOIN students ON enrollments.student_id = students.id 
          JOIN courses ON enrollments.course_id = courses.id 
          WHERE courses.instructor_id = :instructor_id";

                $stmt = $conn->prepare($query);
                $stmt->bindParam(':instructor_id', $instructor_id);
                $stmt->execute();
                $students = $stmt->fetchAll(PDO::FETCH_ASSOC);

                foreach ($students as $student): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($student['id']); ?></td>
                        <td><?php echo htmlspecialchars($student['name']); ?></td>
                        <td><?php echo htmlspecialchars($student['email']); ?></td>
                        <td><?php echo htmlspecialchars($student['enrollment_date']); ?></td>
                        <td><button class="remove-btn" onclick="unenrollStudent('<?php echo $student['id']; ?>')">Remove</button></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <script>
        function unenrollStudent(studentID) {
            if (confirm("Are you sure you want to remove this student?")) {
                window.location.href = "unenroll_student.php?student_id=" + studentID;
            }
        }
    </script>
</body>
</html>
