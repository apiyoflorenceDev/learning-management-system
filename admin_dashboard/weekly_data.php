<?php
$host = 'localhost';
$db = 'student_db';
$user = 'root';
$pass = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    $data = [];

    for ($i = 6; $i >= 0; $i--) {
        $date = date('Y-m-d', strtotime("-$i days"));

        $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM enrollments WHERE DATE(created_at) = :date");
        $stmt->execute(['date' => $date]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        $data[] = [
            'date' => date('D', strtotime($date)),
            'total' => $result['total']
        ];
    }

    echo json_encode($data);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
