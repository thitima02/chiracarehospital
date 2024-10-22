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
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// ดึงข้อมูล JSON ที่ส่งมาจาก JavaScript
$data = json_decode(file_get_contents("php://input"), true);

// แสดงข้อมูลที่ได้รับ
error_log('Received data: ' . print_r($data, true));

// ตรวจสอบ monitorStatus และ patientIds ว่ามีการส่งมาหรือไม่
$monitorStatus = $data['monitorStatus'] ?? '';
$patientIds = $data['patientIds'] ?? [];

if (empty($monitorStatus) || empty($patientIds)) {
    echo json_encode(['status' => 'error', 'message' => 'No patient IDs or monitor status provided']);
    exit;
}

// ตรวจสอบ ID ที่ส่งมา
error_log('Patient IDs for update: ' . implode(',', $patientIds));

// เตรียมคำสั่ง SQL
$placeholders = implode(',', array_fill(0, count($patientIds), '?'));
$sql = "
    UPDATE monitor_information 
    SET monitor_status = ? 
    WHERE patient_id IN ($placeholders)
";

// เพิ่ม log เพื่อตรวจสอบคำสั่ง SQL
error_log('SQL Query: ' . $sql);

try {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    // ผูกค่าให้กับ placeholders
    $types = 's' . str_repeat('i', count($patientIds)); // 's' สำหรับ monitorStatus, 'i' สำหรับ patientIds
    $stmt->bind_param($types, $monitorStatus, ...$patientIds);

    // ตรวจสอบก่อนการอัปเดตว่า ID มีอยู่ในฐานข้อมูลหรือไม่
    $sql_check = "SELECT * FROM monitor_information WHERE patient_id IN ($placeholders)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param(str_repeat('i', count($patientIds)), ...$patientIds);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    // Debug: แสดงจำนวนแถวที่พบ
    error_log('Matching records found: ' . $result_check->num_rows);

    if ($result_check->num_rows > 0) {
        // ทำการอัปเดต
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("SQL error: " . $stmt->error);
        }
        echo json_encode(['status' => 'success', 'updated_count' => $stmt->affected_rows]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching records found for update.']);
    }

    $stmt_check->close(); // ปิด statement ตรวจสอบ
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
