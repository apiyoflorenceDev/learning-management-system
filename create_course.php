<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Creation</title>
    <link rel="stylesheet" href="courses.css"> <!-- Link to CSS file -->
</head>
<body>

    <form method="POST" action="create_course_handler.php" enctype="multipart/form-data">
        <label for="course_name">Course Name</label>
        <input type="text" name="course_name" required><br>

        <label for="course_code">Course Code</label>
        <input type="text" name="course_code" required><br>

        <label for="description">Description</label>
        <textarea name="description" required></textarea><br>

        <label for="course_image">Upload Course Image </label>
        <input type="file" name="course_image" accept="image/*"><br>

        <input type="submit" value="Create Course">
    </form>

</body>
</html>
