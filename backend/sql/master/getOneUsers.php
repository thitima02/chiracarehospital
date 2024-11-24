<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

$id = $_GET['id'];

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้ทั้งหมดจากตาราง user_info
    $stmt = $conn->prepare("SELECT id, role, username, password, responsibility_area, user_image, full_name, phone_number , department FROM user_info WHERE id = {$id}");
    $stmt->execute();

    // ดึงข้อมูลทั้งหมด
    $users = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($users) {
        // ถ้าพบข้อมูลผู้ใช้
        $response = [
            'status' => 'success',
            'message' => 'ดึงข้อมูลสำเร็จ',
            'data' => $users
        ];
    } else {
        // ถ้าไม่พบข้อมูลผู้ใช้
        $response = [
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลผู้ใช้'
        ];
    }
} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
    $response = [
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];
}

// ส่ง response กลับเป็น JSON
echo json_encode($response);