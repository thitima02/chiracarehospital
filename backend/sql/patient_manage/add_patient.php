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

    // ลบ Header "data:image/...;base64," ออก หากมี
    if ($patientImageBase64) {
        $patientImageBase64 = preg_replace('/^data:image\/[a-zA-Z]+;base64,/', '', $patientImageBase64);
    }

    // ฟังก์ชันเพื่อสุ่มหมายเลข patient_id ที่ไม่ซ้ำ
    function generateUniquePatientId($pdo) {
        do {
            $patient_id = mt_rand(1000, 9999);
            $sql = "SELECT COUNT(*) FROM patient_information WHERE patient_id = :patient_id";
            $stmt = $pdo->prepare($sql);
            $stmt->execute(['patient_id' => $patient_id]);
            $result = $stmt->fetchColumn();
        } while ($result > 0);
        return $patient_id;
    }

    // สร้าง patient_id ใหม่
    $patient_id = generateUniquePatientId($pdo);

    // ใส่ข้อมูลลงในตาราง patient_information
    $stmt1 = $pdo->prepare("INSERT INTO patient_information (patient_id, full_name, id_card, rank, department, phone_number, emergency_phone, birth_date, current_status, marital_status, patient_image)
                            VALUES (:patient_id, :full_name, :id_card, :rank, :department, :phone_number, :emergency_phone, :birth_date, :current_status, :marital_status, :patient_image)");
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

    // ใส่ข้อมูลลงในตาราง patient_address
    $stmt2 = $pdo->prepare("INSERT INTO patient_address (patient_id, postal_code, province, amphur, tambon, soi, moo, number, area)
                            VALUES (:patient_id, :postal_code, :province, :amphur, :tambon, :soi, :moo, :number, :area)");
    $stmt2->bindParam(':patient_id', $patient_id);
    $stmt2->bindParam(':postal_code', $postal_code);
    $stmt2->bindParam(':province', $province);
    $stmt2->bindParam(':amphur', $amphur);
    $stmt2->bindParam(':tambon', $tambon);
    $stmt2->bindParam(':soi', $soi);
    $stmt2->bindParam(':moo', $moo);
    $stmt2->bindParam(':number', $number);
    $stmt2->bindParam(':area', $area);

    // ใส่ข้อมูลลงในตาราง patient_medical_information
    $stmt3 = $pdo->prepare("INSERT INTO patient_medical_information (patient_id, disease_type, note, patient_group, patient_type)
                            VALUES (:patient_id, :disease_type, :note, :patient_group, :patient_type)");
    $stmt3->bindParam(':patient_id', $patient_id);
    $stmt3->bindParam(':disease_type', $diseaseType);
    $stmt3->bindParam(':note', $note);
    $stmt3->bindParam(':patient_group', $patient_group);
    $stmt3->bindParam(':patient_type', $patient_type);

// ใส่ข้อมูลลงในตาราง monitor_information
$stmt4 = $pdo->prepare("INSERT INTO monitor_information (patient_id, monitor_round) VALUES (:patient_id, :monitor_round)");
$monitor_round = 0; // รอบเริ่มต้น
$stmt4->bindParam(':patient_id', $patient_id);
$stmt4->bindParam(':monitor_round', $monitor_round);

// ใส่ข้อมูลลงในตาราง treatment_information
$stmt5 = $pdo->prepare("INSERT INTO treatment_information (patient_id, treatment_round) VALUES (:patient_id, :treatment_round)");
$treatment_round = 0; // สถานะเริ่มต้น
$stmt5->bindParam(':patient_id', $patient_id);
$stmt5->bindParam(':treatment_round', $treatment_round);


// ดำเนินการเพิ่มข้อมูลทั้งหมด
$pdo->beginTransaction();

if ($stmt1->execute() && $stmt2->execute() && $stmt3->execute() && $stmt4->execute() && $stmt5->execute()) {
$pdo->commit();
echo json_encode(['success' => true, 'message' => 'Data inserted successfully']);
} else {
$pdo->rollBack();
echo json_encode(['success' => false, 'message' => 'ไม่สามารถเพิ่มข้อมูลได้']);
}
} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()]);
}
?>