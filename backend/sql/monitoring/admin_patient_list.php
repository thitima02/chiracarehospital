<?php
// เชื่อมต่อฐานข้อมูล
include_once 'db_connection.php'; // สร้างไฟล์นี้ถ้ายังไม่มี

$sql = "SELECT id_patient_information, full_name, disease_type, patient_type, patient_group, current_status, monitor_round, treatment_status, monitor_status FROM patient_information";
$result = $conn->query($sql);

$patients = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
}

echo json_encode($patients); // ส่งข้อมูลกลับไปในรูปแบบ JSON

$conn->close();
?>
