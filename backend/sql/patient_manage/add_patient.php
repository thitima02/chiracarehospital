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

    // ตรวจสอบค่า patientId
    $patientId = isset($data['patientId']) ? $data['patientId'] : null;
    if (!$patientId) {
        echo json_encode(['success' => false, 'message' => 'Patient ID not provided']);
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

    // คำสั่ง SQL เพื่ออัปเดตข้อมูลผู้ป่วยในฐานข้อมูล
    $stmt = $pdo->prepare("UPDATE patient_information SET 
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

    // ผูกค่ากับ SQL
    $stmt->bindParam(':patient_id', $patientId);
    $stmt->bindParam(':full_name', $full_name);
    $stmt->bindParam(':id_card', $id_card);
    $stmt->bindParam(':rank', $rank);
    $stmt->bindParam(':department', $department);
    $stmt->bindParam(':phone_number', $phone_number);
    $stmt->bindParam(':emergency_phone', $emergency_phone);
    $stmt->bindParam(':birth_date', $birth_date);
    $stmt->bindParam(':current_status', $current_status);
    $stmt->bindParam(':marital_status', $marital_status);
    $stmt->bindParam(':patient_image', $patientImageBase64);

    // ตรวจสอบการอัปเดต
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'ข้อมูลผู้ป่วยได้รับการอัปเดตเรียบร้อย']);
    } else {
        echo json_encode(['success' => false, 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
    }

} catch (PDOException $e) {
    echo json_encode(['success' => false, 'message' => 'เกิดข้อผิดพลาดในการเชื่อมต่อฐานข้อมูล']);
}
?>