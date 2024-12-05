<?php
// เชื่อมต่อกับฐานข้อมูล
include('../db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['patient_id'])) {
        $patient_id = $_POST['patient_id'];

        // ตรวจสอบว่า patient_id เป็นจำนวนเต็มหรือไม่
        if (is_numeric($patient_id) && $patient_id > 0) {
            try {
                // ใช้ PDO เพื่อเตรียมคำสั่ง SQL
                $sql = "SELECT * FROM assign_patients_to_vhv WHERE patient_id = :patient_id";
                $stmt = $conn->prepare($sql);
                
                // ผูกค่า parameter ด้วย bindValue
                $stmt->bindValue(':patient_id', $patient_id, PDO::PARAM_INT);
                
                $stmt->execute();
                $result = $stmt->fetchAll();

                // หากพบข้อมูลแสดงว่าได้รับมอบหมายแล้ว
                if (count($result) > 0) {
                    echo 'assigned';
                } else {
                    echo 'not_assigned';
                }
            } catch (PDOException $e) {
                // ถ้ามีข้อผิดพลาดในการทำงานกับฐานข้อมูล
                echo 'Database error: ' . $e->getMessage();
            }
        } else {
            // ถ้า patient_id ไม่ถูกต้อง
            echo 'Invalid patient_id';
        }
    } else {
        // ถ้าไม่มีการส่ง patient_id มาจาก POST
        echo 'patient_id is missing';
    }
}
?>
