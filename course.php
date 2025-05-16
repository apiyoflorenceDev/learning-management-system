<?php
session_start();
include 'student_dbcon.php'; // PDO connection

// Redirect if not logged in
if (!isset($_SESSION['student_id'])) {
    header("Location: login.php");
    exit();
}

if (!isset($_GET['id'])) {
    die("Course ID is missing.");
}

$courseId = (int) $_GET['id'];
$userId = $_SESSION['student_id'];

// Check if user is enrolled
$check = $conn->prepare("SELECT * FROM enrollments WHERE student_id = :userId AND course_id = :courseId");
$check->execute(['userId' => $userId, 'courseId' => $courseId]);
if (!$check->fetch()) {
    die("You are not enrolled in this course.");
}

// Fetch course details
$courseStmt = $conn->prepare("SELECT * FROM courses WHERE id = :courseId");
$courseStmt->execute(['courseId' => $courseId]);
$course = $courseStmt->fetch(PDO::FETCH_ASSOC);

if (!$course) {
    die("Course not found.");
}

// Fetch topics
$topicsStmt = $conn->prepare("SELECT * FROM topics WHERE course_id = :courseId");
$topicsStmt->execute(['courseId' => $courseId]);
$topics = $topicsStmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($course['course_name']) ?> - Course</title>
    <link rel="stylesheet" href="styles.css">
    <style>
        .topic {
            margin-bottom: 30px;
        }
        .materials {
            list-style: square;
            padding-left: 20px;
        }
        .materials li {
            margin-bottom: 5px;
        }
        .modal {
            position: fixed;
            z-index: 1000;
            left: 0; top: 0;
            width: 100%; height: 100%;
            background-color: rgba(0,0,0,0.7);
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .modal-content {
            background: white;
  padding: 20px;
  width: 90%;
  height: 90%;
  overflow: hidden;
  position: relative;
  border-radius: 10px;
  display: flex;
  flex-direction: column;
        }
        .close {
            position: absolute;
            top: 10px; right: 20px;
            font-size: 28px;
            cursor: pointer;
        }
        .download-link {
            display: inline-block;
            margin-top: 10px;
            background-color: #007BFF;
            color: white;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 4px;
        }
        .download-link:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
<div class="content">
    <h1><?= htmlspecialchars($course['course_name']) ?></h1>
    <p><?= nl2br(htmlspecialchars($course['description'])) ?></p>

    <?php if ($topics): ?>
        <?php foreach ($topics as $topic): ?>
            <div class="topic">
                <h2><?= htmlspecialchars($topic['topic_name']) ?></h2>
                <p><?= nl2br(htmlspecialchars($topic['topic_description'])) ?></p>
                <ul class="materials">
                <?php
                $materialsStmt = $conn->prepare("SELECT * FROM learning_materials WHERE topic_id = :topicId");
                $materialsStmt->execute(['topicId' => $topic['id']]);
                $materials = $materialsStmt->fetchAll(PDO::FETCH_ASSOC);

                if ($materials):
                    foreach ($materials as $material): 
                        $filePath = '' . $material['file_path'];
                        $fileExtension = strtolower(pathinfo($filePath, PATHINFO_EXTENSION));
                        $fileName = basename($material['file_path']);
                ?>
                        <li>
                            <?php if (file_exists($filePath)): ?>
                                <a href="<?= $filePath ?>" download><?= htmlspecialchars($fileName) ?> (<?= strtoupper($fileExtension) ?>)</a>
                                <?php if (in_array($fileExtension, ['pdf', 'mp4'])): ?>
                                    <button onclick="previewMedia('<?= $filePath ?>', '<?= $fileExtension ?>')">Preview</button>
                                <?php endif; ?>
                            <?php else: ?>
                                <span style="color:red;">File not found: <?= htmlspecialchars($fileName) ?></span>
                            <?php endif; ?>
                        </li>
                    <?php endforeach;
                else: ?>
                    <li>No learning materials for this topic yet.</li>
                <?php endif; ?>
                </ul>
            </div>
        <?php endforeach; ?>
    <?php else: ?>
        <p>No topics added to this course yet.</p>
    <?php endif; ?>
</div>

<!-- Modal -->
<div id="mediaModal" class="modal" style="display:none;">
    <div class="modal-content">
        <span class="close" onclick="closeModal()">&times;</span>
        <div id="mediaPreview"></div>
        <a id="downloadBtn" href="#" download class="download-link">Download</a>
    </div>
</div>

<script>
function previewMedia(fileUrl, fileType) {
    const modal = document.getElementById("mediaModal");
    const preview = document.getElementById("mediaPreview");
    const downloadBtn = document.getElementById("downloadBtn");

    let content = "";

    if (fileType === 'pdf') {
        content = `<iframe src="${fileUrl}" width="100%" height="600px"></iframe>`;
    } else if (fileType === 'mp4') {
        content = `<video width="100%" height="auto" controls><source src="${fileUrl}" type="video/mp4">Your browser does not support the video tag.</video>`;
    } else {
        content = `<p>Preview not available for this file type.</p>`;
    }

    preview.innerHTML = content;
    downloadBtn.href = fileUrl;
    modal.style.display = "flex";
}

function closeModal() {
    document.getElementById("mediaModal").style.display = "none";
    document.getElementById("mediaPreview").innerHTML = "";
}
</script>
</body>
</html>
