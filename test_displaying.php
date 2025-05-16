
<style>
.courses-container {
    display: flex;
    flex-wrap: wrap;
    gap: 20px;
    padding: 20px;
}

.course {
    border: 1px solid #ddd;
    border-radius: 8px;
    padding: 15px;
    width: 300px;
    box-shadow: 0 2px 5px rgba(0,0,0,0.1);
}

.course-image {
    width: 100%;
    height: auto;
    border-radius: 5px;
    margin-bottom: 10px;
}

.course h2 {
    margin-top: 0;
    color: #333;
}
</style>

<?php
// Database connection configuration
$host = 'localhost';
$dbname = 'student_db';
$username = 'root';
$password = '';

try {
    // Create a PDO connection
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // SQL query to fetch course information
    $sql = "SELECT course_name, description, course_image FROM courses";
    $stmt = $pdo->query($sql);
    
    // Check if there are any courses
    if ($stmt->rowCount() > 0) {
        echo "<div class='courses-container'>";
        
        // Fetch each course and display it
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            echo "<div class='course'>";
            echo "<h2>" . htmlspecialchars($row['course_name']) . "</h2>";
            
            // Display the course image if it exists
            if (!empty($row['course_image'])) {
                echo "<img src='" . htmlspecialchars($row['course_image']) . "' alt='" . htmlspecialchars($row['course_name']) . "' class='course-image'>";
            }
            
            echo "<p>" . htmlspecialchars($row['description']) . "</p>";
            echo "</div>";
        }
        
        echo "</div>";
    } else {
        echo "<p>No courses found.</p>";
    }
} catch (PDOException $e) {
    // Handle database connection errors
    echo "Database error: " . $e->getMessage();
}
?>