<?php
// เชื่อมต่อฐานข้อมูล
require 'db_connection.php';

// ตรวจสอบว่ามี patient_id ใน URL หรือไม่
if (!isset($_GET['patient_id'])) {
    echo json_encode(["error" => "patient_id is required"]);
    exit;
}

// รับ patient_id จาก URL
$patient_id = $_GET['patient_id'];

// สร้างคำสั่ง SQL เพื่อดึงข้อมูลจากหลายตาราง
$sql = "
SELECT 
    pi.full_name,
    pi.age,
    pi.current_status,
    pi.disease_type,
    pi.patient_group,
    pi.patient_type,
    pi.id_card,
    pi.birth_date,
    pi.phone_number,
    pi.emergency_phone,
    pa.address,
    pa.additional_info,
    mf.monitor_round,
    mf.monitor_status,
    mi.blood_sugar_level,
    mi.general_symptoms,
    tf.treatment_date,
    ti.treatment_type,
    ti.treatment_status,
    ui.username AS responsible_staff
FROM 
    patient_information pi
LEFT JOIN 
    patient_address pa ON pi.patient_id = pa.patient_id
LEFT JOIN 
    monitor_information mi ON pi.patient_id = mi.patient_id
LEFT JOIN 
    monitor_form mf ON mi.id_monitor_form = mf.id_monitor_form
LEFT JOIN 
    treatment_information ti ON pi.patient_id = ti.patient_id
LEFT JOIN 
    treatment_form tf ON ti.treatment_form_id = tf.treatment_form_id
LEFT JOIN 
    user_info ui ON ti.user_id = ui.user_id
WHERE 
    pi.patient_id = :patient_id
";

$stmt = $conn->prepare($sql);
$stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT); // ใช้ bindValue แทน bind_param
$stmt->execute();
$result = $stmt->fetch(PDO::FETCH_ASSOC);

if ($result) {
    // แปลงข้อมูลเป็น JSON
    echo json_encode($result);
} else {
    echo json_encode(null); // ไม่มีข้อมูล
}

// ปิดการเชื่อมต่อ
$conn = null; // ปิดการเชื่อมต่อ
?>
