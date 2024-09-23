<?php
session_start();

// ลบข้อมูล session ที่เกี่ยวข้องกับการเข้าสู่ระบบ
if (isset($_SESSION['user'])) {
    unset($_SESSION['user']);
}

// หากใช้ JWT token ให้ลบ token ที่เก็บในฐานข้อมูล (ถ้ามี)
// ที่นี่คุณอาจต้องเขียนโค้ดเพื่ออัปเดตสถานะของ token ในฐานข้อมูล

// ส่งการตอบกลับกลับไปยังฝั่งผู้ใช้
$response = [
    'status' => 'success',
    'message' => 'ออกจากระบบเรียบร้อยแล้ว'
];

header('Content-Type: application/json');
echo json_encode($response);
?>
