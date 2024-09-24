<?php
// Database connection
$host = "localhost";
$db = "chiracare_follow_up_db";
$user = "root";
$pass = "";

// Create connection
$conn = new mysqli($host, $user, $pass, $db);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch values in PHP
$searchTerm = $_POST['searchTerm'] ?? '';
$updatedDate = $_POST['updatedDate'] ?? '';
$diseaseType = $_POST['diseaseType'] ?? 'ทั้งหมด';

var_dump($searchTerm, $updatedDate, $diseaseType);

// SQL Query
$sql = "SELECT p.full_name, tf.newupdate, pm.disease_type, tf.general_symptoms, tf.treatment_issue, 
        tf.date_of_treatment, tf.next_appointment_date, tf.notes
        FROM treatment_form tf 
        JOIN patient_information p ON tf.id_patient_information = p.id 
        JOIN patient_medical_information pm ON tf.id_patient_medical_information = pm.id
        WHERE 1=1";

// ฟิลเตอร์การค้นหาชื่อ
if (!empty($searchTerm)) {
    $sql .= " AND p.full_name LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
}

// ฟิลเตอร์ตามวันที่อัปเดต
if (!empty($updatedDate)) {
    $sql .= " AND DATE(tf.newupdate) = '" . $conn->real_escape_string($updatedDate) . "'";
}

// ฟิลเตอร์ตามประเภทโรค
if (!empty($diseaseType) && $diseaseType !== 'ทั้งหมด') {
    $sql .= " AND pm.disease_type = '" . $conn->real_escape_string($diseaseType) . "'";
}

// ดึงข้อมูลจากฐานข้อมูล
$result = $conn->query($sql);
$historyData = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $historyData[] = $row;
    }
}

// ส่งข้อมูลเป็น JSON กลับไปที่ฝั่ง Frontend
header('Content-Type: application/json');
echo json_encode($historyData);

$conn->close();
?>
