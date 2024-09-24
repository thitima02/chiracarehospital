<?php
$host = 'localhost';
$dbname = 'chiracare_follow_up_db';
$username = 'root';  // ค่าเริ่มต้นสำหรับ XAMPP
$password = '';      // ค่าเริ่มต้นสำหรับ XAMPP

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($host, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อผิดพลาด: " . $conn->connect_error);
}

// กำหนดค่า query SQL
$sql = "SELECT disease, patient_group, SUM(total_count) as total 
        FROM patient_disease_types 
        GROUP BY disease, patient_group";
        
// รัน query
$result = $conn->query($sql);

// เก็บผลลัพธ์
$patients = [];

// ตรวจสอบหากมีข้อมูล
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// กำหนดค่า header ให้ส่งกลับเป็น JSON
header('Content-Type: application/json');

// ส่งข้อมูลกลับเป็น JSON
echo json_encode($patients);

// ปิดการเชื่อมต่อ
$conn->close();
?>
