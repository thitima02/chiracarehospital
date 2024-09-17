<?php
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // ตรวจสอบว่ามีการอัปโหลดไฟล์หรือไม่
    if (isset($_FILES['profile-image']) && $_FILES['profile-image']['error'] === UPLOAD_ERR_OK) {
        // กำหนดโฟลเดอร์ที่จะเก็บรูปภาพ
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile-image']['name']);

        // ตรวจสอบว่ามีการอัปโหลดสำเร็จหรือไม่
        if (move_uploaded_file($_FILES['profile-image']['tmp_name'], $uploadFile)) {
            // เก็บชื่อไฟล์และข้อมูลผู้ป่วยลงฐานข้อมูล
            $name = $_POST['name'];
            $idNumber = $_POST['id-number'];
            $age = $_POST['age'];
            $birthdate = $_POST['birthdate'];
            $phone = $_POST['phone'];
            $phoneNumber = $_POST['phone-number'];

            // เชื่อมต่อฐานข้อมูลและบันทึกข้อมูล (เปลี่ยน username, password, และ database_name ให้ถูกต้อง)
            $conn = new mysqli('localhost', 'root', '', 'patient_db'); // ใช้ฐานข้อมูล patient_db ที่สร้าง

            if ($conn->connect_error) {
                die('Connection failed: ' . $conn->connect_error);
            }

            // Query เพื่อบันทึกข้อมูล
            $sql = "INSERT INTO patients (name, id_number, age, birthdate, phone, emergency_phone, profile_image) 
                    VALUES ('$name', '$idNumber', '$age', '$birthdate', '$phone', '$phoneNumber', '$uploadFile')";

            if ($conn->query($sql) === TRUE) {
                echo json_encode(['success' => true]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Error: ' . $conn->error]);
            }

            $conn->close();
        } else {
            echo json_encode(['success' => false, 'message' => 'File upload failed']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'No file uploaded']);
    }
}
?>
