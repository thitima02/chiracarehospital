<?php
// เรียกใช้ไฟล์เชื่อมต่อฐานข้อมูล
include('db_connection.php');

// กำหนดคำสั่ง SQL เพื่อดึงข้อมูลเฉพาะคอลัมน์ที่ต้องการ
$sql = "SELECT patient_id, postal_code, province, amphur, tambon, soi, moo, number, area FROM patient_address";

try {
    // ใช้คำสั่ง prepare และ execute กับ PDO
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // ดึงข้อมูลทั้งหมดจากผลลัพธ์
    $patient_addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($patient_addresses) {
        // แปลงข้อมูลเป็น JSON และแสดงผล
        echo json_encode($patient_addresses, JSON_PRETTY_PRINT);
    } else {
        // ถ้าไม่พบข้อมูล
        echo json_encode(array("message" => "No data found"));
    }
} catch (PDOException $e) {
    // ถ้ามีข้อผิดพลาดในการเชื่อมต่อหรือดำเนินการ
    echo json_encode(array("error" => $e->getMessage()));
}

// ปิดการเชื่อมต่อ
$conn = null;
?>
