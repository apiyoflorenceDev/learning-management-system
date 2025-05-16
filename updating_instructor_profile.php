<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['instructor_id'])) {
    echo "You must be logged in to view your profile.";
    exit();
}

$instructor_id = $_SESSION['instructor_id'];

// Fetch current instructor data
$query = "SELECT name, profile_pic FROM instructors WHERE id = :id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':id', $instructor_id);
$stmt->execute();
$instructor = $stmt->fetch(PDO::FETCH_ASSOC);

// Handle form submission
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];

    // Handle image upload
    $upload_dir = 'uploads/';
    $profile_pic = $instructor['profile_pic']; // Retain existing picture if no new file uploaded

    if (!file_exists($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // Check if a new file is uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['profile_pic']['tmp_name'];
        $file_name = uniqid() . '_' . basename($_FILES['profile_pic']['name']);
        $file_path = $upload_dir . $file_name;

        // Validate file type (only images allowed)
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        $file_type = mime_content_type($file_tmp);

        if (in_array($file_type, $allowed_types)) {
            // Move the uploaded file to the target directory
            if (move_uploaded_file($file_tmp, $file_path)) {
                $profile_pic = $file_path;
            } else {
                echo '<div class="alert error">Failed to upload image. Please try again.</div>';
            }
        } else {
            echo '<div class="alert error">Only image files are allowed (JPG, PNG, GIF).</div>';
        }
    }

    // Update instructor info in the database
    $update_query = "UPDATE instructors SET name = :name, profile_pic = :profile_pic WHERE id = :id";
    $stmt = $conn->prepare($update_query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':profile_pic', $profile_pic);
    $stmt->bindParam(':id', $instructor_id);

    if ($stmt->execute()) {
        echo '<div class="alert success">Profile updated successfully.</div>';
        $instructor['name'] = $name;
        $instructor['profile_pic'] = $profile_pic; // Update instructor details after successful DB update
    } else {
        echo '<div class="alert error">Failed to update profile. Please try again later.</div>';
    }
}
?>

<style>
    /* Styling for the profile page */
    body {
        font-family: 'Segoe UI', sans-serif;
        background-color: #f4f6f9;
        padding: 30px;
    }

    h2 {
        color: #2c3e50;
        margin-bottom: 20px;
    }

    .profile-container {
        background: #fff;
        max-width: 600px;
        padding: 30px;
        border-radius: 10px;
        box-shadow: 0 2px 12px rgba(0, 0, 0, 0.1);
    }

    .profile-container img {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        border: 3px solid #3498db;
        margin-bottom: 15px;
    }

    label {
        display: block;
        margin-bottom: 8px;
        font-weight: 600;
    }

    input[type="text"], input[type="file"] {
        width: 100%;
        padding: 10px;
        margin-bottom: 20px;
        border: 1px solid #ccc;
        border-radius: 6px;
    }

    button {
        background-color: #3498db;
        color: white;
        padding: 10px 20px;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s;
    }

    button:hover {
        background-color: #2980b9;
    }

    .alert {
        padding: 12px;
        border-radius: 5px;
        margin-bottom: 20px;
        max-width: 600px;
    }

    .alert.success {
        background-color: #d4edda;
        color: #155724;
        border-left: 5px solid #28a745;
    }

    .alert.error {
        background-color: #f8d7da;
        color: #721c24;
        border-left: 5px solid #dc3545;
    }
</style>

<div class="profile-container">
    <h2>Instructor Profile</h2>

    <!-- Display profile picture -->
    <?php if (!empty($instructor['profile_pic']) && file_exists($instructor['profile_pic'])): ?>
        <img src="<?php echo htmlspecialchars($instructor['profile_pic']); ?>" alt="Profile Picture">
    <?php else: ?>
        <img src="https://via.placeholder.com/120?text=Profile" alt="Default Profile">
    <?php endif; ?>

    <!-- Form to update instructor name and profile picture -->
    <form method="POST" enctype="multipart/form-data">
        <label for="name">Instructor Name</label>
        <input type="text" id="name" name="name" required value="<?php echo htmlspecialchars($instructor['name']); ?>">

        <label for="profile_pic">Upload New Profile Picture</label>
        <input type="file" id="profile_pic" name="profile_pic" accept="image/*">

        <button type="submit">Update Profile</button>
    </form>
</div>
