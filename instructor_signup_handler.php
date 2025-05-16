<?php
require_once 'student_dbcon.php';

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $name = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);

    // Validate input fields
    if (empty($username) || empty($email) || empty($password)) {
        die("All fields are required. <a href='signup.php'>Go back</a>");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. <a href='instructor_signup.php'>Go back</a>");
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM instructors WHERE email = :email");
    $stmt->execute(["email" => $email]);

    if ($stmt->fetch()) {
        die("Email already registered. <a href='instructor_signup.php'>Try again</a>");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into database
    $stmt = $conn->prepare("INSERT INTO instructors (name, email, password) VALUES (:name, :email, :password)");
    $success = $stmt->execute([
        "name" => $name,
        "email" => $email,
        "password" => $hashed_password
    ]);

    if ($success) {
        echo "Signup successful! <a href='instructor_login.php'>Login here</a>";
    } else {
        echo "Signup failed. Please try again. <a href='instructor_signup.php'>Go Back </a> ";
    }
} else {
    // Redirect if accessed directly
    header("Location: instructor_signup.php");
    exit();
}
?>
