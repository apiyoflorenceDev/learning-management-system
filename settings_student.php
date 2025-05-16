<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Settings</title>
    <link rel="stylesheet" href="settings.css">
</head>
<body>
<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to access settings.";
    exit();
}

$student_id = $_SESSION['student_id'];

// Fetch current student details
$query = "SELECT name, email FROM students WHERE id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$student = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<h2>Settings</h2>

<h3>Update Profile</h3>
<form action="update_student_profile.php" method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($student['name']); ?>" required><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($student['email']); ?>" required><br>

    <input type="submit" value="Update Profile">
</form>



<h3>Delete Account</h3>
<form action="delete_student_profile.php" method="POST">
    <input type="submit" value="Delete Account" onclick="return confirm('Are you sure? This action cannot be undone!');">
</form>
</body>
</html>

