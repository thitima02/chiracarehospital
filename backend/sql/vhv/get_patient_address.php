<?php
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include '../db_connection.php';


// ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if ($conn) {
    try {
        // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง patient_address
        $sql = "SELECT p.full_name, a.province, a.amphur, a.tambon, a.number
        FROM patient_information p
        JOIN patient_address a ON p.id_patient_address = a.id";
       
        $stmt = $conn->prepare($sql);
        $stmt->execute();

        // ตั้งค่าหัวข้อการส่งกลับเป็น JSON
        header('Content-Type: application/json');

        // ดึงข้อมูลทั้งหมดเป็นแบบ associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

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
