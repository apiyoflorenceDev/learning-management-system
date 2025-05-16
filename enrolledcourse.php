<?php
include('student_dbcon.php');
session_start();

// Check if student is logged in
if (!isset($_SESSION['student_id'])) {
    header('Location: login.php');
    exit();
}

$student_id = $_SESSION['student_id'];

try {
    // Fetch enrolled courses with additional details
    $query = "SELECT 
                c.id, 
                c.course_name, 
                c.description, 
                c.course_image,
                e.enrollment_date,
                (SELECT COUNT(*) FROM enrollments WHERE course_id = c.id) AS total_students
              FROM courses c
              JOIN enrollments e ON c.id = e.course_id
              WHERE e.student_id = :student_id
              ORDER BY e.enrollment_date DESC";
    
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $enrolled_courses = $stmt->fetchAll(PDO::FETCH_ASSOC);

} catch (PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Enrolled Courses</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        .enrolled-courses-container {
            max-width: 1200px;
            margin: 20px auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        .course-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }
        
        .course-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 25px;
        }
        
        .course-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            overflow: hidden;
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .course-image-container {
            height: 180px;
            overflow: hidden;
        }
        
        .course-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s;
        }
        
        .course-card:hover .course-image {
            transform: scale(1.05);
        }
        
        .course-details {
            padding: 20px;
        }
        
        .course-title {
            font-size: 1.2rem;
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .course-description {
            color: #7f8c8d;
            margin-bottom: 15px;
            line-height: 1.5;
        }
        
        .course-meta {
            display: flex;
            justify-content: space-between;
            font-size: 0.9rem;
            color: #7f8c8d;
            margin-top: 15px;
            padding-top: 15px;
            border-top: 1px solid #eee;
        }
        
        .view-course-btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        
        .view-course-btn:hover {
            background-color: #2980b9;
        }
        
        .no-courses {
            text-align: center;
            padding: 40px;
            color: #7f8c8d;
            font-size: 1.1rem;
        }
        
        .alert {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
        }
        
        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        
        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
    </style>
</head>
<body>
    <?php include('sidebar.php'); ?>
    
    <div class="main-content">
        <div class="enrolled-courses-container">
            <div class="course-header">
                <h1>My Enrolled Courses</h1>
            </div>
            
            <?php if (isset($_SESSION['success'])): ?>
                <div class="alert alert-success">
                    <?= $_SESSION['success']; unset($_SESSION['success']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-error">
                    <?= $_SESSION['error']; unset($_SESSION['error']); ?>
                </div>
            <?php endif; ?>
            
            <?php if (isset($error)): ?>
                <div class="alert alert-error">
                    <?= $error; ?>
                </div>
            <?php endif; ?>
            
            <?php if (!empty($enrolled_courses)): ?>
                <div class="course-list">
                    <?php foreach ($enrolled_courses as $course): ?>
                        <div class="course-card">
                            <div class="course-image-container">
                                <img src="uploads/images/<?= htmlspecialchars($course['course_image']) ?>" 
                                     alt="<?= htmlspecialchars($course['course_name']) ?>" 
                                     class="course-image">
                            </div>
                            <div class="course-details">
                                <h3 class="course-title"><?= htmlspecialchars($course['course_name']) ?></h3>
                                <p class="course-description">
                                    <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                                    <?= strlen($course['description']) > 100 ? '...' : '' ?>
                                </p>
                                <a href="course.php?id=<?= $course['id'] ?>" class="view-course-btn">
                                    View Course
                                </a>
                                <div class="course-meta">
                                    <span>Enrolled: <?= date('M d, Y', strtotime($course['enrollment_date'])) ?></span>
                                    <span><?= $course['total_students'] ?> students</span>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="no-courses">
                    <p>You are not enrolled in any courses yet.</p>
                    <a href="courses.php" class="view-course-btn">Browse Available Courses</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>