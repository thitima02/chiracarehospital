<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$dbname = "chiracare_follow_up_db";
$username = "root";
$password = "";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => "ไม่สามารถเชื่อมต่อฐานข้อมูลได้: " . $e->getMessage()]);
    exit();
}

// รับข้อมูล JSON
$inputData = json_decode(file_get_contents("php://input"), true);

// ดีบักการแสดงข้อมูลที่รับมา
if ($inputData === null) {
    echo json_encode(["status" => "error", "message" => "ไม่สามารถอ่านข้อมูล JSON ที่ส่งมาได้", "received_data" => file_get_contents("php://input")]);
    exit();
}

// ตรวจสอบการมีค่า patient_id
if (isset($inputData['patient_id']) && !empty($inputData['patient_id'])) {
    $patientId = $inputData['patient_id'];
    $incrementRound = isset($inputData['increment_round']) ? $inputData['increment_round'] : false; // ตรวจสอบว่ามีการส่งค่าบวกครั้งที่รักษาหรือไม่

    try {
        // ตรวจสอบว่ามีข้อมูลในฐานข้อมูลหรือไม่
        $sql = "SELECT * FROM treatment_information WHERE patient_id = :patient_id";
        $stmt = $pdo->prepare($sql);
        $stmt->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            // อัปเดตสถานะและบันทึกเวลาที่ยืนยัน
            if ($incrementRound) {
                // บวกครั้งที่รักษา
                $sqlUpdateRound = "UPDATE treatment_information SET treatment_status = 'รักษาเสร็จสิ้น', confirmed_at = NOW(), treatment_round = treatment_round + 1 WHERE patient_id = :patient_id";
                $stmtUpdateRound = $pdo->prepare($sqlUpdateRound);
                $stmtUpdateRound->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
                $stmtUpdateRound->execute();
            } else {
                $sqlUpdate = "UPDATE treatment_information SET treatment_status = 'รักษาเสร็จสิ้น', confirmed_at = NOW() WHERE patient_id = :patient_id";
                $stmtUpdate = $pdo->prepare($sqlUpdate);
                $stmtUpdate->bindParam(':patient_id', $patientId, PDO::PARAM_INT);
                $stmtUpdate->execute();
            }

            echo json_encode(["status" => "success", "message" => "อัปเดตสถานะการรักษาเสร็จสิ้นแล้ว"]);
        } else {
            echo json_encode(["status" => "error", "message" => "ไม่พบผู้ป่วยที่ตรงกับ patient_id นี้"]);
        }
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "ไม่สามารถอัปเดตสถานะการรักษาได้: " . $e->getMessage()]);
    }
} else {
    echo json_encode(["status" => "error", "message" => "ไม่พบ patient_id"]);
}
?>
