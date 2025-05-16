<?php
include('student_dbcon.php');
session_start();

// Ensure the student is logged in
$student_id = $_SESSION['student_id'] ?? null;

if (!$student_id) {
    echo "<p style='color: red; text-align: center;'>You need to be logged in to view your grades.</p>";
    exit();
}

// Fetch grades for the student
$query = "SELECT courses.course_name, grades.grade
          FROM grades
          JOIN courses ON grades.course_id = courses.id
          WHERE grades.student_id = :student_id";

$stmt = $conn->prepare($query);
$stmt->bindParam(':student_id', $student_id, PDO::PARAM_INT);
$stmt->execute();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Grades</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 20px;
            text-align: center;
        }
        h3 {
            color: #333;
        }
        table {
            width: 50%;
            margin: 20px auto;
            border-collapse: collapse;
            background: #fff;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        th, td {
            padding: 12px;
            border: 1px solid #ddd;
            text-align: left;
        }
        th {
            background-color:rgb(35, 60, 87);
            color: white;
        }
        tr:nth-child(even) {
            background-color: #f2f2f2;
        }
        p {
            font-size: 16px;
        }
        a {
            display: inline-block;
            margin-top: 10px;
            padding: 10px 15px;
            background:rgb(26, 47, 69);
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        a:hover {
            background:rgb(28, 50, 74);
        }
    </style>
</head>
<body>

<h3>Your Grades</h3>

<?php if ($stmt->rowCount() > 0): ?>
    <table>
        <tr>
            <th>Course Name</th>
            <th>Grade</th>
        </tr>
        <?php while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
            <tr>
                <td><?php echo htmlspecialchars($row['course_name']); ?></td>
                <td><?php echo htmlspecialchars($row['grade']); ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
<?php else: ?>
    <p>You have no grades to show.</p>
<?php endif; ?>

<a href="student_dashboard.php">Go Back</a>

</body>
</html>
