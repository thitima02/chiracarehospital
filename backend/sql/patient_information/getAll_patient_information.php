<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

try {
    // คำสั่ง SQL สำหรับดึงข้อมูลที่ต้องการจากตาราง patient_information
    $stmt = $conn->prepare("SELECT id, patient_id, full_name, birth_date, id_card, phone_number, emergency_phone, current_status FROM patient_information");
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
