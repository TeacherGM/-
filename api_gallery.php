<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// ดึงข้อมูลรูปภาพจากฐานข้อมูล เรียงจากอันล่าสุดขึ้นก่อน
$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
$limit_sql = $limit > 0 ? " LIMIT $limit" : "";

$sql = "SELECT filename FROM gallery ORDER BY id DESC" . $limit_sql;
$result = $conn->query($sql);

$images = [];

if ($result && $result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // เพิ่มพาธไปยังโฟลเดอร์ image
        $images[] = "./image/" . $row["filename"];
    }
}

echo json_encode([
    'success' => true,
    'images' => $images
]);

$conn->close();
?>
