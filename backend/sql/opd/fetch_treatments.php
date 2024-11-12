<?php
// Database connection
$host = "localhost";
$db = "chiracare_follow_up_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query เพื่อดึงข้อมูลจากตารางต่างๆ
$sql = "
SELECT 
    p.full_name AS patient_name,
    p.patient_image,
    t.appointment_date,
    p.patient_id,
    pm.disease_type,
    t.treatment_round,
    t.treatment_status,
    t.date_of_treatment AS treatment_date,  
    t.next_appointment_date,
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

$result = $conn->query($sql);

if (!$result) {
    die("Error in SQL query: " . $conn->error);
}

$data = array();

if ($result->num_rows > 0) {
    // วนลูปเพื่อดึงข้อมูลแต่ละแถว
    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
} else {
    // กรณีไม่มีข้อมูล
    $data = ["message" => "No data found"];
}

// แปลงข้อมูลเป็น JSON และส่งกลับไปที่ frontend
header('Content-Type: application/json');
echo json_encode($data);

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>