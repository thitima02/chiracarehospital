<?php
header("Content-Type: application/json");
require_once '../db_connection.php';

if (isset($_GET['patient_id']) && !empty($_GET['patient_id'])) {
    $patient_id = $_GET['patient_id'];
    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        echo json_encode([
            "status" => "error",
            "message" => "การเชื่อมต่อฐานข้อมูลล้มเหลว: " . $conn->connect_error
        ]);
        exit;
    }

    $sql = "SELECT full_name, id_card, age, birth_date, phone_number, emergency_phone FROM patient_information WHERE patient_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $patient_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $data = $result->fetch_assoc();
        echo json_encode([
            "status" => "success",
            "data" => $data
        ]);
    } else {
        echo json_encode([
            "status" => "error",
            "message" => "ไม่พบข้อมูลผู้ป่วย"
        ]);
    }

    $stmt->close();
    $conn->close();
} else {
    echo json_encode([
        "status" => "error",
        "message" => "ไม่ได้ระบุรหัสผู้ป่วย"
    ]);
}
?>
