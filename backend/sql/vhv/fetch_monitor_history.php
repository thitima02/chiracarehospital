<?php
require_once '../db_connection.php'; // เปลี่ยนเป็นไฟล์เชื่อมต่อของคุณ

session_start();  // เริ่มต้นการใช้งาน session

// ตรวจสอบว่า user_id อยู่ใน session หรือไม่
if (!isset($_SESSION['user_id'])) {
    // ถ้าไม่มีการตั้งค่า session ให้ส่ง error กลับ
    echo json_encode(['success' => false, 'error' => 'User not logged in']);
    exit;
}

header('Content-Type: application/json'); // ตั้งค่าหัวเรื่องให้เป็น JSON

// ดึงข้อมูลจาก monitor_form และ filter ด้วย assign_patients_to_vhv ตาม user_id
$sql = "
  SELECT 
    mf.patient_id, 
    pi.full_name, 
    pm.disease_type, 
    mf.blood_sugar_level, 
    mf.general_symptoms, 
    mf.vital_signs, 
    mf.reason_for_missed_treatment, 
    mf.form_submission_date, 
    mf.newupdate,
    mf.user_fullname, 
    mf.id_user_info
  FROM monitor_form mf
  JOIN patient_information pi ON mf.patient_id = pi.patient_id
  JOIN patient_medical_information pm ON mf.patient_id = pm.patient_id
  JOIN assign_patients_to_vhv apv ON mf.patient_id = apv.patient_id
  WHERE apv.user_id = :user_id"; // ใช้ user_id จาก session

$stmt = $conn->prepare($sql);

// รับค่า user_id จาก session
$user_id = $_SESSION['user_id'];  // ค่าของ user_id จาก session

// Bind ค่า user_id ไปยัง query
$stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

$stmt->execute();

$results = $stmt->fetchAll(PDO::FETCH_ASSOC);

// ส่งข้อมูลในรูปแบบ JSON
echo json_encode(['success' => true, 'data' => $results]);

// ปิดการเชื่อมต่อ
$stmt = null;
$conn = null;
?>
