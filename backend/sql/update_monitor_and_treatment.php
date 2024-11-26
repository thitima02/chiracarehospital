<?php
include 'db_connection.php'; // เชื่อมไฟล์ฐานข้อมูล

header('Content-Type: application/json');

// รับข้อมูล JSON ที่ส่งมา
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่าข้อมูลครบถ้วนหรือไม่
if (!isset($data['patient_id']) || !isset($data['monitor_status']) || !isset($data['treatment_status'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: patient_id, monitor_status, or treatment_status.'
    ]);
    exit;
}

// ดึงข้อมูลจาก JSON
$patient_id = $data['patient_id'];
$monitor_status = $data['monitor_status'];
$treatment_status = $data['treatment_status'];
$monitor_date = ($data['monitor_date'] === "ยังไม่มี" || !$data['monitor_date']) ? null : $data['monitor_date'];
$monitor_deadline = ($data['monitor_deadline'] === "ยังไม่มี" || !$data['monitor_deadline']) ? null : $data['monitor_deadline'];
$monitor_round = $data['monitor_round'] ?? null;

try {
    // เริ่มต้นการทำงานแบบ Transaction
    $conn->beginTransaction();

    // อัปเดตสถานะการติดตาม (monitor_status)
    $monitor_stmt = $conn->prepare("
        UPDATE monitor_information 
        SET 
            monitor_status = :monitor_status, 
            monitor_date = :monitor_date, 
            monitor_deadline = :monitor_deadline, 
            monitor_round = :monitor_round 
        WHERE patient_id = :patient_id
    ");
    $monitor_stmt->bindParam(':monitor_status', $monitor_status);
    $monitor_stmt->bindParam(':monitor_date', $monitor_date);
    $monitor_stmt->bindParam(':monitor_deadline', $monitor_deadline);
    $monitor_stmt->bindParam(':monitor_round', $monitor_round);
    $monitor_stmt->bindParam(':patient_id', $patient_id);
    $monitor_stmt->execute();

    // ตรวจสอบว่า patient_id มีอยู่ในตาราง treatment_information หรือไม่
    $check_stmt = $conn->prepare("
        SELECT id 
        FROM treatment_information 
        WHERE patient_id = :patient_id
    ");
    $check_stmt->bindParam(':patient_id', $patient_id);
    $check_stmt->execute();

    if ($check_stmt->rowCount() === 0) {
        throw new Exception('Patient not found in treatment_information.');
    }

    // อัปเดตสถานะการรักษา (treatment_status)
    $treatment_stmt = $conn->prepare("
        UPDATE treatment_information 
        SET treatment_status = :treatment_status, last_update = NOW() 
        WHERE patient_id = :patient_id
    ");
    $treatment_stmt->bindParam(':treatment_status', $treatment_status);
    $treatment_stmt->bindParam(':patient_id', $patient_id);
    $treatment_stmt->execute();

    // ยืนยันการทำงาน
    $conn->commit();

    echo json_encode([
        'status' => 'success',
        'message' => 'Monitor status and treatment status updated successfully.'
    ]);
} catch (Exception $e) {
    // ยกเลิกการเปลี่ยนแปลงหากเกิดข้อผิดพลาด
    $conn->rollBack();
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
$conn = null;
?>
