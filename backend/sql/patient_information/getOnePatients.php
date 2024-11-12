<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$response = [];

// รับค่าจาก URL ที่ใช้เป็น patient_id
$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null;

if ($patient_id === null) {
    // ถ้าไม่มี patient_id ใน URL
    $response = [
        'status' => 'error',
        'message' => 'ไม่พบ patient_id'
    ];
} else {
    try {
        // SQL สำหรับค้นหาผู้ป่วยโดยใช้ patient_id
        $stmt = $conn->prepare("SELECT id, patient_id, full_name, birth_date, id_card, phone_number, emergency_phone FROM patient_information WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id, PDO::PARAM_INT);
        $stmt->execute();

        $patient = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($patient) {
            $response = [
                'status' => 'success',
                'message' => 'ดึงข้อมูลสำเร็จ',
                'data' => $patient
            ];
        } else {
            $response = [
                'status' => 'error',
                'message' => 'ไม่พบข้อมูลผู้ป่วย'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
}

// ส่งผลลัพธ์กลับในรูปแบบ JSON
echo json_encode($response);
?>
