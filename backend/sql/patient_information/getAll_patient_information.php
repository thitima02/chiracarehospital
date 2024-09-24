<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลผู้ป่วยทั้งหมดจากตาราง patient_information
    $stmt = $conn->prepare("SELECT id, full_name, id_card, birth_date, phone_number, emergency_phone, rank, department, marital_status, current_status, id_patient_address ,id_patient_medical_information  FROM patient_information");
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
