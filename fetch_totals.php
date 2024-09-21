<?php
header('Content-Type: application/json');
include 'db_connection.php'; // รวมไฟล์การเชื่อมต่อฐานข้อมูล

// สร้างการเชื่อมต่อฐานข้อมูล
$conn = OpenCon();

// รับวันที่ปัจจุบัน
$current_date = date('Y-m-d');

// ดึงข้อมูลจำนวนผู้ป่วยที่มีนัดวันนี้
$query_total_appointments = "SELECT COUNT(*) as total FROM patient_infor WHERE appointment_date = '$current_date'";
$result_total_appointments = $conn->query($query_total_appointments);
$row_total_appointments = $result_total_appointments->fetch_assoc();
$total_appointments = $row_total_appointments['total'];

// ดึงข้อมูลจำนวนผู้ป่วยที่รักษาเสร็จสิ้น
$query_total_treated = "SELECT COUNT(*) as total FROM patient_infor WHERE appointment_date = '$current_date' AND treatment_status = 'รักษาเสร็จสิ้น'";
$result_total_treated = $conn->query($query_total_treated);
$row_total_treated = $result_total_treated->fetch_assoc();
$total_treated = $row_total_treated['total'];

// ดึงข้อมูลจำนวนผู้ป่วยที่ยังไม่ได้รักษา
$query_total_untreated = "SELECT COUNT(*) as total FROM patient_infor WHERE appointment_date = '$current_date' AND treatment_status = 'ยังไม่ได้รักษา'";
$result_total_untreated = $conn->query($query_total_untreated);
$row_total_untreated = $result_total_untreated->fetch_assoc();
$total_untreated = $row_total_untreated['total'];

// ส่งข้อมูลกลับเป็น JSON
echo json_encode([
    'totalAppointments' => $total_appointments,
    'totalTreated' => $total_treated,
    'totalUntreated' => $total_untreated,
]);

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
