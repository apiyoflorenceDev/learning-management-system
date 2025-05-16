<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["file"])) {
    $title = $_POST['title'];
    $description = $_POST['description'];
    $file_name = $_FILES['file']['name'];
    move_uploaded_file($_FILES['file']['tmp_name'], "uploads/" . $file_name);
    echo "<p>Course '$title' uploaded successfully!</p>";
}
?>