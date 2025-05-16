<?php
// Include database connection file
include('student_dbcon.php');

// Start the session to get the logged-in student information
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Assume the student is logged in and their ID is stored in the session
$student_id = $_SESSION['student_id'];

// Check if student ID is set
if (!$student_id) {
    echo "You need to be logged in to view your courses.";
    exit();
}

try {
    // Fetch courses for the student using PDO
    $query = "SELECT courses.course_name, courses.course_code, enrollments.enrollment_date
              FROM courses
              JOIN enrollments ON courses.id = enrollments.course_id
              WHERE enrollments.student_id = :student_id";
    
    // Prepare the SQL query
    $stmt = $conn->prepare($query);

    // Bind the student ID parameter
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    
    // Execute the statement
    $stmt->execute();

    // Display courses
    if ($stmt->rowCount() > 0) {
        echo "<h3>Your Courses</h3>";
        echo "<div id='coursesContainer'>";
        echo "<table class='coursesTable'>";
        echo "<tr><th>Course Name</th><th>Course Code</th><th>Enrollment Date</th></tr>";

        // Fetch and display each course
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<tr><td>" . $row['course_name'] . "</td><td>" . $row['course_code'] . "</td><td>" . $row['enrollment_date'] . "</td></tr>";
        }

        echo "</table>";
        echo "</div>";
    } else {
        echo "<p>You are not enrolled in any courses.</p> <a href='student_dashboard.php'>Go back</a>";
    }
} catch (PDOException $e) {
    // Catch any errors and display a message
    echo "Error: " . $e->getMessage();
}

$conn = null;  // Close the connection
?>

<!-- Sidebar Navigation -->
<div id="sidebar">
    <ul>
        <li><a href="javascript:void(0);" onclick="showCourses()">My Courses</a></li>
        <!-- Add other sidebar items here -->
    </ul>
</div>

<!-- Add this CSS for better styling -->
<style>
    body {
        font-family: Arial, sans-serif;
        margin: 0;
        padding: 0;
        display: flex;
        min-height: 100vh;
    }

    #sidebar {
        background-color: #2c3e50;
        width: 250px;
        height: 100%;
        position: fixed;
        top: 0;
        left: 0;
        color: white;
        padding-top: 20px;
    }

    #sidebar ul {
        list-style-type: none;
        padding: 0;
    }

    #sidebar ul li {
        padding: 15px;
        text-align: center;
    }

    #sidebar ul li a {
        color: white;
        text-decoration: none;
        font-size: 18px;
        display: block;
    }

    #sidebar ul li a:hover {
        background-color: #34495e;
    }

    #coursesContainer {
        margin-left: 250px;
        padding: 20px;
        width: calc(100% - 250px);
    }

    .coursesTable {
        width: 100%;
        border-collapse: collapse;
        margin-top: 20px;
    }

    .coursesTable th, .coursesTable td {
        padding: 12px;
        text-align: left;
        border: 1px solid #ddd;
    }

    .coursesTable th {
        background-color: #3498db;
        color: white;
    }

    .coursesTable tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    .coursesTable tr:hover {
        background-color: #f1f1f1;
    }
</style>

<!-- Add this JavaScript for sidebar functionality -->
<script>
    function showCourses() {
        document.getElementById('coursesContainer').scrollIntoView({behavior: 'smooth'});
    }
</script>
