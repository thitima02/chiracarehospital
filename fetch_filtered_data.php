<?php
// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'chiracare_follow_up_db');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$search = isset($_GET['search']) ? $_GET['search'] : '';
$appointmentDate = isset($_GET['appointment_date']) ? $_GET['appointment_date'] : '';
$treatmentCount = isset($_GET['treatment_count']) ? $_GET['treatment_count'] : 'ทั้งหมด';
$followUpStatus = isset($_GET['follow_up_status']) ? $_GET['follow_up_status'] : 'ทั้งหมด';
$treatmentStatus = isset($_GET['treatment_status']) ? $_GET['treatment_status'] : 'ทั้งหมด';

// สร้าง SQL Query ตามเงื่อนไขที่เลือก
$sql = "SELECT p.full_name, t.appointment_date, t.date_of_treatment, t.next_appointment_date, t.treatment_times, m.follow_up_status, t.treatment_status
        FROM patient_information p
        JOIN treatment_form t ON p.id_patient_information = t.id_patient_information
        JOIN monitor_information m ON t.id_treatment_form = m.id_treatment_form
        WHERE 1=1";

// ตรวจสอบและปรับเงื่อนไข
if (!empty($search)) {
    $sql .= " AND p.full_name LIKE '%" . $conn->real_escape_string($search) . "%'";
}
if (!empty($appointmentDate)) {
    $sql .= " AND t.appointment_date = '" . $conn->real_escape_string($appointmentDate) . "'";
}
if ($treatmentCount !== 'ทั้งหมด') {
    $sql .= " AND t.treatment_times = " . intval($treatmentCount);
}
if ($followUpStatus !== 'ทั้งหมด') {
    $sql .= " AND m.follow_up_status = '" . $conn->real_escape_string($followUpStatus) . "'";
}
if ($treatmentStatus !== 'ทั้งหมด') {
    $sql .= " AND t.treatment_status = '" . $conn->real_escape_string($treatmentStatus) . "'";
}

// แสดง SQL Query สำหรับการตรวจสอบ
echo $sql; // แสดงคำสั่ง SQL

$result = $conn->query($sql);

if (!$result) {
    die("Query failed: " . $conn->error); // แสดงข้อผิดพลาดจาก SQL
}

// ใช้ fetch_assoc() ถ้าคำสั่ง SQL สำเร็จ
$data = [];
while ($row = $result->fetch_assoc()) {
    $data[] = $row;
}

echo json_encode($data); // ส่งผลลัพธ์กลับในรูปแบบ JSON

$conn->close();
