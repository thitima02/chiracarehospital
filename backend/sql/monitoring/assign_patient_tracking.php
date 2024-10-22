<?php
header('Content-Type: application/json');

// เปิดการแสดงข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db"; // ชื่อฐานข้อมูลที่ใช้

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// รับข้อมูล JSON ที่ส่งมาจาก JavaScript
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบค่าที่ส่งมา
if (!isset($data['selected_patients']) || !is_array($data['selected_patients'])) {
    echo json_encode(['success' => false, 'error' => 'selected_patients ไม่ถูกต้อง']);
    exit; // หยุดการทำงานถ้าข้อมูลไม่ถูกต้อง
}

$selectedPatients = $data['selected_patients'];
$userId = $data['id'];

// เริ่มการทำธุรกรรม
$conn->begin_transaction();

try {
    foreach ($selectedPatients as $patientId) {
        // ตรวจสอบสถานะปัจจุบันของผู้ป่วย
        $sql = "SELECT monitor_status FROM monitor_information WHERE patient_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $patientId);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows === 0) {
            throw new Exception("ไม่พบผู้ป่วยที่มี ID: $patientId");
        }

        $patient = $result->fetch_assoc();

        // ถ้าสถานะเป็น "ยังไม่ได้ติดตาม" ให้เปลี่ยนเป็น "กำลังติดตาม"
        if ($patient['monitor_status'] === 'ยังไม่ได้ติดตาม') {
            $updateSql = "UPDATE monitor_information SET monitor_status = 'กำลังติดตาม', assigned_user = ?, assigned_timestamp = NOW() WHERE patient_id = ?";
            $updateStmt = $conn->prepare($updateSql);
            $updateStmt->bind_param("ii", $userId, $patientId);
            $updateStmt->execute();
        }
    }

    // ยืนยันการทำธุรกรรม
    $conn->commit();
    echo json_encode(['success' => true]);
} catch (Exception $e) {
    // หากมีข้อผิดพลาด ให้ทำการย้อนกลับธุรกรรม
    $conn->rollback();
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
