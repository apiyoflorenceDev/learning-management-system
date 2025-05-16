<?php
include('student_dbcon.php');
session_start();

$student_id = $_SESSION['student_id']; // Ensure student is logged in

if (!$student_id) {
    header('Location: login.php'); // Redirect to login if not logged in
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Dashboard</title>
    <style>
        .courses-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            padding: 20px;
        }
        .course-section {
            width: 100%;
            margin-bottom: 30px;
        }
        .course-section h2 {
            border-bottom: 2px solid #ddd;
            padding-bottom: 10px;
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
            height: 180px;
            object-fit: cover;
            border-radius: 5px;
            margin-bottom: 10px;
        }
        .course h3 {
            margin-top: 0;
            color: #333;
        }
        .enrolled-badge {
            background-color: #4CAF50;
            color: white;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            display: inline-block;
            margin-top: 10px;
        }
        .enroll-btn {
            background-color: #2196F3;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <h1>Course Dashboard</h1>
    
    <?php
    try {
        // Fetch enrolled courses
        $query_my = "SELECT courses.id, courses.course_name, courses.description, courses.course_image 
                    FROM courses 
                    JOIN enrollments ON courses.id = enrollments.course_id 
                    WHERE enrollments.student_id = :student_id";
        $stmt_my = $conn->prepare($query_my);
        $stmt_my->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt_my->execute();
        $my_courses = $stmt_my->fetchAll(PDO::FETCH_ASSOC);
        
        // Fetch new courses (not enrolled)
        $query_new = "SELECT id, course_name, description, course_image FROM courses 
                    WHERE id NOT IN 
                    (SELECT course_id FROM enrollments WHERE student_id = :student_id)";
        $stmt_new = $conn->prepare($query_new);
        $stmt_new->bindParam(':student_id', $student_id, PDO::PARAM_INT);
        $stmt_new->execute();
        $new_courses = $stmt_new->fetchAll(PDO::FETCH_ASSOC);
        
        // Display enrolled courses
        echo '<div class="course-section">';
        echo '<h2>My Enrolled Courses</h2>';
        if (!empty($my_courses)) {
            echo '<div class="courses-container">';
            foreach ($my_courses as $course) {
                echo '<div class="course">';
                if (!empty($course['course_image'])) {
                    echo '<img src="uploads/images/' . htmlspecialchars($course['course_image']) . '" 
                         alt="' . htmlspecialchars($course['course_name']) . '" class="course-image">';
                }
                echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($course['description']) . '</p>';
                echo '<span class="enrolled-badge">Enrolled</span>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>You are not enrolled in any courses yet.</p>';
        }
        echo '</div>';
        
        // Display available courses
        echo '<div class="course-section">';
        echo '<h2>Available Courses</h2>';
        if (!empty($new_courses)) {
            echo '<div class="courses-container">';
            foreach ($new_courses as $course) {
                echo '<div class="course">';
                if (!empty($course['course_image'])) {
                    echo '<img src="uploads/images/' . htmlspecialchars($course['course_image']) . '" 
                         alt="' . htmlspecialchars($course['course_name']) . '" class="course-image">';
                }
                echo '<h3>' . htmlspecialchars($course['course_name']) . '</h3>';
                echo '<p>' . htmlspecialchars($course['description']) . '</p>';
                echo '<a href="enroll.php?course_id=' . $course['id'] . '" class="enroll-btn">Enroll Now</a>';
                echo '</div>';
            }
            echo '</div>';
        } else {
            echo '<p>No new courses available at this time.</p>';
        }
        echo '</div>';
        
    } catch (PDOException $e) {
        echo '<div class="error">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    }
    ?>
</body>
</html>