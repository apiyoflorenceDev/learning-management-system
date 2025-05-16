<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard</title>
    <link rel="stylesheet" href="dashboard.css">
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <!-- Sidebar -->
    <div class="sidebar">
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

    <!-- Main Content -->
    <div class="main-content">
        <h2>Welcome to the Student Dashboard</h2>

        <div class="dashboard-grid">
        
            <!-- Profile Section -->
            <div class="profile-section">
                <h3>Profile</h3>
                
                <div class="profile-pic-container">
                    <img id="profile-pic" src="default-avatar.png" alt="Profile Picture">
                    <label for="upload-pic" class="upload-btn">Change</label>
                    <input type="file" id="upload-pic" name="profile_pic" accept="image/*" style="display: none;">
                </div>

                <h4 id="student-name">Loading...</h4>
            </div>

            <!-- Calendar Section -->
            <div class="calendar-section">
                <h3>Calendar</h3>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Courses Section -->
        <div class="courses-container">
            <h3>New Courses</h3>
            <div id="new-courses" class="course-list"></div>

            <h3>My Courses</h3>
            <div id="my-courses" class="course-list"></div>
        </div>
    </div>

    <script>
        $(document).ready(function () {
            // Fetch profile data
            $.ajax({
                url: 'fetch_profile.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }
                    $('#student-name').text(data.name);
                    if (data.profile_pic) {
                        $('#profile-pic').attr('src', data.profile_pic);
                    }
                }
            });

            // Upload Profile Picture
            $('#upload-pic').change(function () {
                let formData = new FormData();
                formData.append('profile_pic', this.files[0]);

                $.ajax({
                    url: 'upload_profile.php',
                    type: 'POST',
                    data: formData,
                    processData: false,
                    contentType: false,
                    success: function (response) {
                        alert(response);
                        location.reload(); // Reload to show the new image
                    }
                });
            });


            // Fetch Courses
            $.ajax({
                url: 'fatching_course.php',
                method: 'GET',
                dataType: 'json',
                success: function (data) {
                    if (data.error) {
                        alert(data.error);
                        return;
                    }

                    let newCoursesHtml = '';
                    data.new_courses.forEach(course => {
                        newCoursesHtml += `
                            <div class="course-card">
                                <img src="${course.course_image}" alt="${course.course_name}" class="course-image">
                                <h4>${course.course_name}</h4>
                                <p>${course.description}</p>
                                <button>Learn More</button>
                            </div>
                        `;
                    });
                    $('#new-courses').html(newCoursesHtml);

                    let myCoursesHtml = '';
                    data.my_courses.forEach(course => {
                        myCoursesHtml += `
                            <div class="course-card">
                                <img src="${course.course_image}" alt="${course.course_name}" class="course-image">
                                <h4>${course.course_name}</h4>
                                <p>${course.description}</p>
                                <button>Go to Course</button>
                            </div>
                        `;
                    });
                    $('#my-courses').html(myCoursesHtml);
                }
            });

            // Generate Calendar
            function generateCalendar() {
                const today = new Date();
                const currentMonth = today.toLocaleString('default', { month: 'long' });
                const currentYear = today.getFullYear();
                const currentDate = today.getDate();

                let calendarHtml = `<h4>${currentMonth} ${currentYear}</h4>`;
                calendarHtml += `<table><tr>`;
                const daysOfWeek = ['Sun', 'Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat'];
                daysOfWeek.forEach(day => calendarHtml += `<th>${day}</th>`);
                calendarHtml += `</tr><tr>`;

                const firstDay = new Date(currentYear, today.getMonth(), 1).getDay();
                for (let i = 0; i < firstDay; i++) {
                    calendarHtml += `<td></td>`;
                }

                const daysInMonth = new Date(currentYear, today.getMonth() + 1, 0).getDate();
                for (let day = 1; day <= daysInMonth; day++) {
                    if ((day + firstDay - 1) % 7 === 0) calendarHtml += `</tr><tr>`;
                    if (day === currentDate) {
                        calendarHtml += `<td class="today">${day}</td>`;
                    } else {
                        calendarHtml += `<td>${day}</td>`;
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
