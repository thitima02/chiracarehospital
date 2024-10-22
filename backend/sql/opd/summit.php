<?php
// Database connection
$host = "localhost";
$db = "chiracare_follow_up_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจากฟอร์ม
$name = $_POST['name'];
$disease = $_POST['disease'];
$symptoms = $_POST['symptoms'];
$startDate = $_POST['start-date'];
$nextVisitDate = $_POST['next-visit-date'];

// สร้างคำสั่ง SQL เพื่อบันทึกข้อมูล
$sql = "INSERT INTO appointments (name, disease, symptoms, start_date, next_visit_date) VALUES ('$name', '$disease', '$symptoms', '$startDate', '$nextVisitDate')";

if ($conn->query($sql) === TRUE) {
    echo "บันทึกข้อมูลสำเร็จ!";
} else {
    echo "เกิดข้อผิดพลาด: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
