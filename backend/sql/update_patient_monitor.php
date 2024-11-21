<?php
    header('Content-Type: application/json');
    include_once 'db_connection.php'; // เชื่อมต่อฐานข้อมูล

    $input = json_decode(file_get_contents('php://input'), true);

    if (isset($input['patient_id']) && isset($input['reason']) && isset($input['occupation']) && isset($input['blood_level']) && isset($input['symptoms'])) {
        $patient_id = $input['patient_id'];
        $reason = $input['reason'];
        $occupation = $input['occupation'];
        $blood_level = $input['blood_level'];
        $symptoms = $input['symptoms'];

        $sql = "UPDATE monitor_data 
                SET reason_for_missed_treatment = ?, 
                    vital_signs = ?, 
                    blood_sugar_level = ?, 
                    general_symptoms = ? 
                WHERE patient_id = ?";
        
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $reason, $occupation, $blood_level, $symptoms, $patient_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success', 'message' => 'ข้อมูลถูกอัปเดตเรียบร้อยแล้ว']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ครบถ้วน']);
    }
?>
