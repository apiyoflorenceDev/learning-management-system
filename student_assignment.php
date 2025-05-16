<?php
include('student_dbcon.php');
session_start();

$student_id = $_SESSION['student_id'] ?? null;

if (!$student_id) {
    header('Location: login.php');
    exit();
}

// Fetch student profile data
$profile_query = "SELECT name, profile_pic FROM students WHERE id = :student_id";
$profile_stmt = $conn->prepare($profile_query);
$profile_stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$profile_stmt->execute();
$profile_data = $profile_stmt->fetch(PDO::FETCH_ASSOC);

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
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            display: flex;
            min-height: 100vh;
        }
        
        /* Sidebar Styles */
        .sidebar {
            width: 250px;
            background-color: #2c3e50;
            color: white;
            height: 100vh;
            position: fixed;
            padding: 20px 0;
        }
        
        .logo {
            text-align: center;
            padding: 0 20px 20px;
            border-bottom: 1px solid #34495e;
            margin-bottom: 20px;
        }
        
        .menu {
            list-style: none;
            padding: 0 20px;
        }
        
        .menu li {
            margin-bottom: 5px;
        }
        
        .menu a {
            display: flex;
            align-items: center;
            color: #ecf0f1;
            text-decoration: none;
            padding: 12px 15px;
            border-radius: 5px;
            transition: background-color 0.3s;
        }
        
        .menu a:hover {
            background-color: #34495e;
        }
        
        .menu i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }
        
        /* Main Content Styles */
        .main-content {
            margin-left: 250px;
            padding: 30px;
            width: calc(100% - 250px);
            position: relative;
            padding-right: 330px; /* Space for right columns */
        }
        
        .page-title {
            color: #2c3e50;
            margin-bottom: 30px;
        }
        
        /* Right Columns */
        .right-columns-container {
            position: fixed;
            top: 30px;
            right: 30px;
            width: 300px;
            z-index: 10;
        }
        
        .profile-section, .calendar-section {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 6px rgba(0,0,0,0.05);
            margin-bottom: 20px;
        }
        
        .profile-section {
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            width: 100%;
            text-align: center; /* Center all text */
            display: flex;
            flex-direction: column;
            align-items: center; /* Center all child elements */
        }
        
        
        .profile-section h3 {
            margin: 0 0 15px 0;
            width: 100%;
            text-align: center;
        }

        .profile-pic-container {
            margin-bottom: 15px;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        #profile-pic {
            width: 100px;
            height: 100px;
            border-radius: 50%;
            object-fit: cover;
            border: 3px solid #e0e0e0;
            margin: 0 auto; /* Center the image */
        }
        .upload-btn {
            display: inline-block;
            margin-top: 10px;
            padding: 6px 12px;
            background: #3498db;
            color: white;
            border-radius: 4px;
            font-size: 13px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        
        .upload-btn:hover {
            background: #2980b9;
        }
        
        .calendar-section table {
            width: 100%;
            border-collapse: collapse;
        }
        
        .calendar-section th, .calendar-section td {
            padding: 5px;
            text-align: center;
            font-size: 12px;
        }
        
        .calendar-section th {
            color: #7f8c8d;
            font-weight: normal;
        }
        
        .today {
            background: #3498db;
            color: white;
            border-radius: 50%;
            width: 24px;
            height: 24px;
            display: inline-block;
            line-height: 24px;
        }
        
        /* Courses Section */
        .courses-section {
            margin-top: 30px;
            width: calc(100% - 20px);
        }
        
        .course-row-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
        }
        
        .course-row-title {
            font-size: 1.3rem;
            color: #2c3e50;
            font-weight: 600;
        }
        
        .see-all {
            color: #3498db;
            text-decoration: none;
            font-size: 0.9rem;
            transition: color 0.3s;
        }
        
        .see-all:hover {
            color: #2980b9;
            text-decoration: underline;
        }
        
        .course-row {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 40px;
        }
        
        .course-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.05);
            transition: transform 0.3s, box-shadow 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        }
        
        .course-image {
            width: 100%;
            height: 160px;
            object-fit: cover;
            border-radius: 8px;
            margin-bottom: 15px;
        }
        
        .course-title {
            font-size: 1.1rem;
            color: #2c3e50;
            margin-bottom: 10px;
        }
        
        .course-description {
            color: #7f8c8d;
            font-size: 0.9rem;
            line-height: 1.5;
            margin-bottom: 15px;
        }
        
        .enrolled-badge {
            display: inline-block;
            background-color: #2ecc71;
            color: white;
            padding: 3px 10px;
            border-radius: 4px;
            font-size: 12px;
            margin-bottom: 10px;
        }
        
        .enroll-btn, .view-course-btn {
            display: inline-block;
            padding: 8px 16px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 4px;
            font-size: 14px;
            transition: background-color 0.3s;
        }
        
        .enroll-btn:hover, .view-course-btn:hover {
            background-color: #2980b9;
        }
        
        /* Responsive Styles */
        @media (max-width: 1200px) {
            .right-columns-container {
                width: 280px;
            }
            
            .main-content {
                padding-right: 310px;
            }
        }
        
        @media (max-width: 992px) {
            .course-row {
                grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            }
        }
        
        @media (max-width: 768px) {
            .sidebar {
                width: 70px;
                overflow: hidden;
            }
            
            .sidebar .logo h2,
            .sidebar .menu span {
                display: none;
            }
            
            .sidebar .menu a {
                justify-content: center;
                padding: 15px 0;
            }
            
            .sidebar .menu i {
                margin-right: 0;
                font-size: 1.2rem;
            }
            
            .main-content {
                margin-left: 70px;
                padding: 20px;
                padding-right: 20px;
            }
            
            .right-columns-container {
                position: static;
                width: 100%;
                margin-bottom: 30px;
            }
            
            .profile-section, .calendar-section {
                width: 100%;
            }
        }
        
        @media (max-width: 576px) {
            .course-row {
                grid-template-columns: 1fr;
            }
            
            .course-row-header {
                flex-direction: column;
                align-items: flex-start;
                gap: 5px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar Navigation -->
    <div class="sidebar">
        <div class="logo">
            <h2>EduSys</h2>
        </div>
        <ul class="menu">
            <li><a href="#"><i class="fas fa-home"></i><span> Overview</span></a></li>
            <li><a href="enrolled_course.php"><i class="fas fa-book"></i><span> My Courses</span></a></li>
            <li><a href="submit_assignment.php"><i class="fas fa-tasks"></i><span> Assignments</span></a></li>
            <li><a href="student_grade.php"><i class="fas fa-graduation-cap"></i><span> Grades</span></a></li>
            <li><a href="#"><i class="fas fa-envelope"></i><span> Messages</span></a></li>
            <li><a href="student_profile.php"><i class="fas fa-user"></i><span> Profile</span></a></li>
            <li><a href="settings_student.php"><i class="fas fa-cog"></i><span> Settings</span></a></li>
        </ul>
    </div>
    
    <!-- Main Content Area -->
    <div class="main-content">
        <h1 class="page-title">Student Dashboard</h1>
        
        <!-- Profile and Calendar (Right Columns) -->
        <div class="right-columns-container">
            <div class="profile-section">
                <h3>My Profile</h3>
                <div class="profile-pic-container">
                    <img id="profile-pic" src="<?= htmlspecialchars($profile_data['profile_pic'] ?? 'default-avatar.png') ?>" 
                         alt="Profile Picture">
                    <label for="upload-pic" class="upload-btn">Change Photo</label>
                    <input type="file" id="upload-pic" name="profile_pic" accept="image/*" style="display: none;">
                </div>
                <h4><?= htmlspecialchars($profile_data['name'] ?? 'Student') ?></h4>
            </div>
            
            <div class="calendar-section">
                <h3>Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>
        
        <!-- Courses Section -->
        <div class="courses-section">
            <!-- New Courses -->
            <div class="course-row-header">
                <h2 class="course-row-title">New Courses</h2>
                <a href="all_courses.php" class="see-all">View All Courses</a>
            </div>
            <div class="course-row">
                <?php if (!empty($new_courses)): ?>
                    <?php foreach ($new_courses as $course): ?>
                        <div class="course-card">
                            <?php if (!empty($course['course_image'])): ?>
                                <img src="<?= htmlspecialchars($course['course_image']) ?>" 
                                     alt="<?= htmlspecialchars($course['course_name']) ?>" 
                                     class="course-image">
                            <?php else: ?>
                                <div class="course-image" style="background: #ecf0f1; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book-open" style="font-size: 2rem; color: #bdc3c7;"></i>
                                </div>
                            <?php endif; ?>
                            <h3 class="course-title"><?= htmlspecialchars($course['course_name']) ?></h3>
                            <p class="course-description">
                                <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                                <?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>
                            <a href="enroll.php?course_id=<?= $course['id'] ?>" class="enroll-btn">Enroll Now</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="course-card">
                        <p>No new courses available at this time.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- My Courses -->
            <div class="course-row-header">
                <h2 class="course-row-title">My Courses</h2>
                <a href="enrolled_course.php" class="see-all">View All My Courses</a>
            </div>
            <div class="course-row">
                <?php if (!empty($my_courses)): ?>
                    <?php foreach ($my_courses as $course): ?>
                        <div class="course-card">
                            <?php if (!empty($course['course_image'])): ?>
                                <img src="<?= htmlspecialchars($course['course_image']) ?>" 
                                     alt="<?= htmlspecialchars($course['course_name']) ?>" 
                                     class="course-image">
                            <?php else: ?>
                                <div class="course-image" style="background: #ecf0f1; display: flex; align-items: center; justify-content: center;">
                                    <i class="fas fa-book-open" style="font-size: 2rem; color: #bdc3c7;"></i>
                                </div>
                            <?php endif; ?>
                            <h3 class="course-title"><?= htmlspecialchars($course['course_name']) ?></h3>
                            <p class="course-description">
                                <?= htmlspecialchars(substr($course['description'], 0, 100)) ?>
                                <?= strlen($course['description']) > 100 ? '...' : '' ?>
                            </p>
                            <span class="enrolled-badge">Enrolled</span>
                            <a href="course.php?id=<?= $course['id'] ?>" class="view-course-btn">Continue Learning</a>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="course-card">
                        <p>You haven't enrolled in any courses yet.</p>
                        <a href="all_courses.php" class="enroll-btn">Browse Courses</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            // Profile picture upload
            $('#upload-pic').change(function() {
                if (this.files && this.files[0]) {
                    let formData = new FormData();
                    formData.append('profile_pic', this.files[0]);
                    
                    $.ajax({
                        url: 'upload_profile.php',
                        type: 'POST',
                        data: formData,
                        processData: false,
                        contentType: false,
                        success: function(response) {
                            if (response.success) {
                                $('#profile-pic').attr('src', response.new_path);
                                alert('Profile picture updated successfully!');
                            } else {
                                alert('Error: ' + response.error);
                            }
                        },
                        error: function(xhr, status, error) {
                            alert('Error uploading image: ' + error);
                        }
                    });
                }
            });
            
            // Generate calendar
            function generateCalendar() {
                const today = new Date();
                const currentMonth = today.toLocaleString('default', { month: 'long' });
                const currentYear = today.getFullYear();
                const currentDate = today.getDate();
                
                // Get first day of month and total days
                const firstDay = new Date(currentYear, today.getMonth(), 1).getDay();
                const daysInMonth = new Date(currentYear, today.getMonth() + 1, 0).getDate();
                
                // Calendar HTML
                let calendarHtml = `
                    <h4 style="text-align: center; margin-bottom: 10px;">${currentMonth} ${currentYear}</h4>
                    <table style="width: 100%;">
                        <tr>
                            ${['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'].map(day => `<th style="padding: 5px; font-size: 12px;">${day}</th>`).join('')}
                        </tr>
                        <tr>
                `;
                
                // Add empty cells for days before the 1st
                for (let i = 0; i < firstDay; i++) {
                    calendarHtml += '<td></td>';
                }
                
                // Add days of the month
                for (let day = 1; day <= daysInMonth; day++) {
                    if ((day + firstDay - 1) % 7 === 0 && day !== 1) {
                        calendarHtml += '</tr><tr>';
                    }
                    
                    if (day === currentDate) {
                        calendarHtml += `<td style="padding: 5px;"><span class="today">${day}</span></td>`;
                    } else {
                        calendarHtml += `<td style="padding: 5px;">${day}</td>`;
                    }
                }
                
                calendarHtml += `</tr></table>`;
                $('#calendar').html(calendarHtml);
            }
            
            generateCalendar();
        });
    </script>
</body>
</html>