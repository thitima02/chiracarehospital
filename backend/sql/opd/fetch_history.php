<?php 
// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include('../db_connection.php');

// ดึงข้อมูลประวัติการรักษา
$sql = "SELECT p.full_name, p.patient_image, pm.disease_type , tf.general_symptoms, tf.treatment_issue, tf.next_appointment_date, tf.user_fullname, tf.notes, tf.newupdate
        FROM treatment_form tf 
        JOIN patient_information p ON tf.patient_id = p.patient_id 
        JOIN patient_medical_information pm ON tf.patient_id = pm.patient_id";

$result = $conn->query($sql);

// กำหนดค่าเริ่มต้นให้กับ $historyData
$historyData = []; 

if ($result) {
    // ใช้ rowCount() แทน num_rows
    if ($result->rowCount() > 0) {
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            $historyData[] = $row;
        }
    }
} else {
    die("Query failed: " . $conn->errorInfo()); // แสดงข้อผิดพลาดจาก SQL
}

// ส่งผลลัพธ์กลับในรูปแบบ JSON โดยไม่เข้ารหัส Unicode
echo json_encode($historyData, JSON_UNESCAPED_UNICODE);
?>
