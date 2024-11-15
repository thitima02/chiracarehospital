<?php 
include '../db_connection.php'; // รวมไฟล์การเชื่อมต่อ

// ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if ($conn) {
    try {
        // รับค่า patient_id จาก GET หรือ POST (เปลี่ยนตามที่คุณต้องการ)
        $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

        // ตรวจสอบว่า patient_id มีหรือไม่
        if ($patient_id) {
            // เตรียมคำสั่ง SQL เพื่อดึงข้อมูลจากตารางที่เกี่ยวข้อง
            $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                           a.postal_code,
                           a.province, 
                           a.amphur, 
                           a.tambon, 
                           a.soi, 
                           a.moo, 
                           a.number, 
                           a.area,
                           m.monitor_deadline, 
                           m.monitor_status, 
                           m.monitor_round 
                    FROM patient_information p
                    JOIN patient_address a ON p.patient_id = a.patient_id
                    JOIN patient_medical_information pm ON p.patient_id = pm.patient_id
                    JOIN monitor_information m ON p.patient_id = m.patient_id
                    WHERE p.patient_id = :patient_id"; // เพิ่ม WHERE เพื่อกรองตาม patient_id
        } else {
            // หากไม่ได้ส่ง patient_id, ดึงข้อมูลทั้งหมด
            $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                           a.postal_code,
                           a.province, 
                           a.amphur, 
                           a.tambon, 
                           a.soi, 
                           a.moo, 
                           a.number,
                           a.area, 
                           m.monitor_deadline, 
                           m.monitor_status, 
                           m.monitor_round 
                    FROM patient_information p
                    JOIN patient_address a ON p.id_patient_address = a.id
                    JOIN patient_medical_information pm ON p.id_patient_medical_information = pm.id
                    JOIN monitor_information m ON p.patient_id = m.patient_id"; // ดึงข้อมูลทั้งหมดหากไม่ได้ระบุ patient_id
        }

        $stmt = $conn->prepare($sql);

        // ถ้ามีการระบุ patient_id ให้ bind ค่าของ patient_id
        if ($patient_id) {
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        }

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
