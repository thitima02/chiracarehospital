<?php
// show_patient_disease_types.php

include 'db_connect.php'; // เชื่อมต่อฐานข้อมูล

// ดึงข้อมูลจากตาราง
$sql = "SELECT * FROM patient_disease_types";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html>
<head>
    <title>แสดงข้อมูลประเภทผู้ป่วย</title>
</head>
<body>

<h2>ข้อมูลประเภทผู้ป่วย</h2>

<table border="1">
    <tr>
        <th>ID</th>
        <th>ประเภทโรค</th>
        <th>กลุ่มผู้ป่วย</th>
        <th>จำนวนผู้ป่วย</th>
    </tr>

    <?php
    if ($result->num_rows > 0) {
        // แสดงผลข้อมูลในตาราง
        while($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["id"] . "</td>";
            echo "<td>" . $row["disease"] . "</td>";
            echo "<td>" . $row["patient_group"] . "</td>";
            echo "<td>" . $row["total_count"] . "</td>";
            echo "</tr>";
        }
    } else {
        echo "<tr><td colspan='4'>ไม่มีข้อมูล</td></tr>";
    }
    ?>

</table>

</body>
</html>

<?php
// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
