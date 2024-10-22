<?php
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include '../db_connection.php';

// ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if ($conn) {
    try {
        // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง patients
        $query = "SELECT COUNT(*) as total FROM patient_information";

        // ใช้ตัวแปร $query ที่ถูกต้อง
        $stmt = $conn->prepare($query);
        $stmt->execute();

        // ตั้งค่าหัวข้อการส่งกลับเป็น JSON
        header('Content-Type: application/json');

        // ดึงข้อมูลทั้งหมดเป็น associative array
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        // ส่งข้อมูลในรูปแบบ JSON
        echo json_encode($result);

    } catch (PDOException $e) {
        // ส่งกลับข้อผิดพลาดถ้ามี
        echo json_encode(['error' => $e->getMessage()]);
    }
} else {
    // กรณีการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    echo json_encode(['error' => 'Failed to connect to the database']);
}
?>