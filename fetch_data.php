<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";  // ชื่อผู้ใช้ XAMPP โดยปกติคือ root
$password = "";  // ไม่มีรหัสผ่านสำหรับ root ใน XAMPP
$dbname = "chiracare_follow_up_db";  // ชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// คำสั่ง SQL สำหรับดึงข้อมูล
$sql = "SELECT name, full_name, appointment_date, treatment_date, next_appointment_date, treatment_times, followup_status, treatment_status FROM patient_infor";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

header('Content-Type: application/json'); // กำหนด content type เป็น JSON
echo json_encode($data);

// ปิดการเชื่อมต่อ
$conn->close();
?>
