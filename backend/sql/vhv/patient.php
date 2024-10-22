<?php
header('Content-Type: application/json');
include_once '../db_connection.php';

try {
    // สร้างคำสั่ง SQL สำหรับดึงข้อมูลจากตาราง patient_information และ patient_address
    $sql = "SELECT p.full_name, a.province, a.amphur, a.tambon, a.soi, a.moo , a.number
            FROM patient_information p
            JOIN patient_address a ON p.id_patient_address = a.id";

    // เตรียมและรันคำสั่ง SQL
    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // ดึงข้อมูลทั้งหมด
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ส่งข้อมูลเป็น JSON
    echo json_encode($patients);
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
