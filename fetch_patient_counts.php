<?php
// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'chiracare_follow_up_db');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้าง SQL Query เพื่อนับจำนวนผู้ป่วยที่รักษาเสร็จสิ้นและยังไม่ได้รักษา
$sql = "SELECT 
            SUM(CASE WHEN t.treatment_status = 'รักษาเสร็จสิ้น' THEN 1 ELSE 0 END) AS totalTreated,
            SUM(CASE WHEN t.treatment_status = 'ยังไม่ได้รักษา' THEN 1 ELSE 0 END) AS totalUntreated 
        FROM patient_information p
        JOIN treatment_information t ON t.id_patient_information = p.id";  // เปลี่ยน m เป็น t ที่นี่

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); // แสดงข้อผิดพลาดจาก SQL
}

$row = $result->fetch_assoc();

$data = [
    'totalTreated' => $row['totalTreated'] ?? 0,
    'totalUntreated' => $row['totalUntreated'] ?? 0
];

echo json_encode($data); // ส่งผลลัพธ์กลับในรูปแบบ JSON

$conn->close();
?>
