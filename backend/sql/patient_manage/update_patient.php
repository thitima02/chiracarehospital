<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$host = "localhost";
$dbname = "chiracare_follow_up_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // รับข้อมูล JSON ที่ส่งมาจาก client
    $data = json_decode(file_get_contents('php://input'), true);

    // ตรวจสอบว่าข้อมูลถูกต้องหรือไม่
    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    // ตรวจสอบค่าของข้อมูลต่างๆ
    $patient_id = !empty($data['patientId']) ? $data['patientId'] : null;  // ต้องมี patient_id เพื่อระบุว่าเป็นผู้ป่วยคนไหน
    if (!$patient_id) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
        exit;
    }

    $full_name = !empty($data['name']) ? $data['name'] : '-';
    $id_card = !empty($data['idNumber']) ? $data['idNumber'] : '-';
    $rank = !empty($data['rank']) ? $data['rank'] : '-';
    $department = !empty($data['department']) ? $data['department'] : '-';
    $phone_number = !empty($data['phone']) ? $data['phone'] : '-';
    $emergency_phone = !empty($data['emergencyPhone']) ? $data['emergencyPhone'] : '-';
    $birth_date = !empty($data['birthdate']) ? $data['birthdate'] : '-';
    $current_status = !empty($data['currentStatus']) ? $data['currentStatus'] : '-';
    $marital_status = !empty($data['maritalStatus']) ? $data['maritalStatus'] : '-';

    // ข้อมูลจากตาราง patient_medical_information
    $diseaseType = isset($data['diseaseType']) ? implode(",", $data['diseaseType']) : '-';
    $other_disease = !empty($data['otherDisease']) ? $data['otherDisease'] : '-';
    $note = !empty($data['note']) ? $data['note'] : '-';
    $patient_group = !empty($data['patientGroup']) ? $data['patientGroup'] : '-';
    $patient_type = !empty($data['patientType']) ? $data['patientType'] : '-';

    // ข้อมูลจากตาราง patient_address
    $postal_code = !empty($data['postalCode']) ? $data['postalCode'] : '-';
    $province = !empty($data['province']) ? $data['province'] : '-';
    $amphur = !empty($data['amphur']) ? $data['amphur'] : '-';
    $tambon = !empty($data['tambon']) ? $data['tambon'] : '-';
    $soi = !empty($data['soi']) ? $data['soi'] : '-';
    $moo = !empty($data['moo']) ? $data['moo'] : '-';
    $number = !empty($data['number']) ? $data['number'] : '-';
    $area = !empty($data['area']) ? $data['area'] : '-';

    // รับข้อมูลรูปภาพจาก Base64
    $patientImageBase64 = isset($data['patientImageBase64']) ? $data['patientImageBase64'] : null;

    // อัปเดตข้อมูลในตาราง patient_information
    $stmt1 = $pdo->prepare("UPDATE patient_information SET 
                            full_name = :full_name,
                            id_card = :id_card,
                            rank = :rank,
                            department = :department,
                            phone_number = :phone_number,
                            emergency_phone = :emergency_phone,
                            birth_date = :birth_date,
                            current_status = :current_status,
                            marital_status = :marital_status,
                            patient_image = :patient_image
                            WHERE patient_id = :patient_id");
    $stmt1->bindParam(':patient_id', $patient_id);
    $stmt1->bindParam(':full_name', $full_name);
    $stmt1->bindParam(':id_card', $id_card);
    $stmt1->bindParam(':rank', $rank);
    $stmt1->bindParam(':department', $department);
    $stmt1->bindParam(':phone_number', $phone_number);
    $stmt1->bindParam(':emergency_phone', $emergency_phone);
    $stmt1->bindParam(':birth_date', $birth_date);
    $stmt1->bindParam(':current_status', $current_status);
    $stmt1->bindParam(':marital_status', $marital_status);
    $stmt1->bindParam(':patient_image', $patientImageBase64);

    // อัปเดตข้อมูลในตาราง patient_address
    $stmt2 = $pdo->prepare("UPDATE patient_address SET
                            postal_code = :postal_code,
                            province = :province,
                            amphur = :amphur,
                            tambon = :tambon,
                            soi = :soi,
                            moo = :moo,
                            number = :number,
                            area = :area
                            WHERE patient_id = :patient_id");
    $stmt2->bindParam(':patient_id', $patient_id);
    $stmt2->bindParam(':postal_code', $postal_code);
    $stmt2->bindParam(':province', $province);
    $stmt2->bindParam(':amphur', $amphur);
    $stmt2->bindParam(':tambon', $tambon);
    $stmt2->bindParam(':soi', $soi);
    $stmt2->bindParam(':moo', $moo);
    $stmt2->bindParam(':number', $number);
    $stmt2->bindParam(':area', $area);

    // อัปเดตข้อมูลในตาราง patient_medical_information
    $stmt3 = $pdo->prepare("UPDATE patient_medical_information SET 
                            disease_type = :disease_type,
                            note = :note,
                            patient_group = :patient_group,
                            patient_type = :patient_type
                            WHERE patient_id = :patient_id");
    $stmt3->bindParam(':patient_id', $patient_id);
    $stmt3->bindParam(':disease_type', $diseaseType);
    $stmt3->bindParam(':note', $note);
    $stmt3->bindParam(':patient_group', $patient_group);
    $stmt3->bindParam(':patient_type', $patient_type);

    // ดำเนินการอัปเดตข้อมูลทั้งหมด
    $pdo->beginTransaction();

    if ($stmt1->execute() && $stmt2->execute() && $stmt3->execute()) {
        $pdo->commit();
        echo json_encode(['success' => true, 'message' => 'Data updated successfully']);
    } else {
        $pdo->rollBack();
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
    }
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>
