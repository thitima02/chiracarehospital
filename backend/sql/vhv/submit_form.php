<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db_connection.php'; // เชื่อมต่อฐานข้อมูล

try {
    // Check if the connection is successful
    if (!$conn) {
        echo json_encode(["error" => "การเชื่อมต่อฐานข้อมูลล้มเหลว"]);
        exit;
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        // อ่านข้อมูล JSON ที่ส่งเข้ามา
        $data = json_decode(file_get_contents('php://input'), true);

        // Log the incoming data
        error_log("Incoming data: " . print_r($data, true));

        // ตรวจสอบ JSON ว่าถูกต้องหรือไม่
        if (json_last_error() !== JSON_ERROR_NONE) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูล JSON ไม่ถูกต้อง']);
            exit();
        }

        // Proceed with extracting data and inserting into the database
        $patient_id = $data['patient_id'] ?? null; 
        $general_symptoms = $data['general_symptoms'] ?? null;
        $blood_sugar_level = $data['blood_sugar_level'] ?? null;
        $vital_signs = $data['vital_signs'] ?? null;
        $reason_for_missed_treatment = $data['reason_for_missed_treatment'] ?? null;
        $user_fullname = $data['user_fullname'] ?? null; // Retrieve user_fullname
        $form_submission_date = date('Y-m-d'); // คุณไม่ต้องส่ง form_submission_date เนื่องจากให้เซิร์ฟเวอร์ตั้งค่าเอง

        // ตรวจสอบค่าที่จำเป็น
        if (empty($patient_id) || empty($general_symptoms) || empty($blood_sugar_level) || empty($vital_signs) || empty($reason_for_missed_treatment) || empty($user_fullname)) {
            echo json_encode(['success' => false, 'message' => 'ข้อมูลไม่ครบถ้วน']);
            exit();
        }        

        // เพิ่มข้อมูลใน monitor_form
        $sqlInsert = "INSERT INTO monitor_form (patient_id, general_symptoms, blood_sugar_level, vital_signs, reason_for_missed_treatment, user_fullname, form_submission_date) 
                      VALUES (:patient_id, :general_symptoms, :blood_sugar_level, :vital_signs, :reason_for_missed_treatment, :user_fullname, :form_submission_date)";
        $stmtInsert = $conn->prepare($sqlInsert);
        try {
            $stmtInsert->execute([
                ':patient_id' => $patient_id,
                ':general_symptoms' => $general_symptoms,
                ':blood_sugar_level' => $blood_sugar_level,
                ':vital_signs' => $vital_signs,
                ':reason_for_missed_treatment' => $reason_for_missed_treatment,
                ':user_fullname' => $user_fullname, // เพิ่ม user_fullname
                ':form_submission_date' => $form_submission_date,
            ]);
        } catch (PDOException $e) {
            error_log("Insert Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'การบันทึกข้อมูลล้มเหลว: ' . $e->getMessage()]);
            exit();
        }

        // อัปเดตสถานะใน monitor_information
        $sqlUpdate = "UPDATE monitor_information SET monitor_status = 'ติดตามแล้ว' WHERE patient_id = :patient_id";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        try {
            $stmtUpdate->execute([ ':patient_id' => $patient_id ]);
        } catch (PDOException $e) {
            error_log("Update Error: " . $e->getMessage());
            echo json_encode(['success' => false, 'message' => 'การอัปเดตสถานะล้มเหลว: ' . $e->getMessage()]);
            exit();
        }

        // ส่ง response สำเร็จ
        echo json_encode(['success' => true]);
        exit();
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'error' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
    exit();
}
?>