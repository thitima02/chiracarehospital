<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON
require_once '../db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล

$response = []; // ตัวแปรสำหรับเก็บข้อมูล response

// ตรวจสอบว่า request method เป็น POST หรือไม่
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

	// อ่านข้อมูล JSON จาก request
	$data = json_decode(file_get_contents("php://input"));

	// ตรวจสอบว่าได้รับข้อมูลครบหรือไม่ (ไม่บังคับทั้งหมด)
	$user_id = isset($data->user_id) ? $data->user_id : null;
	$role = isset($data->role) ? $data->role : null;
	$username = isset($data->username) ? $data->username : null;
	$password = isset($data->password) ? $data->password : null; // ไม่มีการแฮชรหัสผ่าน
	$responsibility_area = isset($data->responsibility_area) ? $data->responsibility_area : null;
	$user_image = isset($data->user_image) ? $data->user_image : null;
	$full_name = isset($data->full_name) ? $data->full_name : null;
	$phone_number = isset($data->phone_number) ? $data->phone_number : null;
	$department = isset($data->department) ? $data->department : null;

	try {
		// คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้ทั้งหมดจากตาราง user_info
		$stmt = $conn->prepare("UPDATE user_info SET role = '{$role}', username = '{$username}', password = '{$password}', responsibility_area = '{$responsibility_area}', user_image = '{$user_image}', full_name = '{$full_name}', phone_number = '{$phone_number}', department = '{$department}' WHERE user_id = {$user_id}");
		$stmt->execute();
		$response = [
			'status' => 'success',
			'message' => 'แก้ไขข้อมูลสำเร็จ'
		];


	} catch (PDOException $e) {
		// กรณีเกิดข้อผิดพลาดในการเชื่อมต่อหรือคำสั่ง SQL
		$response = [
			'status' => 'error',
			'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
		];

	}

	// ส่ง response กลับเป็น JSON
	echo json_encode($response); 
	
} else {
    // ถ้าไม่ใช่การส่ง POST request
    echo json_encode(["error" => "Invalid request method"]);
}