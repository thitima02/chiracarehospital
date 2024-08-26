<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "population_data";

// เชื่อมต่อฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// ดึงข้อมูลจากฐานข้อมูล
$sql = "SELECT person_type, year_2563, year_2564, year_2565, year_2566, year_2567 FROM citizen_data";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    // แปลงข้อมูลเป็น array
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// ส่งข้อมูลเป็น JSON
echo json_encode($data);

// ปิดการเชื่อมต่อ
$conn->close();
?>
