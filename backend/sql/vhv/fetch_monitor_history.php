<?php
require_once '../db_connection.php'; // เปลี่ยนเป็นไฟล์เชื่อมต่อของคุณ

header('Content-Type: application/json'); // ตั้งค่าหัวเรื่องให้เป็น JSON

// ดึงข้อมูลจาก monitor_form
$sql = "SELECT mf.patient_id, pi.full_name, pm.disease_type, mf.blood_sugar_level, mf.general_symptoms, mf.vital_signs, mf.reason_for_missed_treatment, mf.form_submission_date, mf.newupdate, mf.id_user_info
        FROM monitor_form mf
        JOIN patient_information pi ON mf.patient_id = pi.patient_id
        JOIN patient_medical_information pm ON mf.patient_id = pm.patient_id"; // ปรับให้เข้ากับโครงสร้างฐานข้อมูลของคุณ

$stmt = $conn->prepare($sql);
$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ส่งข้อมูลในรูปแบบ JSON
echo json_encode(['success' => true, 'data' => $results]);

// ปิดการเชื่อมต่อ
$stmt = null;
$conn = null;
?>
