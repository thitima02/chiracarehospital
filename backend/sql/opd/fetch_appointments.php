<?php
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
require_once('../db_connection.php'); // รวมไฟล์ที่เชื่อมต่อฐานข้อมูล

// วันที่ปัจจุบัน
$currentDate = date('Y-m-d');

// สร้าง SQL Query เพื่อนับจำนวนผู้ป่วยที่มีนัดหมายวันนี้
$sql = "SELECT COUNT(*) AS totalAppointments FROM treatment_information WHERE appointment_date = :appointment_date";
$stmt = $conn->prepare($sql);

// ผูกค่าพารามิเตอร์กับตัวแปร
$stmt->bindParam(':appointment_date', $currentDate, PDO::PARAM_STR);

// เรียกใช้คำสั่ง
$stmt->execute();

// ดึงผลลัพธ์
$row = $stmt->fetch(PDO::FETCH_ASSOC);
$totalAppointments = $row['totalAppointments'] ?? 0;

echo json_encode(['totalAppointments' => $totalAppointments]); // ส่งผลลัพธ์กลับในรูปแบบ JSON

// ปิดการเชื่อมต่อ
$stmt = null;
$conn = null;
?>
