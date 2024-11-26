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
       ti.treatment_status,
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

        // ตรวจสอบว่า monitor_deadline ไม่เป็น NULL และทำการเปรียบเทียบกับวันที่ปัจจุบัน
        if ($row['monitor_deadline'] !== NULL) {
            $current_date = new DateTime(); // วันเวลาปัจจุบัน
            $deadline_date = new DateTime($row['monitor_deadline']); // วันกำหนดส่งงาน

            // ตรวจสอบว่าเลยกำหนดหรือยัง
            if ($current_date > $deadline_date) {
                // ถ้าเลยกำหนดให้เปลี่ยนสถานะเป็น "ติดตามล้มเหลว"
                $row['monitor_status'] = 'ติดตามล้มเหลว';
            } else {
                // ถ้ายังไม่เลยกำหนดให้เปลี่ยนสถานะเป็น "กำลังติดตาม"
                $row['monitor_status'] = 'กำลังติดตาม';
            }

            // อัพเดตสถานะในฐานข้อมูล
            $update_sql = "UPDATE monitor_information SET monitor_status = ? WHERE patient_id = ?";
            $stmt = $conn->prepare($update_sql);
            $stmt->bind_param("si", $row['monitor_status'], $row['patient_id']);
            $stmt->execute();
        }

        $data[] = $row; // เก็บแต่ละแถวเป็น array
    }
} else {
    $data = ["message" => "ไม่มีข้อมูล"];
}

// ส่งข้อมูลเป็น JSON
echo json_encode($data);

$conn->close(); // ปิดการเชื่อมต่อฐานข้อมูล
?>
