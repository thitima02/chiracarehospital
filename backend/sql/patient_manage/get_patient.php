<?php
// กำหนดประเภทข้อมูลที่ส่งกลับเป็น JSON
header('Content-Type: application/json');

// นำเข้าการเชื่อมต่อฐานข้อมูล
require_once '../db_connection.php';

// ตรวจสอบว่ามีการส่ง patient_id หรือไม่
if (!isset($_GET['patient_id'])) {
    echo json_encode(['error' => 'กรุณาระบุ patient_id']);
    exit;
}

// รับค่า patient_id จาก URL และแปลงเป็นประเภทข้อมูล integer
$patient_id = intval($_GET['patient_id']);

try {
    // คำสั่ง SQL สำหรับดึงข้อมูล
    $sql = "
        SELECT 
            pi.patient_id,
            pi.rank,
            pi.department,
            pi.full_name,
            pi.patient_image,
            pi.id_card,
            pi.marital_status,
            pi.birth_date,
            pi.current_status,
            pi.phone_number,
            pi.emergency_phone,
            pa.postal_code,
            pa.province,
            pa.amphur,
            pa.tambon,
            pa.soi,
            pa.moo,
            pa.number,
            pa.area,
            pm.patient_group,
            pm.patient_type,
            pm.disease_type,
            pm.note
        FROM 
            patient_information pi
        LEFT JOIN 
            patient_address pa ON pi.patient_id = pa.patient_id
        LEFT JOIN 
            patient_medical_information pm ON pi.patient_id = pm.patient_id
        WHERE 
            pi.patient_id = :patient_id
    ";

    // เตรียมและรันคำสั่ง SQL
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
    $stmt->execute();

    // ตรวจสอบผลลัพธ์
    if ($stmt->rowCount() > 0) {
        // ถ้ามีข้อมูลผู้ป่วย ก็จะส่งกลับข้อมูลในรูปแบบ JSON
        echo json_encode($stmt->fetch(PDO::FETCH_ASSOC));
    } else {
        // ถ้าไม่พบข้อมูลผู้ป่วย จะส่งกลับข้อความว่าไม่พบข้อมูล
        echo json_encode(['error' => 'ไม่พบข้อมูลผู้ป่วย']);
    }
} catch (Exception $e) {
    // ถ้ามีข้อผิดพลาดในกระบวนการดึงข้อมูล จะส่งกลับข้อความข้อผิดพลาด
    echo json_encode(['error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
