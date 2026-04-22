<?php
header('Content-Type: application/json');
require_once 'db_connect.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'DELETE' && isset($_GET['filename'])) {
    
    $filename = basename($_GET['filename']); // ป้องกัน Path traversal
    
    // ลบไฟล์ออกจากตารางฐานข้อมูลก่อน
    $stmt = $conn->prepare("DELETE FROM gallery WHERE filename = ?");
    $stmt->bind_param("s", $filename);
    $stmt->execute();
    $deletedRow = $stmt->affected_rows;
    $stmt->close();
    
    // จากนั้นก็ลบไฟล์ออกจากโฟลเดอร์ image
    $filepath = __DIR__ . '/image/' . $filename;
    if (file_exists($filepath)) {
        unlink($filepath);
    }
    
    if($deletedRow > 0){
        echo json_encode(['success' => true, 'message' => 'File deleted']);
    }else{
        // อาจจะไม่มีข้อมูลใน DB แต่เผื่อลบไฟล์ดื้อๆ ด้วยก็ตอบ success ไป
        echo json_encode(['success' => true, 'message' => 'Cleaned up file only']);
    }

} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
}

$conn->close();
?>
