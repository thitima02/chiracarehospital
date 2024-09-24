<?php
// ข้อมูลการเชื่อมต่อฐานข้อมูล
$servername = "localhost";
$username = "root"; // ชื่อผู้ใช้ฐานข้อมูล
$password = ""; // รหัสผ่านฐานข้อมูล
$dbname = "chiracare_follow_up_db";

// สร้างการเชื่อมต่อกับฐานข้อมูล
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die("การเชื่อมต่อผิดพลาด: " . $conn->connect_error);
}

// Query ดึงข้อมูลจากตาราง patient_address
$sql = "SELECT * FROM patient_address";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    // เริ่มการแสดงผล HTML
    echo "<tbody id='patient-tbody'>";
    
    // วนลูปเพื่อแสดงผลข้อมูลจากฐานข้อมูล
    while($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td><img src='assets/images/small 1.png' alt='patient image'> นายปลาร้า ไม่เหม็น</td>";
        echo "<td id='startDate'>22/05/2022</td>";
        echo "<td id='endDate'>29/05/2022</td>";
        
        // ดึงข้อมูลที่อยู่จากฐานข้อมูล
        echo "<td id='address'>" . $row['number'] . " หมู่ " . $row['moo'] . " ซอย " . $row['soi'] . " ตำบล/แขวง " . $row['tambon'] . " อำเภอ/เขต " . $row['amphur'] . " จังหวัด " . $row['province'] . " เลขไปรษณีย์ " . $row['postal_code'] . "</td>";
        
        echo "<td id='timesFollowed'>2</td>";
        echo "<td><button class='action-btn' title='คลิกเพื่อกรอกฟอร์ม' id='add-button'><i class='bx bxs-edit'></i>คลิกเพื่อกรอกฟอร์ม</button></td>";
        echo "</tr>";
    }
    
    echo "</tbody>";
} else {
    echo "ไม่พบข้อมูลผู้ป่วย";
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
