<?php
header('Content-Type: application/json');

// เชื่อมต่อกับฐานข้อมูล
include 'db_connect.php';

// รับข้อมูลจากการร้องขอ (Request)
$data = json_decode(file_get_contents('php://input'), true);

$username = $data['username'];
$email = $data['email'];
$password = password_hash($data['password'], PASSWORD_DEFAULT); // เข้ารหัสรหัสผ่าน

// ตรวจสอบและเพิ่มผู้ใช้ใหม่
$sql = "INSERT INTO users (username, email, password) VALUES ('$username', '$email', '$password')";

if ($conn->query($sql) === TRUE) {
    echo json_encode(["message" => "User created successfully"]);
} else {
    echo json_encode(["error" => "Error: " . $conn->error]);
}

$conn->close();
?>
