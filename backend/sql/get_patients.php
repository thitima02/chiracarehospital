<?php
// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
include './db_connection.php';

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง patients
$sql = "SELECT id, patient_type, total FROM patients";
$stmt = $conn->prepare($sql);
$stmt->execute();

// ดึงข้อมูลออกมา
$patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ส่งออกข้อมูลเป็น JSON
header('Content-Type: application/json');
echo json_encode($patients);
