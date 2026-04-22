-- โค้ดสร้างฐานข้อมูลและตารางสำหรับระบบจัดการรูปภาพ
-- สำหรับอิมพอร์ตเข้าไปใน http://localhost/phpmyadmin
-- ฐานข้อมูลชื่อ: Excavator

CREATE DATABASE IF NOT EXISTS `Excavator` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `Excavator`;

-- สร้างตารางเก็บรูปภาพ
CREATE TABLE IF NOT EXISTS `gallery` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `filename` varchar(255) NOT NULL,
  `uploaded_at` timestamp NOT NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- แถม: เพิ่มรูปตัวอย่างเริ่มต้นลงในฐานข้อมูล
INSERT INTO `gallery` (`filename`) VALUES 
('gallery1.jpg'),
('gallery2.jpg'),
('gallery3.jpg'),
('gallery4.jpg');
