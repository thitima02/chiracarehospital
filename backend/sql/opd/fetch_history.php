<?php
// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'chiracare_follow_up_db');

// ตั้งค่าให้ใช้ UTF-8
$conn->set_charset("utf8");

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// ดึงข้อมูลประวัติการรักษา
$sql = "SELECT p.full_name, pm.disease_type , tf.general_symptoms, tf.treatment_issue, tf.date_of_treatment, tf.next_appointment_date, tf.notes, tf.newupdate
        FROM treatment_form tf 
        JOIN patient_information p ON tf.patient_id = p.patient_id 
        JOIN patient_medical_information pm ON tf.patient_id = pm.patient_id";

$result = $conn->query($sql);

// กำหนดค่าเริ่มต้นให้กับ $historyData
$historyData = []; 

if ($result) {
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $historyData[] = $row;
        }
    }
} else {
    die("Query failed: " . $conn->error); // แสดงข้อผิดพลาดจาก SQL
}

// ส่งผลลัพธ์กลับในรูปแบบ JSON โดยไม่เข้ารหัส Unicode
echo json_encode($historyData, JSON_UNESCAPED_UNICODE);

$conn->close();

?>
