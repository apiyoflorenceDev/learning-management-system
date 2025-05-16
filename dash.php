<!-- eLearning Dashboard - HTML, CSS, and PHP -->
<!DOCTYPE html>
 <html lang="en">
 <head>
     <meta charset="UTF-8">
     <meta name="viewport" content="width=device-width, initial-scale=1.0">
     <title>Student Dashboard</title>
     <link rel="stylesheet" href="dashboard.css">
 </head>
 <body>
     <?php
     session_start();
     include 'student_dbcon.php';  // Database connection using PDO
     
     try {
         $pdo = new PDO("mysql:host=localhost;dbname=elearning", "root", "");
         $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
     } catch (PDOException $e) {
         die("Database connection failed: " . $e->getMessage());
     }
     
     // Fetch user details
     $userId = $_SESSION['user_id'];
     $stmt = $pdo->prepare("SELECT * FROM users WHERE id = :userId");
     $stmt->execute(['userId' => $userId]);
     $user = $stmt->fetch(PDO::FETCH_ASSOC);
     
     // Fetch new courses
     $newCoursesStmt = $pdo->query("SELECT * FROM courses ORDER BY created_at DESC LIMIT 3");
     $newCourses = $newCoursesStmt->fetchAll(PDO::FETCH_ASSOC);
     
     // Fetch enrolled courses
     $enrolledCoursesStmt = $pdo->prepare("SELECT courses.* FROM enrollments 
         JOIN courses ON enrollments.course_id = courses.id WHERE enrollments.user_id = :userId");
     $enrolledCoursesStmt->execute(['userId' => $userId]);
     $enrolledCourses = $enrolledCoursesStmt->fetchAll(PDO::FETCH_ASSOC);
     ?>

     <div class="sidebar">
         <h2>College</h2>
         <ul>
             <li><a href="#">Dashboard</a></li>
             <li><a href="#">Courses</a></li>
             <li><a href="#">Chats</a></li>
             <li><a href="#">Grades</a></li>
             <li><a href="#">Schedule</a></li>
             <li><a href="#">Settings</a></li>
         </ul>
     </div>

     <div class="content">
         <h1>Dashboard</h1>
         <div class="profile">
             <img src="uploads/<?php echo htmlspecialchars($user['profile_pic']); ?>" alt="Profile Picture">
             <h2><?php echo htmlspecialchars($user['name']); ?></h2>
             <p><?php echo htmlspecialchars($user['major']); ?></p>
             <form action="upload.php" method="post" enctype="multipart/form-data">
                 <input type="file" name="profile_pic" required>
                 <button type="submit" name="upload">Upload</button>
             </form>
         </div>

         <h2>New Courses</h2>
         <div class="courses">
             <?php foreach ($newCourses as $course) { ?>
                 <div class="course">
                     <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                     <p><?php echo htmlspecialchars($course['description']); ?></p>
                     <a href="#">Learn More</a>
                 </div>
             <?php } ?>
         </div>

         <h2>My Courses</h2>
         <div class="courses">
             <?php foreach ($enrolledCourses as $course) { ?>
                 <div class="course">
                     <h3><?php echo htmlspecialchars($course['title']); ?></h3>
                     <p><?php echo htmlspecialchars($course['description']); ?></p>
                 </div>
             <?php } ?>
         </div>
     </div>
     
     <script>
         document.addEventListener("DOMContentLoaded", function () {
             const today = new Date();
             document.getElementById("currentDate").textContent = today.toDateString();
         });
     </script>
 </body>
 </html>