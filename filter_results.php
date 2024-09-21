<?php
// รับค่าจากฟอร์ม
$treatmentTimes = isset($_GET['treatmentTimes']) ? $_GET['treatmentTimes'] : 'ทั้งหมด';

// เชื่อมต่อกับฐานข้อมูล
$conn = new mysqli('localhost', 'root', '', 'database_name');  // ปรับค่าตามฐานข้อมูลของคุณ

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// สร้าง SQL Query ตามเงื่อนไขที่เลือก
if ($treatmentTimes === 'ทั้งหมด') {
    $sql = "SELECT * FROM patient_infor";
} else {
    $sql = "SELECT * FROM patient_infor WHERE treatment_times = ?";
}

$stmt = $conn->prepare($sql);

// หากเลือกจำนวนครั้งที่รักษาเฉพาะเจาะจง ให้ bind ค่าที่เลือกไปกับ SQL Query
if ($treatmentTimes !== 'ทั้งหมด') {
    $stmt->bind_param("i", $treatmentTimes);  // i หมายถึง integer
}

$stmt->execute();
$result = $stmt->get_result();

// แสดงผลข้อมูลในตาราง
echo "<table>";
echo "<tr><th>ชื่อผู้ป่วย</th><th>จำนวนครั้งที่รักษา</th><th>วันที่นัดหมาย</th></tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>";
    echo "<td>" . $row['name'] . "</td>";
    echo "<td>" . $row['treatment_times'] . "</td>";
    echo "<td>" . $row['appointment_date'] . "</td>";
    echo "</tr>";
}

echo "</table>";

$stmt->close();
$conn->close();
?>
