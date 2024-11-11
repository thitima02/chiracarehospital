<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";  // ชื่อโฮสต์ของฐานข้อมูล
$username = "root";         // ชื่อผู้ใช้ฐานข้อมูล
$password = "";             // รหัสผ่านฐานข้อมูล
$dbname = "chiracare_follow_up_db";  // ชื่อฐานข้อมูลที่คุณใช้

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับค่าจากฟอร์มและตรวจสอบว่ามีการส่งค่ามาหรือไม่
$data = json_decode(file_get_contents('php://input'), true);

// ตรวจสอบค่าที่รับมาจาก JSON
if (is_null($data)) {
    echo json_encode(["error" => "ไม่สามารถอ่านข้อมูลจาก JSON ได้"]);
    exit;
}

// รับค่าจาก JSON
$patient_id = isset($data['patient_id']) ? $data['patient_id'] : '';
$general_symptoms = isset($data['general_symptoms']) ? $data['general_symptoms'] : '';
$treatment_issue = isset($data['treatment_issue']) ? $data['treatment_issue'] : '';
$date_of_treatment = isset($data['date_of_treatment']) ? $data['date_of_treatment'] : '';
$next_appointment_date = isset($data['next_appointment_date']) ? $data['next_appointment_date'] : '';
$notes = isset($data['notes']) ? $data['notes'] : '';

// ตรวจสอบว่าค่าทั้งหมดได้รับการส่งมาแล้วหรือไม่
if ($patient_id == '' || $general_symptoms == '' || $treatment_issue == '' || $date_of_treatment == '' || $next_appointment_date == '') {
    echo json_encode(["error" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
    exit;
}

// สร้างคำสั่ง SQL เพื่อบันทึกข้อมูลในตาราง treatment_form
$sql = "INSERT INTO treatment_form (patient_id, general_symptoms, treatment_issue, date_of_treatment, next_appointment_date, notes)
        VALUES ('$patient_id', '$general_symptoms', '$treatment_issue', '$date_of_treatment', '$next_appointment_date', '$notes')";

// ตรวจสอบว่าแทรกข้อมูลสำเร็จหรือไม่
if ($conn->query($sql) === TRUE) {
    // อัพเดทสถานะการรักษาเป็น "นัดติดตามต่อเนื่อง" และเพิ่มค่า treatment_round + 1 ในตาราง treatment_information
    $updateStatusSql = "UPDATE treatment_information 
                        SET treatment_status = 'นัดติดตามต่อเนื่อง', treatment_round = treatment_round + 1 
                        WHERE patient_id = '$patient_id'";

    // ส่งคำสั่งอัพเดทสถานะและเพิ่ม treatment_round
    if ($conn->query($updateStatusSql) === TRUE) {
        echo json_encode(["success" => "บันทึกข้อมูลสำเร็จและอัพเดทสถานะการรักษา"]);
    } else {
        echo json_encode(["error" => "ไม่สามารถอัพเดทสถานะการรักษา: " . $conn->error]);
    }
} else {
    // ถ้าบันทึกไม่สำเร็จ
    echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
