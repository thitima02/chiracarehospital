<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

include('../db_connection.php');

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if (!$conn) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// รับข้อมูลจาก request
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่า $data มีข้อมูลหรือไม่
if ($data === null) {
    echo json_encode([
        'status' => 'error', 
        'message' => 'Invalid JSON data received',
        'received_data' => file_get_contents('php://input') // แสดงข้อมูลที่ได้รับจาก request
    ]);
    exit;
}

// รับข้อมูลจาก request
$monitorStatus = $data['monitorStatus'] ?? null;
$patientIds = $data['patientIds'] ?? null;
$monitorDeadline = $data['monitorDeadline'] ?? null;
$monitorStartDate = $data['monitorStartDate'] ?? null;

// ตรวจสอบค่าของตัวแปรสำคัญว่ามีค่าหรือไม่
if (!$monitorStatus || !$patientIds || !$monitorDeadline || !$monitorStartDate) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// อัปเดตข้อมูลในฐานข้อมูล
$updatedCount = 0;
foreach ($patientIds as $patientId) {
    $sql = "UPDATE monitor_information 
            SET monitor_status=:monitorStatus, monitor_date=:monitorStartDate, monitor_deadline=:monitorDeadline 
            WHERE patient_id=:patientId";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':monitorStatus', $monitorStatus);
    $stmt->bindParam(':monitorStartDate', $monitorStartDate);
    $stmt->bindParam(':monitorDeadline', $monitorDeadline);
    $stmt->bindParam(':patientId', $patientId);

    if ($stmt->execute()) {
        $updatedCount++;
    }
}

// ส่งผลลัพธ์กลับ
if ($updatedCount > 0) {
    echo json_encode(['status' => 'success', 'updated_count' => $updatedCount]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No records updated']);
}

// ปิดการเชื่อมต่อ PDO
$conn = null;
?>
