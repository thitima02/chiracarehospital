<?php
include 'db_connection.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่า 'id' ถูกส่งมาหรือไม่
if (!isset($_GET['id'])) {
    echo json_encode(['error' => 'ID not provided']);
    exit;
}

$id = $_GET['id'];
$sql = "SELECT p.full_name AS patient_name, pm.disease_type AS disease 
        FROM patient_information p 
        JOIN patient_medical_information pm ON p.id = pm.id 
        WHERE p.id = ?";
$stmt = $conn->prepare($sql);

if ($stmt === false) {
    echo json_encode(['error' => 'Failed to prepare statement']);
    exit;
}

$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();
$patient = $result->fetch_assoc();

if ($patient) {
    echo json_encode($patient);
} else {
    echo json_encode(['error' => 'No patient found with the given ID']);
}

$stmt->close();
$conn->close();
?>
