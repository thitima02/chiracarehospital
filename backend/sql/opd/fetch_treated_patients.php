<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include('../db_connection.php');

// SQL query to fetch treated patients
$sql = "SELECT 
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
WHERE t.treatment_status = 'มาตามนัด'"; // Treated patients status

// Execute query using PDO
$result = $conn->query($sql);

// ตรวจสอบจำนวนแถวที่คืนกลับจากการค้นหาด้วย rowCount()
if ($result->rowCount() > 0) {
    $patients = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        $patients[] = $row;
    }
    // Return the data as a JSON response
    echo json_encode(['patients' => $patients]); 
} else {
    // Return an error message if no patients found
    echo json_encode(['error' => 'No patients found with the treatment status "มาตามนัด".', 'sql' => $sql]); 
}

// ปิดการเชื่อมต่อ
$conn = null;
?>
