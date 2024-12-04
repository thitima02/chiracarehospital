<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "chiracare_follow_up_db"; 

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูลจาก request
$data = json_decode(file_get_contents('php://input'), true);
$patient_id = $data['patient_id'];
$monitor_status = $data['monitor_status'];
$treatment_status = $data['treatment_status'];
$disease_type = $data['disease_type'];
$monitor_date = $data['monitor_date'];
$monitor_deadline = $data['monitor_deadline'];
$monitor_round = $data['monitor_round'];
$appointment_date = $data['appointment_date'];

// อัปเดตข้อมูลในฐานข้อมูล
$sql = "UPDATE monitor_information 
        SET monitor_status='$monitor_status', monitor_date='$monitor_date', monitor_deadline='$monitor_deadline', monitor_round='$monitor_round' 
        WHERE patient_id='$patient_id'";

if ($conn->query($sql) === TRUE) {
    // อัปเดตสถานะการรักษา
    $sqlTreatment = "UPDATE treatment_information 
                     SET treatment_status='$treatment_status', appointment_date='$appointment_date'
                     WHERE patient_id='$patient_id'";

    if ($conn->query($sqlTreatment) === TRUE) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error updating treatment status: ' . $conn->error]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Error updating monitor status: ' . $conn->error]);
}

$conn->close();
?>
