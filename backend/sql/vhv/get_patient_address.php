<?php 
include '../db_connection.php'; // รวมไฟล์การเชื่อมต่อฐานข้อมูล

// ตรวจสอบว่าเชื่อมต่อสำเร็จหรือไม่
if ($conn) {
    try {
        // รับค่า patient_id จาก GET หรือ POST
        $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

        // ตรวจสอบว่า patient_id ถูกระบุหรือไม่
        if ($patient_id) {
            // ดึงข้อมูลเฉพาะ patient_id
            $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                           a.postal_code, a.province, a.amphur, a.tambon, a.soi, a.moo, a.number, a.area,
                           m.monitor_deadline, m.monitor_status, m.monitor_round
                    FROM patient_information p
                    JOIN patient_address a ON p.patient_id = a.patient_id
                    JOIN patient_medical_information pm ON p.patient_id = pm.patient_id
                    JOIN monitor_information m ON p.patient_id = m.patient_id
                    JOIN assign_patients_to_vhv ap ON p.patient_id = ap.patient_id
                    WHERE p.patient_id = :patient_id";
        } else {
            // ดึงข้อมูลผู้ป่วยทั้งหมดที่มีใน assign_patients_to_vhv
            $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                           a.postal_code, a.province, a.amphur, a.tambon, a.soi, a.moo, a.number, a.area,
                           m.monitor_deadline, m.monitor_status, m.monitor_round
                    FROM patient_information p
                    JOIN patient_address a ON p.patient_id = a.patient_id
                    JOIN patient_medical_information pm ON p.patient_id = pm.patient_id
                    JOIN monitor_information m ON p.patient_id = m.patient_id
                    JOIN assign_patients_to_vhv ap ON p.patient_id = ap.patient_id";
        }

        $stmt = $conn->prepare($sql);

        // ถ้ามีการระบุ patient_id ให้ bind ค่า
        if ($patient_id) {
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        // ตั้งค่าหัวข้อการส่งกลับเป็น JSON
        header('Content-Type: application/json');

        // ดึงข้อมูลทั้งหมดเป็นแบบ associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['message' => 'ไม่พบข้อมูลที่ตรงกับเงื่อนไข']);
        }

    } catch (PDOException $e) {
        // ส่งกลับข้อผิดพลาดถ้ามี
        http_response_code(500);
        echo json_encode(['error' => 'การดึงข้อมูลล้มเหลว: ' . $e->getMessage()]);
    }
} else {
    // กรณีการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    http_response_code(500);
    echo json_encode(['error' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้']);
}
?>
