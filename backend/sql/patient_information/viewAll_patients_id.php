<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response
$user_id = $_GET['user_id'];

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลที่ต้องการจากตาราง patient_information
    $stmt = $conn->prepare("
    SELECT 
        pi.id, 
        pi.patient_id, 
        pi.full_name, 
        pi.birth_date, 
        pi.id_card, 
        pi.phone_number, 
        pi.emergency_phone, 
        pi.current_status,
        pa.Area,
        pm.patient_type,
        pm.patient_group,
        pm.disease_type
    FROM 
        patient_information pi
    JOIN 
        patient_address pa ON pi.patient_id = pa.patient_id
    JOIN
        patient_medical_information pm ON pi.patient_id = pm.patient_id
    
    JOIN
    assign_patients_to_vhv ap ON pi.patient_id = ap.patient_id
    WHERE ap.user_id = {$user_id}
    ");

    $stmt->execute();

    // ดึงข้อมูลทั้งหมด
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    if ($patients) {
        // ถ้าพบข้อมูลผู้ป่วย
        $response = [
            'status' => 'success',
            'message' => 'ดึงข้อมูลสำเร็จ',
            'data' => $patients
        ];
    } else {
        // ถ้าไม่พบข้อมูลผู้ป่วย
        $response = [
            'status' => 'error',
            'message' => 'ไม่พบข้อมูลผู้ป่วย'
        ];
    }
} catch (PDOException $e) {
    // กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
    $response = [
        'status' => 'error',
        'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
    ];
}

// ส่ง response กลับเป็น JSON
echo json_encode($response);
?>
