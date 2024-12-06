<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once '../db_connection.php';

try {       
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // รับข้อมูล JSON
    $data = json_decode(file_get_contents('php://input'), true);

    if (!$data) {
        echo json_encode(['success' => false, 'message' => 'Invalid JSON data']);
        exit;
    }

    // ตรวจสอบค่า patient_id
    $patient_id = !empty($data['patientId']) ? $data['patientId'] : null;
    if (!$patient_id) {
        echo json_encode(['success' => false, 'message' => 'Patient ID is required']);
        exit;
    }

    // รับค่าข้อมูล
    $full_name = $data['name'] ?? '-';
    $id_card = $data['idNumber'] ?? '-';
    $rank = $data['rank'] ?? '-';
    $department = $data['department'] ?? '-';
    $phone_number = $data['phone'] ?? '-';
    $emergency_phone = $data['emergencyPhone'] ?? '-';
    $birth_date = $data['birthdate'] ?? '-';
    $current_status = $data['currentStatus'] ?? '-';
    $marital_status = $data['maritalStatus'] ?? '-';

    $diseaseType = isset($data['diseaseType']) ? implode(",", $data['diseaseType']) : '-';
    $note = $data['note'] ?? '-';
    $patient_group = $data['patientGroup'] ?? '-';
    $patient_type = $data['patientType'] ?? '-';

    $postal_code = $data['postalCode'] ?? '-';
    $province = $data['province'] ?? '-';
    $amphur = $data['amphur'] ?? '-';
    $tambon = $data['tambon'] ?? '-';
    $soi = $data['soi'] ?? '-';
    $moo = $data['moo'] ?? '-';
    $number = $data['number'] ?? '-';
    $area = $data['area'] ?? '-';

    // ตรวจสอบ Base64
    $patientImageBase64 = isset($data['patientImageBase64']) ? $data['patientImageBase64'] : null;
    if ($patientImageBase64 && !preg_match('/^[a-zA-Z0-9\/+]+=*$/', $patientImageBase64)) {
        echo json_encode(['success' => false, 'message' => 'Invalid Base64 image']);
        exit;
    }

    $pdo->beginTransaction();

    // อัปเดตข้อมูล patient_information
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
    $stmt1->execute([
        ':full_name' => $full_name,
        ':id_card' => $id_card,
        ':rank' => $rank,
        ':department' => $department,
        ':phone_number' => $phone_number,
        ':emergency_phone' => $emergency_phone,
        ':birth_date' => $birth_date,
        ':current_status' => $current_status,
        ':marital_status' => $marital_status,
        ':patient_image' => $patientImageBase64,
        ':patient_id' => $patient_id
    ]);

    // อัปเดตข้อมูล patient_address
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
    $stmt2->execute([
        ':postal_code' => $postal_code,
        ':province' => $province,
        ':amphur' => $amphur,
        ':tambon' => $tambon,
        ':soi' => $soi,
        ':moo' => $moo,
        ':number' => $number,
        ':area' => $area,
        ':patient_id' => $patient_id
    ]);

    // อัปเดตข้อมูล patient_medical_information
    $stmt3 = $pdo->prepare("UPDATE patient_medical_information SET 
    disease_type = :disease_type,
    note = :note,
    patient_group = :patient_group,
    patient_type = :patient_type,
    last_update = NOW() 
    WHERE patient_id = :patient_id");

    $stmt3->execute([
        ':disease_type' => $diseaseType,
        ':note' => $note,
        ':patient_group' => $patient_group,
        ':patient_type' => $patient_type,
        ':patient_id' => $patient_id
    ]);

    $pdo->commit();

    echo json_encode(['success' => true, 'message' => 'Data updated successfully']);
} catch (PDOException $e) {
    $pdo->rollBack();
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
