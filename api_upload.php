<?php
header('Content-Type: application/json');
require_once 'db_connect.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image'])) {
    
    $target_dir = __DIR__ . "/image/";
    if(!is_dir($target_dir)) {
        mkdir($target_dir, 0755, true);
    }

    $file = $_FILES['image'];
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // ตรวจสอบนามสกุล
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp" ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & WEBP files are allowed.']);
        exit;
    }

    // สร้างชื่อไฟล์ใหม่
    $new_filename = "gallery_" . time() . "_" . rand(1000,9999) . "." . $imageFileType;
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        
        // บันทึกชื่อลงฐานข้อมูล
        $stmt = $conn->prepare("INSERT INTO gallery (filename) VALUES (?)");
        $stmt->bind_param("s", $new_filename);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'file' => './image/' . $new_filename]);
        } else {
            http_response_code(500);
            echo json_encode(['success' => false, 'message' => 'DB insert failed']);
        }
        $stmt->close();
        
    } else {
        $upload_error = isset($file['error']) ? $file['error'] : 'Unknown';
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file. Error Code: ' . $upload_error]);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'No file uploaded']);
}
$conn->close();
?>
