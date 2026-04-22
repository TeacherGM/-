<?php
header('Content-Type: application/json');
require_once 'db_connect.php';
requireAdmin();

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['image']) && isset($_POST['slideNumber'])) {
    $slideNumber = (int)$_POST['slideNumber'];
    if ($slideNumber < 1 || $slideNumber > 4) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Invalid slide number']);
        exit;
    }

    $target_dir = __DIR__ . "/image/";
    $file = $_FILES['image'];
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // ตรวจสอบนามสกุล
    if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "webp" ) {
        http_response_code(400);
        echo json_encode(['success' => false, 'message' => 'Sorry, only JPG, JPEG, PNG & WEBP files are allowed.']);
        exit;
    }

    // เซฟทับ slide ตามหมายเลข (สมมติว่า front-end อ่านเป็น .jpg อย่างเดียวก็จะบังคับเป็น .jpg)
    $new_filename = "slide" . $slideNumber . ".jpg";
    $target_file = $target_dir . $new_filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        echo json_encode(['success' => true, 'file' => './image/' . $new_filename]);
    } else {
        http_response_code(500);
        echo json_encode(['success' => false, 'message' => 'Sorry, there was an error uploading your file.']);
    }
} else {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Bad request']);
}
?>
