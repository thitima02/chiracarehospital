<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงจำนวนผู้ป่วยที่มีนัดวันนี้
$sql_today_appointments = "SELECT COUNT(*) as total FROM patient_information WHERE appointment_date = CURDATE()";
$result_today = $conn->query($sql_today_appointments);
$row_today = $result_today->fetch_assoc();
$today_appointments = $row_today['total'];

// ดึงจำนวนผู้ป่วยที่รักษาเสร็จสิ้น
$sql_treated_patients = "SELECT COUNT(*) as total FROM patient_information WHERE treatment_status = 'รักษาเสร็จสิ้น'";
$result_treated = $conn->query($sql_treated_patients);
$row_treated = $result_treated->fetch_assoc();
$treated_patients = $row_treated['total'];

// ดึงจำนวนผู้ป่วยที่ยังไม่ได้รักษา
$sql_untreated_patients = "SELECT COUNT(*) as total FROM patient_information WHERE treatment_status = 'ยังไม่ได้รักษา'";
$result_untreated = $conn->query($sql_untreated_patients);
$row_untreated = $result_untreated->fetch_assoc();
$untreated_patients = $row_untreated['total'];

// ส่งผลลัพธ์กลับในรูปแบบ JSON
$data = array(
    'today_appointments' => $today_appointments,
    'treated_patients' => $treated_patients,
    'untreated_patients' => $untreated_patients
);

header('Content-Type: application/json');
echo json_encode($data);

// ปิดการเชื่อมต่อ
$conn->close();
?>
