<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

$ADMIN_USER = "admin";
$ADMIN_PASSWORD = "admin1234";

// รับค่าจากแบบฟอร์ม (ถ้าส่งมาเป็น JSON)
$input = json_decode(file_get_contents('php://input'), true);

if (isset($_GET['action']) && $_GET['action'] == 'check') {
    if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] === true) {
        echo json_encode(['success' => true, 'isAdmin' => true]);
    } else {
        echo json_encode(['success' => true, 'isAdmin' => false]);
    }
    exit;
}

if (isset($_GET['action']) && $_GET['action'] == 'logout') {
    session_destroy();
    echo json_encode(['success' => true]);
    exit;
}

// ตรวจสอบข้อมูลล็อกอิน
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = isset($input['username']) ? $input['username'] : '';
    $password = isset($input['password']) ? $input['password'] : '';
    
    if ($username === $ADMIN_USER && $password === $ADMIN_PASSWORD) {
        $_SESSION['isAdmin'] = true;
        echo json_encode(['success' => true, 'message' => 'Login successful']);
    } else {
        http_response_code(401);
        echo json_encode(['success' => false, 'message' => 'Invalid username or password']);
    }
}
?>
