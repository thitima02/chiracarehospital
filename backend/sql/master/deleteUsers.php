<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response
$user_id = $_GET['user_id'];

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้ทั้งหมดจากตาราง user_info
    $stmt = $conn->prepare("DELETE FROM user_info WHERE id = {$user_id}");
    $stmt->execute();
    $response = [
        'status' => 'success',
        'message' => 'ลบข้อมูลสำเร็จ: '
    ];


} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
    $response = [
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];

}

// ส่ง response กลับเป็น JSON
echo json_encode($response); 