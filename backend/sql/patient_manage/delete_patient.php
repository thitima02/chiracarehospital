<?php
header('Content-Type: application/json');

// รวมไฟล์การเชื่อมต่อฐานข้อมูล
include('../db_connection.php');  // ใช้ path ที่เหมาะสม

// ตรวจสอบว่าได้รับค่า patient_id หรือไม่
if (isset($_POST['patient_id'])) {
    $patient_id = $_POST['patient_id'];

    // เริ่มการทำงานใน Transaction
    $conn->beginTransaction();

    try {
        // ลบข้อมูลในตาราง patient_medical_information
        $sql1 = "DELETE FROM patient_medical_information WHERE patient_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt1->execute();

        // ลบข้อมูลในตาราง patient_information
        $sql2 = "DELETE FROM patient_information WHERE patient_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt2->execute();

        // ลบข้อมูลในตาราง patient_address
        $sql3 = "DELETE FROM patient_address WHERE patient_id = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt3->execute();

        // ถ้าทุกคำสั่งสำเร็จ, ทำการ commit
        $conn->commit();
        echo json_encode(["message" => "ลบข้อมูลผู้ป่วยสำเร็จ!"]);
    } catch (Exception $e) {
        // หากเกิดข้อผิดพลาด, ทำการ rollback
        $conn->rollBack();
        echo json_encode(["message" => "เกิดข้อผิดพลาดในการลบข้อมูล: " . $e->getMessage()]);
    }

    // ปิดการเชื่อมต่อ PDO (ไม่จำเป็นต้องใช้ close)
    $conn = null;
} else {
    echo json_encode(["message" => "ไม่พบ patient_id"]);
}
?>
