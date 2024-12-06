<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

// รวมการเชื่อมต่อฐานข้อมูลจากไฟล์ db_connection.php
include('../db_connection.php');

try {
    // SQL query เพื่อดึงข้อมูลจากตารางต่างๆ
    $sql = "
    SELECT 
        p.full_name AS patient_name,
        p.patient_image,
        p.patient_id,
        t.appointment_date,
        pm.patient_type,
        pm.disease_type,
        t.treatment_round,
        t.treatment_status,
        m.monitor_status AS follow_up_status
    FROM 
        treatment_information t
    JOIN 
        patient_information p ON t.patient_id = p.patient_id
    LEFT JOIN 
        patient_medical_information pm ON t.patient_id = pm.patient_id
    LEFT JOIN 
        monitor_information m ON t.patient_id = m.patient_id
    ";

    // Execute the query using PDO
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // ตรวจสอบจำนวนแถวที่คืนกลับจากการค้นหาด้วย rowCount()
    if ($stmt->rowCount() > 0) {
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } else {
        // กรณีไม่มีข้อมูล
        $data = ["message" => "No data found"];
    }

    // แปลงข้อมูลเป็น JSON และส่งกลับไปที่ frontend
    echo json_encode($data);

} catch (PDOException $e) {
    // หากเกิดข้อผิดพลาดในการเชื่อมต่อหรือการ query
    echo json_encode(["error" => "Database error: " . $e->getMessage()]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn = null;
?>
