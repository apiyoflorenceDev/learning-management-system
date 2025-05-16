<!-- index.php -->

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard</title>
    <link rel="stylesheet" href="style.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="script.js" defer></script>
</head>

<body>
    <!-- Toggle Button for Mobile -->
    <button class="toggle-btn" onclick="toggleSidebar()">☰</button>

    <!-- Sidebar -->
    <div class="sidebar">
        <h2>Admin Panel</h2>
        <ul>
            <li class="active">Dashboard</li>
            <li>Courses</li>
            <li>Instructors</li>
            <li>Students</li>
            <li>Enrolment</li>
            <li>Messages</li>
            <li>Admin Profile</li>
            <li>Settings</li>
        </ul>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <h1>Dashboard</h1>

        <!-- Statistics Cards -->
        <div class="cards">
            <div class="card">
                <h3>Total Enrolments</h3>
                <p id="totalEnrolments">0</p>
            </div>
        </div>
        

        <!-- Weekly Progress Chart -->
        <section class="charts">
            <h2>Weekly Progress</h2>
            <canvas id="weeklyProgressChart" width="400" height="200"></canvas>
        </section>

        <!-- Messages Panel -->
        <section class="messages">
            <h2>Messages</h2>
            <div class="message-box">
                <p>No new messages.</p>
            </div>
        </section>

    </div>

    <script src="script.js"></script>
</body>

</html>
