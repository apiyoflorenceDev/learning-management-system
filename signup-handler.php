<?php
require_once 'dbcon.php';

// Check if form is submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve and sanitize input
    $name = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = trim($_POST["password"]);
    $role = $_POST["role"];

    // Validate input fields
    if (empty($username) || empty($email) || empty($password) || empty($role)) {
        die("All fields are required. <a href='signup.php'>Go back</a>");
    }

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        die("Invalid email format. <a href='signup.php'>Go back</a>");
    }

    // Check if email already exists
    $stmt = $pdo->prepare("SELECT id FROM users WHERE email = :email");
    $stmt->execute(["email" => $email]);

    if ($stmt->fetch()) {
        die("Email already registered. <a href='signup.php'>Try again</a>");
    }

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_BCRYPT);

    // Insert new user into database
    $stmt = $pdo->prepare("INSERT INTO users (username, email, password,role) VALUES (:username, :email, :password,:role)");
    $success = $stmt->execute([
        "username" => $name,
        "email" => $email,
        "password" => $hashed_password,
        "role" => $role
    ]);

    if ($success) {
        echo "Signup successful! <a href='login.php'>Login here</a>";
    } else {
        echo "Signup failed. Please try again.";
    }
} else {
    // Redirect if accessed directly
    header("Location: signup.php");
    exit();
}
?>
