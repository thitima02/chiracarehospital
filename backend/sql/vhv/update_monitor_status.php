<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require '../db_connection.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูล JSON จากการร้องขอ
    $data = json_decode(file_get_contents("php://input"), true);
    $patient_id = $data['patient_id'] ?? null;  
    $monitor_status = $data['monitor_status'] ?? null;

    // ตรวจสอบค่าที่จำเป็น
    if (!$patient_id || !$monitor_status) {
        echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
        exit();
    }

    // อัปเดตสถานะการติดตามในตาราง monitor_information
    $sqlUpdate = "UPDATE monitor_information SET monitor_status = :monitor_status WHERE patient_id = :patient_id";
    $stmtUpdate = $pdo->prepare($sqlUpdate);
    try {
        $stmtUpdate->execute([
            ':monitor_status' => $monitor_status,
            ':patient_id' => $patient_id,
        ]);

        echo json_encode(['success' => true]);
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'ใช้การร้องขอแบบ POST เท่านั้น']);
}
?>
