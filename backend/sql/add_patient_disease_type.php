<?php
// add_patient_disease_type.php

include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ตรวจสอบว่ามีการส่งข้อมูลจากฟอร์ม
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $disease = $_POST['disease'];
    $patient_group = $_POST['patient_group'];
    $total_count = $_POST['total_count'];

    // สร้าง SQL สำหรับเพิ่มข้อมูล
    $sql = "INSERT INTO patient_disease_types (disease, patient_group, total_count) 
            VALUES ('$disease', '$patient_group', '$total_count')";

    // ตรวจสอบว่าการเพิ่มข้อมูลสำเร็จหรือไม่
    if ($conn->query($sql) === TRUE) {
        echo "เพิ่มข้อมูลสำเร็จ";
    } else {
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>

<!-- <!DOCTYPE html>
<html>
<head>
    <title>เพิ่มประเภทผู้ป่วย</title>
</head>
<body>

<h2>ฟอร์มเพิ่มประเภทผู้ป่วย</h2>
<form method="POST" action="">
    ประเภทโรค: <input type="text" name="disease" required><br><br>
    กลุ่มผู้ป่วย: 
    <select name="patient_group" required>
        <option value="ประชาชน">ประชาชน</option>
        <option value="ครอบครัว">ครอบครัว</option>
        <option value="กำลังพล">กำลังพล</option>
    </select><br><br>
    จำนวนผู้ป่วย: <input type="number" name="total_count" required><br><br>
    <input type="submit" value="เพิ่มข้อมูล">
</form>

</body>
</html> -->
