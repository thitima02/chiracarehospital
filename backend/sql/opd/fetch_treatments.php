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
        t.appointment_date, 
        tf.date_of_treatment AS treatment_date,  
        tf.next_appointment_date,  
        t.treatment_round, 
        m.monitor_status AS follow_up_status, 
        t.treatment_status
    FROM 
        treatment_information t
    JOIN 
        patient_information p ON t.id_patient_information = p.id  -- แก้ไขตามชื่อคอลัมน์ที่ถูกต้อง
    JOIN 
        treatment_form tf ON t.id_treatment_form = tf.id
    JOIN 
        monitor_information m ON t.id_monitor_information = m.id
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