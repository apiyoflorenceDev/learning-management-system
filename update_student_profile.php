<?php
include('student_dbcon.php');
session_start();

if (!isset($_SESSION['student_id'])) {
    echo "You need to be logged in to update your profile.<a href='student_login.php'>Login<>";
    exit();
}

$student_id = $_SESSION['student_id'];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = $_POST['name'];
    $email = $_POST['email'];

    $query = "UPDATE students SET name = :name, email = :email WHERE id = :student_id";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':name', $name);
    $stmt->bindParam(':email', $email);
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);

    if ($stmt->execute()) {
        echo "Profile updated successfully! <a href='student_dashboard.php'>Go Back</a>";
    } else {
        echo "Error updating profile.<a href='updata_student_profile.php'>Please Go Back and Updata Again</a>";
    }
}

// Fetch current details
$query = "SELECT name, email FROM students WHERE id = :student_id";
$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();
$row = $stmt->fetch(PDO::FETCH_ASSOC);
?>

<form method="POST">
    <label>Name:</label>
    <input type="text" name="name" value="<?php echo htmlspecialchars($row['name']); ?>" required><br>

    <label>Email:</label>
    <input type="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required><br>

    <input type="submit" value="Update Profile">
</form>
