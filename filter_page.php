<?php
// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'database_name');  // ปรับค่าตามฐานข้อมูลของคุณ

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Query เพื่อหาค่าจำนวนครั้งที่รักษามากที่สุด
$sql = "SELECT MAX(treatment_times) AS max_treatment_times FROM patient_infor";
$result = $conn->query($sql);
$row = $result->fetch_assoc();
$max_treatment_times = $row['max_treatment_times'];

// สร้างฟอร์มเลือกจำนวนครั้งที่รักษา
echo '<form method="GET" action="filter_results.php">';
echo '<label for="treatmentTimesFilter">จำนวนครั้งที่รักษา:</label>';
echo '<select name="treatmentTimes" id="treatmentTimesFilter">';
echo '<option value="ทั้งหมด">ทั้งหมด</option>';

// Loop เพื่อสร้างตัวเลือกจำนวนครั้งที่รักษา 1 ถึง max_treatment_times
for ($i = 1; $i <= $max_treatment_times; $i++) {
    echo '<option value="' . $i . '">' . $i . '</option>';
}

echo '</select>';
echo '<button type="submit">กรองข้อมูล</button>';
echo '</form>';

$conn->close();
?>
