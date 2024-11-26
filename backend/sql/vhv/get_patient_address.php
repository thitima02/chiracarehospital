<?php 
header('Content-Type: application/json');
include '../db_connection.php'; // รวมไฟล์การเชื่อมต่อฐานข้อมูล
session_start(); // เริ่มต้น session เพื่อดึงข้อมูล user_id

// ตรวจสอบการเชื่อมต่อฐานข้อมูล
if ($conn) {
    try {
        // ตรวจสอบว่า session มี user_id หรือไม่
        if (!isset($_SESSION['user_id'])) {
            throw new Exception('ไม่พบข้อมูล user_id ใน session');
        }

        // รับ user_id จาก session
        $user_id = $_SESSION['user_id'];

        // รับค่า patient_id ถ้ามี
        $patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

        // SQL พื้นฐานสำหรับดึงข้อมูลผู้ป่วย
        $sql = "SELECT p.full_name, p.patient_image, pm.disease_type, p.patient_id,
                       a.postal_code, a.province, a.amphur, a.tambon, a.soi, a.moo, a.number, a.area,
                       m.monitor_deadline, m.monitor_status, m.monitor_round
                FROM patient_information p
                JOIN patient_address a ON p.patient_id = a.patient_id
                JOIN patient_medical_information pm ON p.patient_id = pm.patient_id
                JOIN monitor_information m ON p.patient_id = m.patient_id
                JOIN assign_patients_to_vhv ap ON p.patient_id = ap.patient_id
                WHERE ap.user_id = :user_id";

        // เพิ่มเงื่อนไขกรองตาม patient_id ถ้ามีการระบุ
        if ($patient_id) {
            $sql .= " AND p.patient_id = :patient_id";
        }

        $stmt = $conn->prepare($sql);

        // Bind ค่า user_id
        $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

        // Bind ค่า patient_id ถ้ามีการระบุ
        if ($patient_id) {
            $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        }

        $stmt->execute();

        // ตั้งค่าหัวข้อการส่งกลับเป็น JSON
        header('Content-Type: application/json');

        // ดึงข้อมูลทั้งหมดเป็น associative array
        $result = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // ตรวจสอบว่ามีข้อมูลหรือไม่
        if ($result) {
            echo json_encode($result);
        } else {
            echo json_encode(['message' => 'ไม่พบข้อมูลที่ตรงกับเงื่อนไข']);
        }

    } catch (Exception $e) {
        // ส่งกลับข้อผิดพลาดถ้ามี
        http_response_code(500);
        echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    }
} else {
    // กรณีการเชื่อมต่อฐานข้อมูลไม่สำเร็จ
    http_response_code(500);
    echo json_encode(['error' => 'ไม่สามารถเชื่อมต่อกับฐานข้อมูลได้']);
}
?>
