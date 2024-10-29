<?php
include '../db_connection.php'; // รวมไฟล์การเชื่อมต่อ

try {
    $stmt = $conn->prepare("SELECT * FROM patient_address"); // เปลี่ยนชื่อตารางตามที่คุณต้องการ
    $stmt->execute();
    
    // ดึงข้อมูลทั้งหมด
    $addresses = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // แสดงผลข้อมูลในรูปแบบ JSON
    echo json_encode($addresses);
} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>

