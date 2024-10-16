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

// Debug: แสดงข้อมูลที่ได้รับ
error_log('Received JSON data: ' . json_encode($data));

// ตรวจสอบการถอดรหัส JSON
if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['status' => 'error', 'message' => 'Invalid JSON input', 'raw_input' => file_get_contents("php://input")]);
    exit;
}

// ดึง patientIds จากข้อมูลที่ถอดรหัส
$patientIds = $data['patientIds'] ?? [];

// Debug: แสดง patientIds
error_log('Patient IDs: ' . json_encode($patientIds)); // คำสั่งนี้จะช่วยให้คุณเห็นค่าที่ได้รับ

// ตรวจสอบว่ามี patientIds หรือไม่
if (!empty($patientIds)) {
    // เตรียมคำสั่ง SQL
    $placeholders = implode(',', array_fill(0, count($patientIds), '?'));
    $sql = "
        UPDATE monitor_information 
        SET monitor_status = 'กำลังติดตาม' 
        WHERE id_patient_information IN ($placeholders)
    ";

    try {
        $stmt = $conn->prepare($sql);
        if ($stmt === false) {
            throw new Exception("Failed to prepare SQL statement: " . $conn->error);
        }

        // ผูกค่าให้กับ placeholders
        $stmt->bind_param(str_repeat('i', count($patientIds)), ...$patientIds);
        $stmt->execute();

        // Debug: แสดงจำนวนแถวที่ได้รับการอัปเดต
        error_log('Rows affected: ' . $stmt->affected_rows);

        if ($stmt->affected_rows > 0) {
            echo json_encode(['status' => 'success', 'updated_count' => $stmt->affected_rows]);
        } else {
            echo json_encode(['status' => 'warning', 'message' => 'No records updated.']);
        }

    } catch (Exception $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }

} else {
    echo json_encode(['status' => 'error', 'message' => 'No patient IDs provided']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
