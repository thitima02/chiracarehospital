<?php
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include('../db_connection.php');

// สร้าง SQL Query เพื่อนับจำนวนผู้ป่วยที่รักษาเสร็จสิ้นและยังไม่ได้รักษา
$sql = "SELECT 
            SUM(CASE WHEN t.treatment_status = 'มาตามนัด' THEN 1 ELSE 0 END) AS totalTreated,
            SUM(CASE WHEN t.treatment_status = 'ไม่มาตามนัด' THEN 1 ELSE 0 END) AS totalUntreated 
        FROM patient_information p
        JOIN treatment_information t ON t.id_patient_information = p.id";  // เปลี่ยน m เป็น t ที่นี่

// Execute the query
$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->errorInfo()); // แสดงข้อผิดพลาดจาก SQL
}

// ใช้ fetch(PDO::FETCH_ASSOC) แทน fetch_assoc()
$row = $result->fetch(PDO::FETCH_ASSOC);

// ตรวจสอบผลลัพธ์และเตรียมข้อมูลส่งกลับ
$data = [
    'totalTreated' => $row['totalTreated'] ?? 0,
    'totalUntreated' => $row['totalUntreated'] ?? 0
];

echo json_encode($data); // ส่งผลลัพธ์กลับในรูปแบบ JSON

// ปิดการเชื่อมต่อ (ไม่จำเป็นใน PDO แต่ใช้ได้กับ mysqli)
$conn = null;
?>
