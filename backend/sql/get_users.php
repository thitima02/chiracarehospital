<?php
header('Content-Type: application/json');

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// คำสั่ง SQL เพื่อดึงข้อมูลจากตาราง users
$sql = "SELECT id, username, email, created_at FROM users";
$result = $conn->query($sql);

$users = [];

// ตรวจสอบและดึงข้อมูลจากฐานข้อมูล
if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $users[] = $row;
    }
} else {
    $users = ["message" => "No users found"];
}

// ส่งผลลัพธ์กลับเป็น JSON
echo json_encode($users);

// ปิดการเชื่อมต่อ
$conn->close();
?>
