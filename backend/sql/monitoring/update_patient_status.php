<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "chiracare_follow_up_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
}

// ดึงข้อมูล JSON ที่ส่งมาจาก JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// แสดงข้อมูลที่ได้รับใน log
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

// ตรวจสอบก่อนการอัปเดตว่า ID มีอยู่ในฐานข้อมูลหรือไม่
$placeholders = implode(',', array_fill(0, count($patientIds), '?'));
$check_sql = "SELECT COUNT(*) as count FROM monitor_information WHERE patient_id IN ($placeholders)";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute($patientIds);
$row = $check_stmt->fetch(PDO::FETCH_ASSOC);
$matching_count = $row['count'];

if ($matching_count > 0) {
    // เตรียมคำสั่ง SQL เพื่ออัปเดตสถานะการติดตาม
    $sql = "UPDATE monitor_information 
            SET monitor_status = ?, monitor_deadline = ?, monitor_date = ?, monitor_round = monitor_round + 1
            WHERE patient_id IN ($placeholders)";
    
    $stmt = $pdo->prepare($sql);
    
    // ผูกค่าให้กับ placeholders
    $params = array_merge([$monitorStatus, $monitorDeadline, $monitorStartDate], $patientIds);
    
    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'updated_count' => $stmt->rowCount()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error executing update: ' . $stmt->errorInfo()[2]]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No matching records found for update.']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$pdo = null;
?>