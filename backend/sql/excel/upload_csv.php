<?php

header('Content-Type: application/json');

require_once '../db_connection.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบการเชื่อมต่อ
if (!$conn) {
    echo json_encode(["error" => "การเชื่อมต่อฐานข้อมูลล้มเหลว"]);
    exit;
}

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $fileName = $_FILES['csvFile']['name'];
    $fileTmpName = $_FILES['csvFile']['tmp_name'];
    $fileType = $_FILES['csvFile']['type'];

    // ตรวจสอบว่าเป็นไฟล์ CSV หรือไม่
    if ($fileType == 'text/csv' || $fileType == 'application/vnd.ms-excel') {
        if (($handle = fopen($fileTmpName, 'r')) !== false) {
            // ข้ามหัวตาราง
            $headers = fgetcsv($handle, 1000, ',');

            // อ่านข้อมูลจากไฟล์ CSV
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // ข้อมูลที่ได้จากไฟล์ CSV
                $patient_id = $data[0]; // เพิ่ม patient_id
                $rank = $data[1];
                $department = $data[2];
                $full_name = $data[3];
                $id_card = $data[4];
                $birth_date_original = $data[5]; // วันที่จาก CSV เช่น "29/4/2016"
                $date_obj = DateTime::createFromFormat('d/m/Y', $birth_date_original);
                $birth_date = $date_obj ? $date_obj->format('Y-m-d') : null;
                
                // ตรวจสอบว่า `$birth_date` มีค่าหรือไม่
                if (!$birth_date) {
                    echo json_encode(["error" => "รูปแบบวันที่ไม่ถูกต้องสำหรับ patient_id: $patient_id"]);
                    exit;
                }                
                $marital_status = $data[6];
                $phone_number = $data[7];
                $emergency_phone = $data[8];
                $postal_code = $data[9];
                $province = $data[10];
                $amphur = $data[11];
                $tambon = $data[12];
                $soi = $data[13];
                $moo = $data[14];
                $number = $data[15];
                $area = $data[16];
                $patient_group = $data[17];
                $patient_type = $data[18];
                $disease_type = $data[19];
                $current_status = $data[20];
                $note = $data[21];

                // ใช้คำสั่ง SQL แบบ prepare เพื่อป้องกัน SQL injection
                $sql_patient_information = "INSERT INTO patient_information (patient_id, rank, department, full_name, id_card, birth_date, marital_status, phone_number, emergency_phone, current_status)
                                             VALUES (:patient_id, :rank, :department, :full_name, :id_card, :birth_date, :marital_status, :phone_number, :emergency_phone, :current_status)";
                $stmt = $conn->prepare($sql_patient_information);
                $stmt->execute([
                    ':patient_id' => $patient_id,
                    ':rank' => $rank,
                    ':department' => $department,
                    ':full_name' => $full_name,
                    ':id_card' => $id_card,
                    ':birth_date' => $birth_date,
                    ':marital_status' => $marital_status,
                    ':phone_number' => $phone_number,
                    ':emergency_phone' => $emergency_phone,
                    ':current_status' => $current_status
                ]);

                // SQL สำหรับแทรกข้อมูลลงในตาราง patient_address
                $sql_patient_address = "INSERT INTO patient_address (patient_id, postal_code, province, amphur, tambon, soi, moo, number, area)
                                        VALUES (:patient_id, :postal_code, :province, :amphur, :tambon, :soi, :moo, :number, :area)";
                $stmt = $conn->prepare($sql_patient_address);
                $stmt->execute([
                    ':patient_id' => $patient_id,
                    ':postal_code' => $postal_code,
                    ':province' => $province,
                    ':amphur' => $amphur,
                    ':tambon' => $tambon,
                    ':soi' => $soi,
                    ':moo' => $moo,
                    ':number' => $number,
                    ':area' => $area
                ]);

                // SQL สำหรับแทรกข้อมูลลงในตาราง patient_medical_information
                $sql_patient_medical_information = "INSERT INTO patient_medical_information (patient_id, patient_group, patient_type, disease_type, note)
                                                    VALUES (:patient_id, :patient_group, :patient_type, :disease_type, :note)";
                $stmt = $conn->prepare($sql_patient_medical_information);
                $stmt->execute([
                    ':patient_id' => $patient_id,
                    ':patient_group' => $patient_group,
                    ':patient_type' => $patient_type,
                    ':disease_type' => $disease_type,
                    ':note' => $note
                ]);

                // SQL สำหรับแทรกข้อมูลลงในตาราง monitor_information
                $sql_monitor_information = "INSERT INTO monitor_information (patient_id, monitor_round) VALUES (:patient_id, 0)";
                $stmt = $conn->prepare($sql_monitor_information);
                $stmt->execute([':patient_id' => $patient_id]);

                // SQL สำหรับแทรกข้อมูลลงในตาราง treatment_information
                $sql_treatment_information = "INSERT INTO treatment_information (patient_id, treatment_round) VALUES (:patient_id, 0)";
                $stmt = $conn->prepare($sql_treatment_information);
                $stmt->execute([':patient_id' => $patient_id]);
            }

            fclose($handle);
            echo json_encode(["status" => "success", "message" => "ข้อมูลทั้งหมดได้ถูกอัปโหลดและบันทึกเรียบร้อยแล้ว"]);
        }
    } else {
        echo json_encode(["error" => "กรุณาอัปโหลดไฟล์ CSV เท่านั้น"]);
    }
} else {
    echo json_encode(["error" => "กรุณาเลือกไฟล์ CSV ที่ต้องการอัปโหลด"]);
}
?>