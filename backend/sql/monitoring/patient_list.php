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

// SQL query ดึงข้อมูลจากหลายตาราง
$sql = "SELECT pi.patient_id, pi.full_name, pi.current_status, 
               pm.disease_type, pm.patient_type, pm.patient_group,
               mi.monitor_round, mi.monitor_status, mi.monitor_deadline, mi.monitor_date,
               ti.treatment_status
        FROM patient_information pi
        LEFT JOIN patient_medical_information pm ON pi.patient_id = pm.patient_id
        LEFT JOIN monitor_information mi ON pi.patient_id = mi.patient_id
        LEFT JOIN treatment_information ti ON pi.patient_id = ti.patient_id";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row; // เก็บแต่ละแถวเป็น array
    }
} else {
    $data = ["message" => "ไม่มีข้อมูล"];
}

// ส่งข้อมูลเป็น JSON
echo json_encode($data);

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
