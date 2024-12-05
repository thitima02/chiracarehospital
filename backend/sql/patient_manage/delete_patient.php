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
        // ลบข้อมูลในตาราง monitor_information
        $sql1 = "DELETE FROM monitor_information WHERE patient_id = ?";
        $stmt1 = $conn->prepare($sql1);
        $stmt1->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt1->execute();

        // ลบข้อมูลในตาราง treatment_information
        $sql2 = "DELETE FROM treatment_information WHERE patient_id = ?";
        $stmt2 = $conn->prepare($sql2);
        $stmt2->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt2->execute();

        // ลบข้อมูลในตาราง monitor_form
        $sql3 = "DELETE FROM monitor_form WHERE patient_id = ?";
        $stmt3 = $conn->prepare($sql3);
        $stmt3->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt3->execute();

        // ลบข้อมูลในตาราง treatment_form
        $sql4 = "DELETE FROM treatment_form WHERE patient_id = ?";
        $stmt4 = $conn->prepare($sql4);
        $stmt4->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt4->execute();

        // ลบข้อมูลในตาราง assign_patients_to_vhv
        $sql5 = "DELETE FROM assign_patients_to_vhv WHERE patient_id = ?";
        $stmt5 = $conn->prepare($sql5);
        $stmt5->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt5->execute();

        // ลบข้อมูลในตาราง patient_medical_information
        $sql6 = "DELETE FROM patient_medical_information WHERE patient_id = ?";
        $stmt6 = $conn->prepare($sql6);
        $stmt6->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt6->execute();

        // ลบข้อมูลในตาราง patient_information
        $sql7 = "DELETE FROM patient_information WHERE patient_id = ?";
        $stmt7 = $conn->prepare($sql7);
        $stmt7->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt7->execute();

        // ลบข้อมูลในตาราง patient_address
        $sql8 = "DELETE FROM patient_address WHERE patient_id = ?";
        $stmt8 = $conn->prepare($sql8);
        $stmt8->bindParam(1, $patient_id, PDO::PARAM_INT);
        $stmt8->execute();

        // ถ้าทุกคำสั่งสำเร็จ, ทำการ commit
        $conn->commit();
        echo json_encode(["message" => "ลบข้อมูลผู้ป่วยและข้อมูลที่เกี่ยวข้องสำเร็จ!"]);
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
