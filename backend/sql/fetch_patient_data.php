<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_patient_data"; // ชื่อฐานข้อมูล

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูลประเภทผู้ป่วยและจำนวนผู้ป่วย
$sql = "SELECT patient_type, total FROM patients";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// ส่งข้อมูลเป็น JSON
echo json_encode($data);

$conn->close();
?>
