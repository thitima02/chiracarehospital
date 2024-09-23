<?php
// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "username"; // เปลี่ยนเป็นชื่อผู้ใช้ฐานข้อมูลของคุณ
$password = "password"; // เปลี่ยนเป็นรหัสผ่านฐานข้อมูลของคุณ
$dbname = "chiracare_follow_up_db"; // ชื่อฐานข้อมูลของคุณ

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์ม
$name = $_POST['name'] ?? '';
$disease = $_POST['disease'] ?? '';
$symptoms = $_POST['symptoms'] ?? '';
$start_date = $_POST['start-date'] ?? '';
$next_visit_date = $_POST['next-visit-date'] ?? '';

// คำสั่ง SQL สำหรับเพิ่มข้อมูล
$sql = "INSERT INTO appointments (name, disease, symptoms, start_date, next_visit_date) VALUES ('$name', '$disease', '$symptoms', '$start_date', '$next_visit_date')";

if ($conn->query($sql) === TRUE) {
    echo "เพิ่มข้อมูลสำเร็จ";
} else {
    echo "เกิดข้อผิดพลาด: " . $conn->error;
}

$conn->close();
?>
