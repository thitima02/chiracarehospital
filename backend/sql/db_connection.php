<?php
$host = 'localhost';
$dbname = 'chiracare_follow_up_db';
$username = 'root';  // ค่าเริ่มต้นสำหรับ XAMPP
$password = '';      // ค่าเริ่มต้นสำหรับ XAMPP

try {
    // สร้างการเชื่อมต่อกับฐานข้อมูล
    $conn = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $username, $password);
    // กำหนดให้ PDO แจ้งข้อผิดพลาด
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    // ลบข้อความนี้ออกหรือคอมเมนต์ออก
    // echo "เชื่อมต่อสำเร็จ!";
} catch (PDOException $e) {
   // echo "การเชื่อมต่อล้มเหลว: " . $e->getMessage();
}
?>
