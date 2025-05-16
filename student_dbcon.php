<?php
$host = 'localhost';  // or your host
$db = 'student_db';  // your database name
$username = 'root';  // your DB username
$password = '';  // your DB password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>

