<?php
$host = 'localhost'; // เปลี่ยนเป็น host ของคุณ
$db = 'chiracare_follow_up_db'; // ชื่อฐานข้อมูลของคุณ
$user = 'root'; // ชื่อผู้ใช้
$pass = ''; // รหัสผ่าน

try {
    $conn = new PDO("mysql:host=$host;dbname=$db", $user, $pass);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Connection failed: " . $e->getMessage();
}
?>
