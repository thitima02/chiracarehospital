<?php
header('Content-Type: application/json');
require_once '../db_connection.php';

$response = [];

$patient_id = isset($_GET['patient_id']) ? $_GET['patient_id'] : null; // ใช้ patient_id แทน id

if ($patient_id === null) {
    $response = [
        'status' => 'error',
        'message' => 'ไม่พบรหัสผู้ป่วย'
    ];
} else {
    try {
        // SQL สำหรับค้นหาผู้ป่วยโดยใช้ patient_id
        $stmt = $conn->prepare("SELECT id, patient_id, full_name, birth_date, id_card, phone_number, emergency_phone, current_status FROM patient_information WHERE patient_id = :patient_id");
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

echo json_encode($response);
?>
