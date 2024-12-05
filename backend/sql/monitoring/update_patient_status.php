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
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
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
$treatmentStatus = $data['treatmentStatus'] ?? null;

// ตรวจสอบค่าของตัวแปรสำคัญว่ามีค่าหรือไม่
if (!$monitorStatus || !$patientIds || !$monitorDeadline || !$monitorStartDate || !$treatmentStatus) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required fields']);
    exit;
}

// อัปเดตข้อมูลในฐานข้อมูล
$updatedCount = 0;
foreach ($patientIds as $patientId) {
    $sql = "UPDATE monitor_information 
            SET monitor_status='$monitorStatus', monitor_date='$monitorStartDate', monitor_deadline='$monitorDeadline' 
            WHERE patient_id='$patientId'";

    if ($conn->query($sql) === TRUE) {
        // อัปเดตสถานะการรักษา
        $sqlTreatment = "UPDATE treatment_information 
                         SET treatment_status='$treatmentStatus' 
                         WHERE patient_id='$patientId'";

        if ($conn->query($sqlTreatment) === TRUE) {
            $updatedCount++;
        }
    }
}

// ส่งผลลัพธ์กลับ
if ($updatedCount > 0) {
    echo json_encode(['status' => 'success', 'updated_count' => $updatedCount]);
} else {
    echo json_encode(['status' => 'error', 'message' => 'No records updated']);
}

$conn->close();
?>
