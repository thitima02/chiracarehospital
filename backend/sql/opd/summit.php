<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$data = json_decode(file_get_contents('php://input'), true);

if (is_null($data)) {
    echo json_encode(["error" => "ไม่สามารถอ่านข้อมูลจาก JSON ได้"]);
    exit;
}

$patient_id = isset($data['patient_id']) ? $data['patient_id'] : '';
$general_symptoms = isset($data['general_symptoms']) ? $data['general_symptoms'] : '';
$treatment_issue = isset($data['treatment_issue']) ? $data['treatment_issue'] : '';
$next_appointment_date = isset($data['next_appointment_date']) ? $data['next_appointment_date'] : '';
$notes = isset($data['notes']) ? $data['notes'] : '';
$user_fullname = isset($data['user_fullname']) ? $data['user_fullname'] : '';  // รับข้อมูลชื่อผู้ใช้

if ($patient_id == '' || $general_symptoms == '' || $treatment_issue == '' || $next_appointment_date == '') {
    echo json_encode(["error" => "กรุณากรอกข้อมูลให้ครบถ้วน"]);
    exit;
}

$sql = "INSERT INTO treatment_form (patient_id, general_symptoms, treatment_issue, next_appointment_date, notes, user_fullname, newupdate)
        VALUES ('$patient_id', '$general_symptoms', '$treatment_issue', '$next_appointment_date', '$notes', '$user_fullname', NOW())";

if ($conn->query($sql) === TRUE) {
    // อัปเดตข้อมูลใน treatment_information
    $updateStatusSql = "UPDATE treatment_information 
                        SET appointment_date = '$next_appointment_date', 
                            last_update = NOW(), 
                            treatment_status = 'มาตามนัด', 
                            treatment_round = treatment_round + 1 
                        WHERE patient_id = '$patient_id'";

    if ($conn->query($updateStatusSql) === TRUE) {
        echo json_encode(["success" => "บันทึกข้อมูลสำเร็จและอัพเดทสถานะการรักษา"]);
    } else {
        echo json_encode(["error" => "ไม่สามารถอัพเดทสถานะการรักษา: " . $conn->error]);
    }
} else {
    echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $conn->error]);
}

$conn->close();
?>
