<?php
// รวมการเชื่อมต่อฐานข้อมูลด้วย PDO
include 'db_connection.php';

try {
    // เตรียมคำสั่ง SQL เพื่อนับจำนวน monitor_status และดึงค่าที่เหลือ
    $stmt = $conn->prepare("
        SELECT 
            id,
            patient_id,
            monitor_date,
            monitor_round,
            monitor_status,
            last_update,
            id_patient_information,
            id_user_info,
            id_monitor_form,
            id_treatment_information,
            COUNT(*) AS status_count
        FROM monitor_information
        GROUP BY monitor_status
    ");
    
    // ดำเนินการคำสั่ง SQL
    $stmt->execute();

    // ดึงข้อมูลทั้งหมดในรูปแบบอาร์เรย์แบบ associative
    $monitor_information = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if (count($monitor_information) > 0) {
        // แสดงผลข้อมูลในรูปแบบ JSON
        header('Content-Type: application/json'); // ตั้งค่า Content-Type เป็น JSON
        echo json_encode($monitor_information);
    } else {
        // ข้อความถ้าไม่พบข้อมูล
        header('Content-Type: application/json'); // ตั้งค่า Content-Type เป็น JSON
        echo json_encode(array("message" => "No data found."));
    }
    
} catch (PDOException $e) {
    // จัดการกับข้อผิดพลาด
    header('Content-Type: application/json'); // ตั้งค่า Content-Type เป็น JSON
    echo json_encode(array("error" => $e->getMessage()));
}

// ปิดการเชื่อมต่อ
$conn = null;
?>
