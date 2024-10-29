<?php
// get_treatment_form.php
include 'db_connection.php';

// กำหนด Content-Type เป็น JSON
header("Content-Type: application/json; charset=UTF-8");

try {
    // เตรียม SQL statement สำหรับดึงข้อมูลจากตาราง treatment_form
    $stmt = $conn->prepare("SELECT * FROM treatment_form");
    $stmt->execute();

    // ดึงข้อมูลทั้งหมด
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // แสดงข้อมูลในรูปแบบ JSON
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
} catch (PDOException $e) {
    echo json_encode(["message" => "ไม่สามารถดึงข้อมูลได้: " . $e->getMessage()]);
}
?>
