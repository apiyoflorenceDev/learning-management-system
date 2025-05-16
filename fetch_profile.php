<?php
include('student_dbcon.php');
session_start();

$student_id = $_SESSION['student_id']; // Ensure student is logged in
if (!$student_id) {
    echo json_encode(['error' => 'You need to be logged in.']);
    exit();
}

try {
    $stmt = $conn->prepare("SELECT name, profile_pic FROM students WHERE id = :student_id");
    $stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
    $stmt->execute();
    $profile = $stmt->fetch(PDO::FETCH_ASSOC);

    echo json_encode($profile);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
