<?php
include '../db_connection.php'; // รวมไฟล์การเชื่อมต่อ

// ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if ($conn) {
    try {
        // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากตาราง patient_address
        $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                       a.postal_code,
                       a.province, 
                       a.amphur, 
                       a.tambon, 
                       a.soi, 
                       a.moo, 
                       a.number, 
                       m.monitor_deadline, 
                       m.monitor_status, 
                       m.monitor_round 
                FROM patient_information p
                JOIN patient_address a ON p.id_patient_address = a.id
                JOIN patient_medical_information pm ON p.id_patient_medical_information = pm.id
                JOIN monitor_information m ON p.patient_id = m.patient_id"; // ใช้ id_patient เพื่อเชื่อมต่อกับ monitor_information

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
        echo json_encode(['error' => 'การดึงข้อมูลล้มเหลว: ' . $e->getMessage()]);
    }
} else {
    // กรณีการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    echo json_encode(['error' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้']);
}
?>

