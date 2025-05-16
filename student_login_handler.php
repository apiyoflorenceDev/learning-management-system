<?php
include('student_dbcon.php');
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    $query = "SELECT * FROM students WHERE email = :email";
    $stmt = $conn->prepare($query);
    $stmt->bindParam(':email', $email);
    $stmt->execute();
    
    if ($stmt->rowCount() > 0) {
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        
        if (password_verify($password, $row['password'])) {
            $_SESSION['student_id'] = $row['id'];
            header("Location: student_dashboard.php");
        } else {
            echo "Invalid password.<a href ='student_login.php'>Go Back</a>";
        }
    } else {
        echo "No student found with that email. <a href ='student_signup.php'>Go Back<>";
    }
}
?>