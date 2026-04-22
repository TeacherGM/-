<?php
header('Content-Type: application/json');
require_once 'db_connect.php';

// --- สร้างตารางถ้ายังไม่มี ---
$createTable = "CREATE TABLE IF NOT EXISTS `articles` (
    `id` int(11) NOT NULL AUTO_INCREMENT,
    `title` varchar(255) NOT NULL,
    `description` text DEFAULT NULL,
    `image` varchar(255) DEFAULT NULL,
    `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
    PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($createTable);

$method = $_SERVER['REQUEST_METHOD'];

if ($method == 'GET') {
    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 0;
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id > 0) {
        $stmt = $conn->prepare("SELECT * FROM articles WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $row['image_url'] = './image/' . $row['image'];
            echo json_encode(['success' => true, 'article' => $row]);
        } else {
            echo json_encode(['success' => false, 'message' => 'ไม่พบบทความ']);
        }
        $stmt->close();
    } else {
        $limit_sql = $limit > 0 ? " LIMIT $limit" : "";
        
        $sql = "SELECT * FROM articles ORDER BY id DESC" . $limit_sql;
        $result = $conn->query($sql);
        
        $articles = [];
        if ($result && $result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $row['image_url'] = './image/' . $row['image'];
                $articles[] = $row;
            }
        }
        echo json_encode(['success' => true, 'articles' => $articles]);
    }

} elseif ($method == 'POST') {
    requireAdmin();
    
    $title = isset($_POST['title']) ? $_POST['title'] : '';
    $description = isset($_POST['description']) ? $_POST['description'] : '';
    
    if (empty($title) || !isset($_FILES['image'])) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit;
    }

    $target_dir = __DIR__ . "/image/";
    $file = $_FILES['image'];
    $imageFileType = strtolower(pathinfo($file["name"], PATHINFO_EXTENSION));
    
    // สร้างชื่อไฟล์ใหม่
    $filename = "article_" . time() . "_" . rand(1000,9999) . "." . $imageFileType;
    $target_file = $target_dir . $filename;

    if (move_uploaded_file($file["tmp_name"], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO articles (title, description, image) VALUES (?, ?, ?)");
        $stmt->bind_param("sss", $title, $description, $filename);
        
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'เพิ่มบทความสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'บันทึกลงฐานข้อมูลไม่สำเร็จ']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'อัปโหลดรูปภาพไม่สำเร็จ']);
    }

} elseif ($method == 'DELETE') {
    requireAdmin();
    $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
    
    if ($id > 0) {
        // หาชื่อไฟล์รูปก่อนลบ
        $stmt = $conn->prepare("SELECT image FROM articles WHERE id = ?");
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $prev_image = __DIR__ . "/image/" . $row['image'];
            if (file_exists($prev_image)) {
                unlink($prev_image);
            }
        }
        $stmt->close();

        // ลบจาก DB
        $stmt = $conn->prepare("DELETE FROM articles WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode(['success' => true, 'message' => 'ลบบทความสำเร็จ']);
        } else {
            echo json_encode(['success' => false, 'message' => 'ลบจากฐานข้อมูลไม่สำเร็จ']);
        }
        $stmt->close();
    } else {
        echo json_encode(['success' => false, 'message' => 'ID ไม่ถูกต้อง']);
    }
}

$conn->close();
?>
