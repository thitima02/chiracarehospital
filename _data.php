<?php
// เปิดการรายงานข้อผิดพลาด
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "hospital_db"; // ชื่อฐานข้อมูลของคุณ

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// วันที่ปัจจุบัน
$current_date = date('Y-m-d');

// คำสั่ง SQL
$sql = "SELECT treatment_status FROM patient_infor WHERE DATE(appointment_date) = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('s', $current_date);

// รันคำสั่ง SQL
if ($stmt->execute()) {
    $result = $stmt->get_result();
    $patientsToday = 0;
    $treated = 0;
    $untreated = 0;

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $patientsToday++;
            
            // นับจำนวนผู้ป่วยที่รักษาเสร็จสิ้นและยังไม่ได้รักษา
            if ($row['treatment_status'] == 'รักษาเสร็จสิ้น') {
                $treated++;
            } elseif ($row['treatment_status'] == 'ยังไม่ได้รักษา') {
                $untreated++;
            }
        }
    } else {
        echo json_encode([
            "totalAppointments" => 0,
            "totalTreated" => 0,
            "totalUntreated" => 0
        ]);
        exit();
    }

    // สร้างการตอบกลับในรูปแบบ JSON
    $response = [
        "totalAppointments" => $patientsToday,
        "totalTreated" => $treated,
        "totalUntreated" => $untreated
    ];

    echo json_encode($response);
} else {
    echo json_encode([
        "totalAppointments" => 0,
        "totalTreated" => 0,
        "totalUntreated" => 0
    ]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
