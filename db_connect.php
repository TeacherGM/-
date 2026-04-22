<?php
// เริ่มต้น Session เพื่อใช้งานล็อกอิน
session_start();

$host = 'localhost';
$user = 'root';
$pass = ''; // รหัสผ่าน XAMPP/MAMP ปกติจะปล่อยว่างไว้ หรือใส่ตามที่คุณตั้งค่า
$dbname = 'Excavator';
$port = 3306;

$conn = new mysqli($host, $user, $pass, $dbname, $port);

if ($conn->connect_error) {
    die(json_encode([
        'success' => false, 
        'message' => 'Database connection failed: ' . $conn->connect_error
    ]));
}

// ตั้งค่าให้ดึงภาษาไทยได้ถูกต้อง
$conn->set_charset("utf8mb4");

// ฟังก์ชันสำหรับเช็คว่าล็อกอินเป็นแอดมินหรือยัง
function requireAdmin() {
    if (!isset($_SESSION['isAdmin']) || $_SESSION['isAdmin'] !== true) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => 'Unauthorized']);
        exit;
    }
}
?>
