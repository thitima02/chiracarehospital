<?php
header('Content-Type: application/json');

// เปิดการแสดงข้อผิดพลาด
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db"; // ชื่อฐานข้อมูลที่ใช้

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
}

// SQL query ดึงข้อมูลจากหลายตาราง
$sql = "SELECT pi.patient_id, pi.full_name, pi.current_status, 
       pm.disease_type, pm.patient_type, pm.patient_group,
       mi.monitor_round, mi.monitor_status, mi.monitor_deadline, mi.monitor_date,
       a.patient_address_area,
       ti.treatment_status, ti.appointment_date,
       ui.full_name AS responsible_person_name
FROM patient_information pi
LEFT JOIN patient_medical_information pm ON pi.patient_id = pm.patient_id
LEFT JOIN monitor_information mi ON pi.patient_id = mi.patient_id
LEFT JOIN treatment_information ti ON pi.patient_id = ti.patient_id
LEFT JOIN assign_patients_to_vhv a ON pi.patient_id = a.patient_id
LEFT JOIN user_info ui ON a.user_id = ui.user_id";

$result = $conn->query($sql);

$data = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        // ตรวจสอบว่า responsible_person_name เป็น NULL หรือไม่
        $row['responsible_person_name'] = $row['responsible_person_name'] ?? 'ไม่มีการกำหนด'; // กำหนดค่า default

        // ตรวจสอบว่า patient_address_area เป็น NULL หรือไม่
        $row['patient_address_area'] = $row['patient_address_area'] ?? 'ไม่มีการกำหนด'; // กำหนดค่า default

        // กำหนดค่า monitor_status ตามเงื่อนไข
        $current_date = new DateTime(); // วันเวลาปัจจุบัน
        $appointment_date = $row['appointment_date'] ? new DateTime($row['appointment_date']) : null;
        $monitor_date = $row['monitor_date'] ? new DateTime($row['monitor_date']) : null;
        $monitor_deadline = $row['monitor_deadline'] ? new DateTime($row['monitor_deadline']) : null;

        // เงื่อนไขที่ 1: ถ้าวันนัดหมายเลยวันไปแล้ว และ monitor_date เป็น null
        if ($appointment_date && $appointment_date < $current_date && !$monitor_date) {
            $row['monitor_status'] = 'ยังไม่ได้ติดตาม';
            // อัพเดตสถานะการรักษาเป็น "ไม่มาตามนัด"
            $update_treatment_status = "UPDATE treatment_information SET treatment_status = 'ไม่มาตามนัด' WHERE patient_id = ?";
            $stmt_update_treatment = $conn->prepare($update_treatment_status);
            $stmt_update_treatment->bind_param("i", $row['patient_id']);
            $stmt_update_treatment->execute();
        }
        // เงื่อนไขที่ 2: ถ้ากำหนดส่งงานเลยวันไปแล้ว และ appointment_date ไม่เป็น null
        elseif ($monitor_deadline < $current_date && $monitor_deadline) {
            $row['monitor_status'] = 'ติดตามล้มเหลว';
        }
        // เงื่อนไขที่ 3: ถ้าวันนัดหมายเป็น null และ monitor_date เป็น null
        elseif (!$appointment_date && !$monitor_date) {
            $row['monitor_status'] = 'ไม่ต้องติดตาม';
        }
        // เงื่อนไขที่ 4: ถ้าวันนัดหมายยังไม่เลยวัน
        elseif ($appointment_date && $appointment_date >= $current_date) {
            $row['monitor_status'] = 'ไม่ต้องติดตาม';
        }

        // อัพเดตสถานะในฐานข้อมูล
        $update_sql = "UPDATE monitor_information SET monitor_status = ? WHERE patient_id = ?";
        $stmt = $conn->prepare($update_sql);
        $stmt->bind_param("si", $row['monitor_status'], $row['patient_id']);
        $stmt->execute();

        $data[] = $row; // เก็บแต่ละแถวเป็น array
    }
} else {
    $data = ["message" => "ไม่มีข้อมูล"];
}

// ส่งข้อมูลเป็น JSON
echo json_encode($data);

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
