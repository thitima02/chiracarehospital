<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

if (isset($_GET['id'])) {
    // รับค่า id จาก URL
    $id = $_GET['id'];

    try {
        // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้ตาม id จากตาราง user_info
        $stmt = $conn->prepare("SELECT id, role, username, responsibility_area, user_image, full_name, phone_number FROM user_info WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT); // ผูกค่า id กับคำสั่ง SQL
        $stmt->execute();

        // ดึงข้อมูลผู้ใช้
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ถ้าพบข้อมูลผู้ใช้
            $response = [
                'status' => 'success',
                'message' => 'ดึงข้อมูลสำเร็จ',
                'data' => $user
            ];
        } else {
            // ถ้าไม่พบข้อมูลผู้ใช้
            $response = [
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลผู้ใช้ที่มี id นี้'
            ];
        }
    } catch (PDOException $e) {
        // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
        $response = [
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
} else {
    // ถ้าไม่ได้ส่งค่า id มาพร้อมกับคำขอ
    $response = [
        'status' => 'error',
        'message' => 'กรุณาส่งค่า id'
    ];
}

// ส่ง response กลับเป็น JSON
echo json_encode($response);
