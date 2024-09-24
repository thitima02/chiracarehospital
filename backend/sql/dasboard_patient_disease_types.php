<?php
// เรียกไฟล์เชื่อมต่อฐานข้อมูล
include 'db_connect.php';

// สร้าง SQL query เพื่อดึงข้อมูลผู้ป่วย
$sql = "SELECT patient_type, COUNT(*) as total FROM patients GROUP BY patient_type";
$result = $conn->query($sql);

$patients = array();

// ตรวจสอบผลลัพธ์และเก็บข้อมูลในรูปแบบ JSON
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

// ส่งข้อมูลเป็น JSON กลับไป
echo json_encode($patients);

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
