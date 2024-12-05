<?php
header('Content-Type: application/json');

// เชื่อมต่อฐานข้อมูล
require_once 'db_connection.php'; // เชื่อมไฟล์ฐานข้อมูล

// ตรวจสอบว่าวิธีที่ใช้คือ POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode([
        'status' => 'error',
        'message' => 'Invalid request method. Use POST.'
    ]);
    exit;
}

// รับข้อมูล JSON ที่ส่งมา
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบว่าข้อมูลที่ส่งมาครบถ้วนหรือไม่
if (!isset($data['patient_id']) || !isset($data['treatment_status']) || !isset($data['monitor_status'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: patient_id, treatment_status, or monitor_status.'
    ]);
    exit;
}

// ดึงข้อมูลจาก JSON
$patient_id = $data['patient_id'];
$treatment_status = $data['treatment_status'];
$monitor_status = isset($data['monitor_status']) ? $data['monitor_status'] : null;
$monitor_date = isset($data['monitor_date']) && !empty($data['monitor_date']) ? $data['monitor_date'] : null;
$monitor_deadline = isset($data['monitor_deadline']) && !empty($data['monitor_deadline']) ? $data['monitor_deadline'] : null;
$monitor_round = isset($data['monitor_round']) && !empty($data['monitor_round']) ? $data['monitor_round'] : null;

// ตรวจสอบว่า patient_id มีอยู่ในฐานข้อมูลหรือไม่
try {
    $check_stmt = $conn->prepare("SELECT id FROM treatment_information WHERE patient_id = ?");
    $check_stmt->bind_param('i', $patient_id);
    $check_stmt->execute();
    $result = $check_stmt->get_result();

    if ($result->num_rows === 0) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Patient not found.'
        ]);
        $check_stmt->close();
        exit;
    }

    $check_stmt->close();

    // อัปเดตสถานะการรักษาและการติดตามในฐานข้อมูล
    $stmt = $conn->prepare("UPDATE treatment_information 
                            SET treatment_status = ?, monitor_status = ?, monitor_date = ?, monitor_deadline = ?, monitor_round = ?, last_update = NOW() 
                            WHERE patient_id = ?");
    $stmt->bind_param('sssssi', $treatment_status, $monitor_status, $monitor_date, $monitor_deadline, $monitor_round, $patient_id);

    if ($stmt->execute()) {
        echo json_encode([
            'status' => 'success',
            'message' => 'Treatment and monitoring statuses updated successfully.'
        ]);
    } else {
        echo json_encode([
            'status' => 'error',
            'message' => 'Failed to update treatment and monitoring statuses.'
        ]);
    }
    $stmt->close();
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
