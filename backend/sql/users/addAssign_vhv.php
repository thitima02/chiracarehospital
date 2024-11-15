<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response
$user_id = $_GET['user_id'];
$patient_id = $_GET['patient_id'];
$user_department = $_GET['user_department'];
$user_responsibility_area = $_GET['user_responsibility_area'];
$patient_address_area = $_GET['patient_address_area'];

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้ทั้งหมดจากตาราง user_info
    $stmt = $conn->prepare("INSERT INTO assign_patients_to_vhv (user_id, patient_id,user_department, user_responsibility_area, patient_address_area) VALUES ('{$user_id}', '{$patient_id}','{$user_department}','{$user_responsibility_area}','{$patient_address_area}')");
    $stmt->execute();
   
} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
    $response = [
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];
}

// ส่ง response กลับเป็น JSON
echo json_encode($response); 