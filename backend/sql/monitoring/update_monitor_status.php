<?php
// ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจาก POST หรือ JSON ที่ส่งมา
$data = json_decode(file_get_contents('php://input'), true);
$patientIds = $data['patientIds']; // รายการ id_patient_information
$status = $data['monitoring_status']; // สถานะการติดตามที่ต้องการอัปเดต

// ตรวจสอบว่ามีข้อมูลที่ส่งมาหรือไม่
if (!empty($patientIds)) {
    foreach ($patientIds as $id) {
        // สร้างคำสั่ง SQL เพื่ออัปเดตสถานะ
        $sql = "UPDATE monitor_information SET 
                monitoring_status = '$status'
                WHERE id_patient_information = '$id'";

        // ตรวจสอบว่าอัปเดตสำเร็จหรือไม่
        if ($conn->query($sql) !== TRUE) {
            echo "Error updating record: " . $conn->error;
        }
    }

    // ส่งผลตอบกลับเป็น JSON
    $response = array("status" => "success", "message" => "ข้อมูลถูกอัปเดตเรียบร้อยแล้ว");
} else {
    $response = array("status" => "error", "message" => "ไม่มีข้อมูลที่ต้องอัปเดต");
}

header('Content-Type: application/json');
echo json_encode($response);

// ปิดการเชื่อมต่อ
$conn->close();
?>
