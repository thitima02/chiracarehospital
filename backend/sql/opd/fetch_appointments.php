<?php
// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'chiracare_follow_up_db'); // ปรับค่าตามฐานข้อมูลของคุณ

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// วันที่ปัจจุบัน
$currentDate = date('Y-m-d');

// สร้าง SQL Query เพื่อนับจำนวนผู้ป่วยที่มีนัดหมายวันนี้
$sql = "SELECT COUNT(*) AS totalAppointments FROM treatment_information WHERE appointment_date = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $currentDate);
$stmt->execute();
$result = $stmt->get_result();

$row = $result->fetch_assoc();
$totalAppointments = $row['totalAppointments'] ?? 0;

echo json_encode(['totalAppointments' => $totalAppointments]); // ส่งผลลัพธ์กลับในรูปแบบ JSON

$stmt->close();
$conn->close();
?>