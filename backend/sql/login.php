<?php
session_start();
header('Content-Type: application/json');
require_once './db_connection.php'; // นำเข้าไฟล์เชื่อมต่อฐานข้อมูล
require '../../vendor/autoload.php'; // นำเข้า Composer autoload

use \Firebase\JWT\JWT;

$response = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents("php://input"), true); // รับข้อมูลจาก request body
    $username = $data['username'];
    $password = $data['password'];

    try {
        // คำสั่ง SQL สำหรับดึงข้อมูลผู้ใช้จากตาราง user_info ตาม username
        $stmt = $conn->prepare("SELECT * FROM user_info WHERE username = :username LIMIT 1");
        $stmt->bindParam(':username', $username);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user) {
            // ตรวจสอบรหัสผ่าน (สมมติว่าใช้การเข้ารหัสในฐานข้อมูลด้วย password_hash)
            if ($password === $user['password']) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['full_name'] = $user['full_name'];

                // สร้าง JWT token
                $secretKey = 'your_secret_key'; // เปลี่ยนเป็นคีย์ที่ปลอดภัย
                $payload = [
                    'iat' => time(), // เวลาออก token
                    'exp' => time() + (15 * 60), // เวลา expiration 15 นาที
                    'data' => [
                        'id' => $user['id'],
                        'username' => $user['username'],
                        'role' => $user['role'],
                        'full_name' => $user['full_name'],
                    ],
                ];

                // เข้ารหัส JWT
                $jwt = JWT::encode($payload, $secretKey, 'HS256');

                // ส่งข้อมูลผู้ใช้กลับไปพร้อมกับ JWT token
                $response = [
                    'status' => 'success',
                    'message' => 'เข้าสู่ระบบสำเร็จ',
                    'token' => $jwt,
                    'user' => [
                        'id' => $user['id'],
                        'role' => $user['role'],
                        'username' => $user['username'],
                        'responsibility_area' => $user['responsibility_area'],
                        'user_image' => $user['user_image'],
                        'full_name' => $user['full_name'],
                        'phone_number' => $user['phone_number'],
                        'department' => $user['department']
                    ]
                ];
            } else {
                $response = [
                    'status' => 'error',
                    'message' => 'รหัสผ่านไม่ถูกต้อง'
                ];
            }
        } else {
            $response = [
                'status' => 'error',
                'message' => 'ไม่พบชื่อผู้ใช้นี้'
            ];
        }
    } catch (PDOException $e) {
        $response = [
            'status' => 'error',
            'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
        ];
    }
} else {
    $response = [
        'status' => 'error',
        'message' => 'รองรับเฉพาะการร้องขอแบบ POST'
    ];
}

echo json_encode($response);
