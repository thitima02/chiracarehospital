<?php
require_once '../db_connection.php'; // เปลี่ยนเป็นไฟล์เชื่อมต่อของคุณ

header('Content-Type: application/json'); // ตั้งค่าหัวเรื่องให้เป็น JSON

// รับข้อมูลจากฟอร์ม
$data = json_decode(file_get_contents("php://input"), true);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($data === null) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON input.']);
    exit;
}

// ตรวจสอบว่ามีข้อมูลที่จำเป็นหรือไม่
if (!isset($data['patient_id'], $data['general_symptoms'], $data['blood_sugar_level'], $data['vital_signs'], $data['reason_for_missed_treatment'], $data['form_submission_date'])) {
    echo json_encode(['success' => false, 'error' => 'Missing required fields.']);
    exit;
}

// รับค่าจากข้อมูล
$patientId = $data['patient_id'];
$generalSymptoms = $data['general_symptoms'];
$bloodSugarLevel = $data['blood_sugar_level'];
$vitalSigns = $data['vital_signs'];
$reasonForMissedTreatment = $data['reason_for_missed_treatment'];
$formSubmissionDate = $data['form_submission_date'];
$userId = 1; // เปลี่ยนให้เป็น ID ของผู้ใช้ที่ส่งข้อมูล

try {
    // เริ่มต้นการทำงานกับฐานข้อมูล
    $conn->beginTransaction();

    // คำสั่ง SQL สำหรับบันทึกข้อมูลใหม่ใน monitor_form
    $insertSql = "INSERT INTO monitor_form (patient_id, general_symptoms, blood_sugar_level, vital_signs, reason_for_missed_treatment, form_submission_date) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($insertSql);
    $stmt->execute([$patientId, $generalSymptoms, $bloodSugarLevel, $vitalSigns, $reasonForMissedTreatment, $formSubmissionDate]);

    // ดึง ID ล่าสุดที่ถูกสร้างขึ้น
    $monitorFormId = $conn->lastInsertId();

    // ดึงข้อมูลเก่าจาก monitor_information
    $selectOldDataSql = "SELECT id_patient_information, monitor_round FROM monitor_information WHERE patient_id = ? ORDER BY monitor_date DESC LIMIT 1";
    $stmt = $conn->prepare($selectOldDataSql);
    $stmt->execute([$patientId]);

    // ตรวจสอบว่ามีข้อมูลเก่าหรือไม่
    if ($oldData = $stmt->fetch(PDO::FETCH_ASSOC)) {
        // เพิ่มข้อมูลใหม่ใน monitor_information
        $insertStatusSql = "INSERT INTO monitor_information (patient_id, monitor_status, monitor_round, id_patient_information, id_user_info, monitor_date, id_monitor_form) 
                            VALUES (?, 'ติดตามแล้ว', ?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($insertStatusSql);

        // เพิ่มจำนวน monitor_round
        $newMonitorRound = $oldData['monitor_round'] + 1; // เพิ่มจำนวนการติดตาม
        $stmt->execute([$patientId, $newMonitorRound, $oldData['id_patient_information'], $userId, $formSubmissionDate, $monitorFormId]);

        // ตรวจสอบว่าการเพิ่มข้อมูลสำเร็จหรือไม่
        if ($stmt->rowCount() > 0) {
            // ยืนยันการทำธุรกรรม
            $conn->commit();
            echo json_encode(['success' => true]);
        } else {
            $conn->rollBack();
            echo json_encode(['success' => false, 'error' => 'Unable to insert status.']);
        }
    } else {
        $conn->rollBack();
        echo json_encode(['success' => false, 'error' => 'No old data found.']);
    }
} catch (Exception $e) {
    // หากเกิดข้อผิดพลาดให้ทำการ rollback
    $conn->rollBack();
    echo json_encode(['success' => false, 'error' => 'Database error: ' . $e->getMessage()]);
}

// ปิดการเชื่อมต่อ
$stmt = null; // set statement to null to free resources
$conn = null; // close connection
?>
