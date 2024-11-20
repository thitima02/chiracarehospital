<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อฐานข้อมูล
require_once '../db_connection.php'; // แก้ไขเส้นทางตามโครงสร้างไฟล์ของคุณ

// ตรวจสอบว่ามีข้อมูลที่ส่งมาหรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // รับข้อมูลจาก JSON
    $input = json_decode(file_get_contents('php://input'), true);
    $patient_id = $input['patient_id'] ?? null;

    // ตรวจสอบว่ามี patient_id หรือไม่
    if ($patient_id) {
        // สร้างคำสั่ง SQL เพื่ออัปเดต treatment_status
        $sql = "UPDATE treatment_information 
                SET treatment_status = 'ไม่มาตามนัด'
                WHERE patient_id = :patient_id AND treatment_status != 'ไม่มาตามนัด'";

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":patient_id", $patient_id, PDO::PARAM_INT);

        // รันคำสั่ง SQL
        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "Treatment status reset successfully"]);
        } else {
            echo json_encode(["success" => false, "message" => "Failed to reset treatment status"]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Invalid patient_id"]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method"]);
}

// ไม่ต้องปิดการเชื่อมต่อ PDO (ปิดอัตโนมัติ)
?>
