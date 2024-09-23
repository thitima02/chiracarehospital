<?php
// เชื่อมต่อฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'chiracare_follow_up_db');

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้าง SQL Query
$sql = "SELECT p.full_name, tf.newupdate, pm.disease_type 
        FROM treatment_form tf 
        JOIN patient_information p ON tf.id_patient_information = p.id 
        JOIN patient_medical_information pm ON tf.id_patient_medical_information = pm.id 
        WHERE 1=1";

// ฟิลเตอร์การค้นหาชื่อผู้ป่วย
$searchTerm = $_POST['searchTerm'] ?? '';
if (!empty($searchTerm)) {
    $sql .= " AND p.full_name LIKE '%" . $conn->real_escape_string($searchTerm) . "%'";
}

// ฟิลเตอร์วันที่อัปเดตล่าสุด
$updatedDate = $_POST['updatedDate'] ?? '';
if (!empty($updatedDate)) {
    $sql .= " AND DATE(tf.newupdate) = '" . $conn->real_escape_string($updatedDate) . "'";
}

// ฟิลเตอร์ประเภทโรค
$diseaseType = $_POST['diseaseType'] ?? '';
if (!empty($diseaseType) && $diseaseType !== 'ทั้งหมด') {
    $sql .= " AND pm.disease_type LIKE '%" . $conn->real_escape_string($diseaseType) . "%'";
}

// แสดง SQL Query สำหรับการตรวจสอบ
// echo $sql; // ใช้สำหรับดีบัก 

$result = $conn->query($sql);
if (!$result) {
    die("Query failed: " . $conn->error);
}

// สร้าง array สำหรับเก็บข้อมูล
$historyData = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $historyData[] = $row;
    }
} else {
    // ถ้าไม่มีข้อมูล
    $historyData = ['message' => 'No data found'];
}

// ตั้งค่า header เป็น JSON
header('Content-Type: application/json');
echo json_encode($historyData);

$conn->close();
?>