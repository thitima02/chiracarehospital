<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "chiracare_follow_up_db";

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

// ดึงข้อมูล JSON ที่ส่งมาจาก JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// แสดงข้อมูลที่ได้รับ
error_log('Received data: ' . print_r($data, true));

// ตรวจสอบว่าข้อมูลที่จำเป็นถูกส่งมา
$monitorStatus = $data['monitorStatus'] ?? '';
$patientIds = $data['patientIds'] ?? [];
$monitorDeadline = $data['monitorDeadline'] ?? ''; 
$monitorStartDate = $data['monitorStartDate'] ?? '';

if (empty($monitorStatus) || empty($monitorDeadline) || empty($monitorStartDate) || !is_array($patientIds) || empty($patientIds)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// เตรียมคำสั่ง SQL เพื่ออัปเดตสถานะการติดตาม
$placeholders = implode(',', array_fill(0, count($patientIds), '?'));
$sql = "UPDATE monitor_information 
        SET monitor_status = ?, monitor_deadline = ?, monitor_date = ?, monitor_round = monitor_round + 1
        WHERE patient_id IN ($placeholders)";

$stmt = $conn->prepare($sql);
if ($stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare SQL statement: ' . $conn->error]);
    exit;
}

// ผูกค่าให้กับ placeholders
$types = 'sss' . str_repeat('i', count($patientIds));
$stmt->bind_param($types, $monitorStatus, $monitorDeadline, $monitorStartDate, ...$patientIds);

// ตรวจสอบก่อนการอัปเดตว่า ID มีอยู่ในฐานข้อมูลหรือไม่
$check_sql = "SELECT COUNT(*) as count FROM monitor_information WHERE patient_id IN ($placeholders)";
$check_stmt = $conn->prepare($check_sql);
if ($check_stmt === false) {
    echo json_encode(['status' => 'error', 'message' => 'Failed to prepare check statement: ' . $conn->error]);
    exit;
}
$check_stmt->bind_param(str_repeat('i', count($patientIds)), ...$patientIds);
$check_stmt->execute();
$result = $check_stmt->get_result();
$row = $result->fetch_assoc();
$matching_count = $row['count'];

if ($matching_count > 0) {
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success', 'updated_count' => $stmt->affected_rows]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error executing update: ' . $stmt->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No matching records found for update.']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$stmt->close();
$check_stmt->close();
$conn->close();
?>
