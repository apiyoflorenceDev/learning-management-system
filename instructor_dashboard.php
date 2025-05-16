<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Instructor Dashboard</title>
  
  <!-- Font Awesome for Icons -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  
  <style>
    * {
      box-sizing: border-box;
    }

    body {
      margin: 0;
      font-family: 'Inter', sans-serif;
      background-color: #f5f7fa;
      display: flex;
    }

    /* Sidebar Styles */
    .sidebar {
      background-color: #1f2937;
      color: #fff;
      width: 260px;
      height: 100vh;
      padding: 20px 15px;
      position: fixed;
      top: 0;
      left: 0;
      display: flex;
      flex-direction: column;
      gap: 10px;
      overflow-y: auto;
    }

    .sidebar h2 {
      font-size: 1.5rem;
      margin-bottom: 30px;
      text-align: center;
      font-weight: 700;
    }

    .sidebar a {
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 10px 15px;
      border-radius: 8px;
      text-decoration: none;
      color: #e5e7eb;
      transition: background 0.2s;
      font-weight: 500;
    }

    .sidebar a:hover {
      background-color: #374151;
      color: #fff;
    }

    .sidebar i {
      font-size: 16px;
      width: 20px;
    }

    /* Main Content */
    .main-content {
      margin-left: 260px;
      padding: 40px;
      width: calc(100% - 260px);
      min-height: 100vh;
    }

    .dashboard-header {
      font-size: 1.8rem;
      font-weight: 600;
      color: #1f2937;
      margin-bottom: 10px;
    }

    .dashboard-subtext {
      color: #6b7280;
      margin-bottom: 30px;
    }

    .dashboard-actions {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(240px, 1fr));
      gap: 25px;
    }

    .action-card {
      background-color: #ffffff;
      border-radius: 16px;
      padding: 25px 20px;
      box-shadow: 0 4px 12px rgba(0, 0, 0, 0.06);
      transition: all 0.3s ease;
      cursor: pointer;
      display: flex;
      flex-direction: column;
      justify-content: space-between;
    }

    .action-card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.1);
    }

    .action-icon {
      font-size: 28px;
      color: #4f46e5;
      margin-bottom: 15px;
    }

    .action-title {
      font-size: 1.1rem;
      font-weight: 600;
      margin-bottom: 5px;
      color: #111827;
    }

    .action-desc {
      font-size: 0.95rem;
      color: #6b7280;
    }

    @media (max-width: 768px) {
      .sidebar {
        position: relative;
        width: 100%;
        height: auto;
        flex-direction: row;
        overflow-x: auto;
        padding: 10px;
      }

      .main-content {
        margin-left: 0;
        width: 100%;
        padding: 20px;
      }
    }
  </style>
</head>
<body>

  <!-- Sidebar -->
  <div class="sidebar">
    <h2>Instructor Panel</h2>
    <a href="javascript:void(0);" onclick="loadDashboard()"><i class="fa-solid fa-house"></i> Dashboard</a>
    <a href="javascript:void(0);" onclick="loadContent('create_course.php')"><i class="fa-solid fa-pen"></i> Create Course</a>
    <a href="javascript:void(0);" onclick="loadContent('course_management.php')"><i class="fa-solid fa-layer-group"></i> Manage Courses</a>
    <a href="javascript:void(0);" onclick="loadContent('add_topic.php')"><i class="fa-solid fa-folder-plus"></i> Create Topics</a>
    <a href="javascript:void(0);" onclick="loadContent('manage_topics.php')"><i class="fa-solid fa-folder-open"></i> Manage Topics</a>
    <a href="javascript:void(0);" onclick="loadContent('enrolment.php')"><i class="fa-solid fa-user-plus"></i> Enroll Students</a>
    <a href="javascript:void(0);" onclick="loadContent('creating_assignment.php')"><i class="fa-solid fa-upload"></i> Upload Assignments</a>
    <a href="javascript:void(0);" onclick="loadContent('display_coursefor_selection.php')"><i class="fa-solid fa-list"></i> View Submissions</a>
    <a href="javascript:void(0);" onclick="loadContent('grading.php')"><i class="fa-solid fa-star"></i> Grade Students</a>
    <a href="javascript:void(0);" onclick="loadContent('send_message.php')"><i class="fa-solid fa-comments"></i> Messages</a>
    <a href="javascript:void(0);" onclick="loadContent('updating_instructor_profile.php')"><i class="fa-solid fa-user"></i> Profile</a>
    <a href="javascript:void(0);" onclick="loadContent('instructor_settings.php')"><i class="fa-solid fa-gear"></i> Settings</a>
    <a href="javascript:void(0);" onclick="loadContent('instructor_logout.php')"><i class="fas fa-sign-out-alt"></i><span> Logout</span></a>
  </div>

  <!-- Main Content -->
  <div class="main-content" id="content">
    <div class="dashboard-header">Welcome to the Instructor Dashboard</div>
    <div class="dashboard-subtext">Manage your courses, students, and content efficiently.</div>

    <div class="dashboard-actions">
      <div class="action-card" onclick="loadContent('create_course.php')">
        <i class="fa-solid fa-book action-icon"></i>
        <div class="action-title">Create Course</div>
        <div class="action-desc">Launch a new course with modules and objectives.</div>
      </div>

      <div class="action-card" onclick="loadContent('creating_assignment.php')">
        <i class="fa-solid fa-file-arrow-up action-icon"></i>
        <div class="action-title">Upload Assignment</div>
        <div class="action-desc">Distribute assignments to students for evaluation.</div>
      </div>

      <div class="action-card" onclick="loadContent('display_coursefor_selection.php')">
        <i class="fa-solid fa-list-check action-icon"></i>
        <div class="action-title">View Submissions</div>
        <div class="action-desc">Review student work and track progress.</div>
      </div>

      <div class="action-card" onclick="loadContent('grading.php')">
        <i class="fa-solid fa-star action-icon"></i>
        <div class="action-title">Grade Students</div>
        <div class="action-desc">Assign scores and publish results to students.</div>
      </div>

      <div class="action-card" onclick="loadContent('enrolment.php')">
        <i class="fa-solid fa-user-plus action-icon"></i>
        <div class="action-title">Enroll Students</div>
        <div class="action-desc">Add learners to your active courses easily.</div>
      </div>

      <div class="action-card" onclick="loadContent('messages_instructor/send_message.php')">
        <i class="fa-solid fa-comment-dots action-icon"></i>
        <div class="action-title">Send Message</div>
        <div class="action-desc">Communicate with students and resolve queries.</div>
      </div>

      <div class="action-card" onclick="loadContent('instructor_settings.php')">
        <i class="fa-solid fa-cog action-icon"></i>
        <div class="action-title">Settings</div>
        <div class="action-desc">Update preferences, account info, and more.</div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script>
    function loadContent(page) {
      fetch(page)
        .then(response => {
          if (!response.ok) throw new Error("Network response was not ok");
          return response.text();
        })
        .then(data => {
          document.getElementById('content').innerHTML = data;
        })
        .catch(error => {
          console.error('Error loading content:', error);
          document.getElementById('content').innerHTML = '<p>Error loading content. Please try again later.</p>';
        });
    }

    function loadDashboard() {
      // Reload the main dashboard content
      location.reload();
    }
  </script>

</body>
</html>
