<?php
// นำเข้าไฟล์การเชื่อมต่อฐานข้อมูล
require 'db_connection.php';

try {
    // ตรวจสอบว่ามี patient_id ถูกส่งมาหรือไม่
    if (isset($_GET['patient_id'])) {
        $patient_id = $_GET['patient_id'];
        $stmt = $conn->prepare("SELECT id, patient_id, patient_group, patient_type, disease_type, date_create 
                                 FROM patient_medical_information WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id);
    } else {
        // ถ้าไม่มี patient_id ถูกส่งมา ให้ดึงข้อมูลทั้งหมด
        $stmt = $conn->prepare("SELECT id, patient_id, patient_group, patient_type, disease_type, date_create 
                                FROM patient_medical_information");
    }

    // ดำเนินการคำสั่ง SQL
    $stmt->execute();
    $patientData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ตั้งค่า header เป็น application/json
    header('Content-Type: application/json');

    // แสดงผลข้อมูลผู้ป่วยในรูปแบบ JSON
    echo json_encode(['patient_data' => $patientData]);

} catch (PDOException $e) {
    // แสดงข้อความข้อผิดพลาดในรูปแบบ JSON
    echo json_encode(["error" => $e->getMessage()]);
}

// ปิดการเชื่อมต่อ
$conn = null;
?>
