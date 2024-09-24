<?php
// กำหนดประเภทเนื้อหาเป็น JSON
header('Content-Type: application/json');

// นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
include 'db_connection.php';

try {
    // สร้างอาร์เรย์เพื่อตอบกลับ
    $response = [];

    // คำสั่ง SQL ที่ 1: รวมจำนวนผู้ป่วยในแต่ละประเภทโรคและประเภทผู้ป่วย
    $stmt1 = $conn->prepare("
    SELECT 
        disease_type,
        SUM(CASE WHEN patient_type = 'ประชาชน' THEN 1 ELSE 0 END) AS public_total,
        SUM(CASE WHEN patient_type = 'ครอบครัว' THEN 1 ELSE 0 END) AS family_total,
        SUM(CASE WHEN patient_type = 'กำลังพล' THEN 1 ELSE 0 END) AS military_total
    FROM patient_medical_information
    GROUP BY disease_type
");
    $stmt1->execute();
    $diseaseData = $stmt1->fetchAll(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลหรือไม่
    if ($diseaseData) {
        $response['disease_data'] = $diseaseData;
    } else {
        $response['disease_data'] = ['message' => 'No disease records found'];
    }

    // คำสั่ง SQL ที่ 2: รวมจำนวนผู้ป่วยในแต่ละกลุ่มผู้ป่วย
    $stmt2 = $conn->prepare("
        SELECT 
            patient_group,
            COUNT(*) AS total
        FROM patient_medical_information
        WHERE patient_group IN ('กลุ่มป่วย', 'กลุ่มเสี่ยง', 'กลุ่มสงสัยป่วย')
        GROUP BY patient_group
    ");
    $stmt2->execute();
    $groupData = $stmt2->fetchAll(PDO::FETCH_ASSOC);

    // ตรวจสอบว่ามีข้อมูลหรือไม่สำหรับคำสั่ง SQL ที่ 2
    if ($groupData) {
        $response['group_data'] = $groupData;
    } else {
        $response['group_data'] = ['message' => 'No group records found'];
    }

    // ส่งข้อมูลเป็นรูปแบบ JSON
    echo json_encode($response);

} catch (Exception $e) {
    // ส่งข้อความแสดงข้อผิดพลาดหากเกิดข้อผิดพลาด
    echo json_encode(['error' => $e->getMessage()]);
}
?>