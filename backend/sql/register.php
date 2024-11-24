<?php
header("Content-Type: application/json; charset=UTF-8");
include('db_connection.php');

// ตรวจสอบว่า request method เป็น POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // อ่านข้อมูล JSON จาก request
    $data = json_decode(file_get_contents("php://input"));

    // ตรวจสอบว่าได้รับข้อมูลครบหรือไม่ (ไม่บังคับทั้งหมด)
    $role = isset($data->role) ? $data->role : null;
    $username = isset($data->username) ? $data->username : null;
    $password = isset($data->password) ? $data->password : null; // ไม่มีการแฮชรหัสผ่าน
    $responsibility_area = isset($data->responsibility_area) ? $data->responsibility_area : null;
    $user_image = isset($data->user_image) ? $data->user_image : null;
    $full_name = isset($data->full_name) ? $data->full_name : null;
    $phone_number = isset($data->phone_number) ? $data->phone_number : null;
    $department = isset($data->department) ? $data->department : null;

    try {
        // เริ่มต้น Transaction
        $conn->beginTransaction();

        // สร้างคำสั่ง SQL สำหรับเพิ่มข้อมูลผู้ใช้ใหม่
        $sql = "INSERT INTO user_info (role, username, password, responsibility_area, user_image, full_name, phone_number, department) 
                VALUES (:role, :username, :password, :responsibility_area, :user_image, :full_name, :phone_number, :department)";

        // เตรียมคำสั่ง SQL
        $stmt = $conn->prepare($sql);

        // Bind ข้อมูลที่รับมาจาก JSON
        $stmt->bindParam(':role', $role);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':password', $password); // ใช้รหัสผ่านแบบข้อความธรรมดา
        $stmt->bindParam(':responsibility_area', $responsibility_area);
        $stmt->bindParam(':user_image', $user_image);
        $stmt->bindParam(':full_name', $full_name);
        $stmt->bindParam(':phone_number', $phone_number);
        $stmt->bindParam(':department', $department);

        // Execute การเพิ่มข้อมูล
        $stmt->execute();

        // ดึงค่า id ที่เพิ่มล่าสุดมาใช้เป็น user_id
        $lastInsertId = $conn->lastInsertId();

        // อัปเดต user_id
        $updateSql = "UPDATE user_info SET user_id = :user_id WHERE id = :id";
        $updateStmt = $conn->prepare($updateSql);
        $updateStmt->bindParam(':user_id', $lastInsertId);
        $updateStmt->bindParam(':id', $lastInsertId);
        $updateStmt->execute();

        // Commit Transaction
        $conn->commit();

        // ตอบกลับว่าเพิ่มข้อมูลสำเร็จ
        echo json_encode(["message" => "User added successfully", "user_id" => $lastInsertId]);
    } catch (PDOException $e) {
        // Rollback Transaction หากเกิดข้อผิดพลาด
        $conn->rollBack();
        echo json_encode(["error" => "Failed to add user: " . $e->getMessage()]);
    }
} else {
    // ถ้าไม่ใช่การส่ง POST request
    echo json_encode(["error" => "Invalid request method"]);
}
?>
