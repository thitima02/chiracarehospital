<?php
// ตั้งค่าการเชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "username"; // เปลี่ยนเป็น username ของฐานข้อมูล
$password = "password"; // เปลี่ยนเป็น password ของฐานข้อมูล
$dbname = "chiracare_follow_up_db"; // เปลี่ยนเป็นชื่อฐานข้อมูล

// สร้างการเชื่อมต่อ
$conn = new mysqli($servername, $username, $password, $dbname);

// เช็คการเชื่อมต่อ
if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

// รับข้อมูล JSON จาก POST request
$data = json_decode(file_get_contents('php://input'), true);

// ดึงข้อมูลจาก JSON
$rank = $data['rank'];
$department = $data['department'];
$name = $data['name'];
$idNumber = $data['idNumber'];
$maritalStatus = $data['maritalStatus'];
$birthdate = $data['birthdate'];
$phone = $data['phone'];
$emergencyPhone = $data['emergencyPhone'];
$postalCode = $data['postalCode'];
$province = $data['province'];
$district = $data['district'];
$subDistrict = $data['subDistrict'];
$soi = $data['soi'];
$village = $data['village'];
$address = $data['address'];
$volunteerArea = $data['volunteerArea'];
$patientGroup = $data['patientGroup'];
$patientType = $data['patientType'];
$currentStatus = $data['currentStatus'];
$note = $data['note'];

// เตรียมคำสั่ง SQL เพื่อเพิ่มข้อมูลผู้ป่วย
$sql = "INSERT INTO patients (rank, department, name, id_number, marital_status, birthdate, phone, emergency_phone, postal_code, province, district, sub_district, soi, village, address, volunteer_area, patient_group, patient_type, current_status, note) 
VALUES ('$rank', '$department', '$name', '$idNumber', '$maritalStatus', '$birthdate', '$phone', '$emergencyPhone', '$postalCode', '$province', '$district', '$subDistrict', '$soi', '$village', '$address', '$volunteerArea', '$patientGroup', '$patientType', '$currentStatus', '$note')";

// ตรวจสอบและดำเนินการบันทึกข้อมูล
if ($conn->query($sql) === TRUE) {
  echo json_encode(['success' => true]);
} else {
  echo json_encode(['success' => false, 'message' => $conn->error]);
}

// ปิดการเชื่อมต่อ
$conn->close();
?>
