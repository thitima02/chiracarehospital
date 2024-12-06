<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// รวมการเชื่อมต่อฐานข้อมูลจากไฟล์ db_connection.php
include('../db_connection.php');

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

try {
    // เริ่มต้นการเชื่อมต่อด้วย PDO
    $conn->beginTransaction();

    // สร้างคำสั่ง SQL สำหรับการเพิ่มข้อมูลใน treatment_form
    $sql = "INSERT INTO treatment_form (patient_id, general_symptoms, treatment_issue, next_appointment_date, notes, user_fullname, newupdate)
            VALUES (:patient_id, :general_symptoms, :treatment_issue, :next_appointment_date, :notes, :user_fullname, NOW())";

    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':patient_id', $patient_id);
    $stmt->bindParam(':general_symptoms', $general_symptoms);
    $stmt->bindParam(':treatment_issue', $treatment_issue);
    $stmt->bindParam(':next_appointment_date', $next_appointment_date);
    $stmt->bindParam(':notes', $notes);
    $stmt->bindParam(':user_fullname', $user_fullname);

    // รันคำสั่ง SQL
    $stmt->execute();

    // อัปเดตข้อมูลใน treatment_information
    $updateStatusSql = "UPDATE treatment_information 
                        SET appointment_date = :next_appointment_date, 
                            last_update = NOW(), 
                            treatment_status = 'มาตามนัด', 
                            treatment_round = treatment_round + 1 
                        WHERE patient_id = :patient_id";

    $updateStmt = $conn->prepare($updateStatusSql);
    $updateStmt->bindParam(':patient_id', $patient_id);
    $updateStmt->bindParam(':next_appointment_date', $next_appointment_date);

    // รันคำสั่ง SQL อัปเดตสถานะการรักษา
    $updateStmt->execute();

    // ถ้าทุกคำสั่งสำเร็จ ให้ commit การทำงาน
    $conn->commit();
    echo json_encode(["success" => "บันทึกข้อมูลสำเร็จและอัพเดทสถานะการรักษา"]);

} catch (PDOException $e) {
    // หากเกิดข้อผิดพลาด ให้ยกเลิกการทำงานทั้งหมด
    $conn->rollBack();
    echo json_encode(["error" => "เกิดข้อผิดพลาดในการบันทึกข้อมูล: " . $e->getMessage()]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn = null;
?>
