<?php
$host = 'localhost';
$dbname = 'student_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $sql = "SELECT c.id AS course_id, c.course_name, c.course_image, t.topic_name
            FROM courses c
            LEFT JOIN topics t ON c.id = t.course_id
            ORDER BY c.id, t.id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute();

    $courses = [];

    while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
        $courseId = $row['course_id'];
        $courseName = $row['course_name'];
        $topicName = $row['topic_name'];
        $imagePath = $row['course_image'] ?? 'images/default.jpg';

        if (!isset($courses[$courseId])) {
            $courses[$courseId] = [
                'name' => $courseName,
                'image' => $imagePath,
                'topics' => []
            ];
        }

        if ($topicName !== null) {
            $courses[$courseId]['topics'][] = $topicName;
        }
    }
} catch (PDOException $e) {
    echo "<p class='error'>Error: " . htmlspecialchars($e->getMessage()) . "</p>";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Courses with Images</title>
    <style>
        body {
            font-family: 'Segoe UI', sans-serif;
            background: #f0f4f8;
            margin: 0;
            padding: 0;
        }

        h2 {
            text-align: center;
            margin-top: 30px;
            color: #333;
        }

        .courses-list {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
            padding: 30px;
            max-width: 1200px;
            margin: auto;
        }

        .course-card {
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 6px 16px rgba(0, 0, 0, 0.08);
            padding: 0 0 20px;
            transition: transform 0.3s ease-in-out;
            overflow: hidden;
        }

        .course-card:hover {
            transform: translateY(-6px);
        }

        .course-card img {
            width: 100%;
            height: 180px;
            object-fit: cover;
        }

        .course-content {
            padding: 20px;
        }

        .course-card h3 {
            margin: 0 0 10px;
            color: #007acc;
        }

        .course-card ul {
            list-style-type: disc;
            padding-left: 20px;
        }

        .course-card li {
            margin-bottom: 5px;
        }

        .course-card p {
            font-style: italic;
            color: #888;
        }

        .error {
            text-align: center;
            color: red;
            font-weight: bold;
            margin-top: 30px;
        }
    </style>
</head>
<body>

<h2>Available Courses</h2>

<?php if (!empty($courses)): ?>
    <div class="courses-list">
        <?php foreach ($courses as $course): ?>
            <div class="course-card">
                <img src="<?= htmlspecialchars($course['image']) ?>" alt="Course Image">
                <div class="course-content">
                    <h3><?= htmlspecialchars($course['name']) ?></h3>
                    <?php if (!empty($course['topics'])): ?>
                        <ul>
                            <?php foreach ($course['topics'] as $topic): ?>
                                <li><?= htmlspecialchars($topic) ?></li>
                            <?php endforeach; ?>
                        </ul>
                    <?php else: ?>
                        <p>No topics yet for this course.</p>
                    <?php endif; ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <p class="error">No courses found.</p>
<?php endif; ?>

</body>
</html>
