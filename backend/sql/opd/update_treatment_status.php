<?php
// Database connection
$host = "localhost";
$db = "chiracare_follow_up_db";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass, $db);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$patient_id = $_POST['patient_id'];

// เช็คสถานะปัจจุบันก่อนอัปเดต
$sql_check_status = "SELECT treatment_status FROM treatment_information WHERE id_patient_information = ?";
$stmt_check = $conn->prepare($sql_check_status);
$stmt_check->bind_param("i", $patient_id);
$stmt_check->execute();
$stmt_check->bind_result($current_status);
$stmt_check->fetch();
$stmt_check->close();

if ($current_status === "รักษาเสร็จสิ้น") {
    echo json_encode(["status" => "error", "message" => "สถานะนี้ถูกอัปเดตเป็น 'รักษาเสร็จสิ้น' แล้ว"]);
    exit();
}

// ถ้ายังไม่ได้อัปเดต ให้ทำการอัปเดต
$new_status = "รักษาเสร็จสิ้น";
$sql = "UPDATE treatment_information SET treatment_status = ? WHERE id_patient_information = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("si", $new_status, $patient_id);

if ($stmt->execute()) {
    echo json_encode(["status" => "success", "message" => "สถานะการรักษาอัปเดตสำเร็จ"]);
} else {
    echo json_encode(["status" => "error", "message" => "เกิดข้อผิดพลาดในการอัปเดตสถานะ"]);
}

$stmt->close();
$conn->close();
?>