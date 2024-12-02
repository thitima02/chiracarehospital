<?php
// กำหนดหัวตาราง
$headers = [
    "คำนำหน้า", "ยศ", "ชื่อ - นามสกุล", "เลขบัตรประชาชน", "วันเกิด", "สถานะการสมรส", 
    "เบอร์โทรศัพท์", "เบอร์โทรศัพท์ญาติ", "เลขไปรษณีย์", "จังหวัด", "อำเภอ/เขต", "ตำบล/แขวง", 
    "ซอย", "หมู่", "ที่อยู่", "เขตที่อยู่อาศัย", "กลุ่มผู้ป่วย", "ประเภทผู้ป่วย", "ประเภทโรค", 
    "สถานะปัจจุบัน", "เพิ่มเติม"
];

// ข้อมูลตัวอย่าง (สามารถปรับเปลี่ยนได้)
$data = [
    ["นาย", "-", "สมชาย แสนดี", "1234567890123", "1985-06-15", "โสด", "0987654321", "0876543210", "10230", "กรุงเทพมหานคร", "บางเขน", "จตุจักร", "ลาดพร้าว", "1", "123/3", "15", "กลุ่มสงสัยป่วย", "กำลังพล", "โรคเบาหวาน", "ปกติ (มีชีวิต,เป็นผู้ป่วยของรพบ)", "-"]
];

// กำหนดชื่อไฟล์ CSV
$filename = "patient_data.csv";

// เปิดไฟล์สำหรับเขียน
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename=' . $filename);

// สร้างตัวจับข้อมูล CSV
$output = fopen('php://output', 'w');

// เขียน BOM (Byte Order Mark) สำหรับ UTF-8
fwrite($output, "\xEF\xBB\xBF");

// เขียนหัวตาราง
fputcsv($output, $headers);

// เขียนข้อมูลผู้ป่วย
foreach ($data as $row) {
    fputcsv($output, $row);
}

// ปิดไฟล์
fclose($output);
?>
