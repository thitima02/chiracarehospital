<?php
// รวมไฟล์เชื่อมต่อฐานข้อมูล
include 'db_connection.php';

// สร้างการเชื่อมต่อ
$conn = openConnection();

header('Content-Type: application/json');

// คำสั่ง SQL สำหรับดึงข้อมูลจากตาราง monitor_form
$sql = "SELECT * FROM monitor_form";
$result = $conn->query($sql);

// ตรวจสอบว่ามีข้อมูลหรือไม่
if ($result->num_rows > 0) {
    // สร้างอาร์เรย์เพื่อเก็บข้อมูล
    $monitorData = [];
    
    // ดึงข้อมูลและเก็บไว้ในอาร์เรย์
    while ($row = $result->fetch_assoc()) {
        $monitorData[] = $row;
    }
    
    // ส่งข้อมูลกลับในรูปแบบ JSON
    echo json_encode($monitorData);
} else {
    // ส่งข้อมูลว่าง
    echo json_encode([]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
