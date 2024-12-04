<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

try {
    if (isset($_GET['patient_id'])) {
        // กรณีที่ได้รับ patient_id
        $patient_id = $_GET['patient_id'];
        
        // คำสั่ง SQL สำหรับดึงข้อมูลเฉพาะ patient_id ที่ระบุ
        $stmt = $conn->prepare("
            SELECT 
                patient_id, full_name, birth_date, id_card, phone_number, 
                emergency_phone, current_status, rank, department, 
                patient_image, id_patient_address, id_patient_medical_information
            FROM patient_information 
            WHERE patient_id = :patient_id
        ");
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();

        // ดึงข้อมูลผู้ป่วยตาม patient_id
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
    } else {
        // กรณีที่ไม่ได้รับ patient_id (ดึงข้อมูลผู้ป่วยทั้งหมด)
        $stmt = $conn->prepare("
            SELECT 
                patient_id, full_name, birth_date, id_card, phone_number, 
                emergency_phone, current_status, rank, department, 
                patient_image, id_patient_address, id_patient_medical_information
            FROM patient_information
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