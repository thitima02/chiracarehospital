<?php
// ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracarehospital";

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อล้มเหลว: " . $conn->connect_error);
}

// รับข้อมูลจาก POST หรือฟอร์มที่ส่งมา
$id = $_POST['id']; // id ของผู้ป่วย
$d_name = $_POST['d_name'];
$d_lastname = $_POST['d_lastname'];
$d_age = $_POST['d_age'];
$d_tel = $_POST['d_tel'];

// สร้างคำสั่ง SQL สำหรับการอัปเดตข้อมูล
$sql = "UPDATE patient_information SET 
        d_name = '$d_name', 
        d_lastname = '$d_lastname',
        d_age = '$d_age',
        d_tel = '$d_tel'
        WHERE id_patient_information = '$id'";

if ($conn->query($sql) === TRUE) {
    echo "ข้อมูลถูกอัปเดตเรียบร้อยแล้ว";
} else {
    echo "Error updating record: " . $conn->error;
}

// ปิดการเชื่อมต่อ
$conn->close();
<<<<<<< HEAD
?>
=======
?>
>>>>>>> 66d56798c43af83ae6c72c3a51642cd333ec7b1d
