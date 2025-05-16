<?php
// get_courses.php
include 'config.php';

try {
    // Prepare SQL statement to fetch all courses
    $stmt = $pdo->prepare("SELECT * FROM courses");

    // Execute the statement
    $stmt->execute();

    // Fetch all courses as an associative array
    $courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if (count($courses) > 0) {
        echo json_encode($courses);
    } else {
        echo "No courses found";
    }
} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}

$conn = null; // Close the connection
?>
