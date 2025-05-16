<?php
include('student_dbcon.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM instructors WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['instructor_id'] = $row['id'];
            header("Location: instructor_dashboard.php");
        } else {
            echo "Invalid password.<p><a href='instructor_login.php'>login Again<></p>";
        }
    } else {
        echo "No instructor found with that email.<p><a href='instructor_signup.php'>Sign Up</a></p>";
    }
}
?>