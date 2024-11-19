<?php
header('Content-Type: application/json');
error_reporting(E_ALL);
ini_set('display_errors', 1);

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "chiracare_follow_up_db";

<<<<<<< HEAD
try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo json_encode(["error" => "Connection failed: " . $e->getMessage()]);
    exit;
=======
$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    die(json_encode(["error" => "Connection failed: " . $conn->connect_error]));
>>>>>>> 66d56798c43af83ae6c72c3a51642cd333ec7b1d
}

// ดึงข้อมูล JSON ที่ส่งมาจาก JavaScript
$data = json_decode(file_get_contents("php://input"), true);

<<<<<<< HEAD
// แสดงข้อมูลที่ได้รับใน log
error_log('Received data: ' . print_r($data, true));

// ตรวจสอบว่าข้อมูลที่จำเป็นถูกส่งมา
$monitorStatus = $data['monitorStatus'] ?? '';
$patientIds = $data['patientIds'] ?? [];
$monitorDeadline = $data['monitorDeadline'] ?? ''; 
$monitorStartDate = $data['monitorStartDate'] ?? '';

if (empty($monitorStatus) || empty($monitorDeadline) || empty($monitorStartDate) || !is_array($patientIds) || empty($patientIds)) {
    echo json_encode(['status' => 'error', 'message' => 'Missing required data']);
    exit;
}

// ตรวจสอบก่อนการอัปเดตว่า ID มีอยู่ในฐานข้อมูลหรือไม่
$placeholders = implode(',', array_fill(0, count($patientIds), '?'));
$check_sql = "SELECT COUNT(*) as count FROM monitor_information WHERE patient_id IN ($placeholders)";
$check_stmt = $pdo->prepare($check_sql);
$check_stmt->execute($patientIds);
$row = $check_stmt->fetch(PDO::FETCH_ASSOC);
$matching_count = $row['count'];

if ($matching_count > 0) {
    // เตรียมคำสั่ง SQL เพื่ออัปเดตสถานะการติดตาม
    $sql = "UPDATE monitor_information 
            SET monitor_status = ?, monitor_deadline = ?, monitor_date = ?, monitor_round = monitor_round + 1
            WHERE patient_id IN ($placeholders)";
    
    $stmt = $pdo->prepare($sql);
    
    // ผูกค่าให้กับ placeholders
    $params = array_merge([$monitorStatus, $monitorDeadline, $monitorStartDate], $patientIds);
    
    if ($stmt->execute($params)) {
        echo json_encode(['status' => 'success', 'updated_count' => $stmt->rowCount()]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Error executing update: ' . $stmt->errorInfo()[2]]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No matching records found for update.']);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$pdo = null;
?>
=======
// แสดงข้อมูลที่ได้รับ
error_log('Received data: ' . print_r($data, true));

// ตรวจสอบ monitorStatus และ patientIds ว่ามีการส่งมาหรือไม่
$monitorStatus = isset($data['monitorStatus']) ? $data['monitorStatus'] : '';
$patientIds = isset($data['patientIds']) ? $data['patientIds'] : [];
$monitorDeadline = isset($data['monitorDeadline']) ? $data['monitorDeadline'] : ''; // รับวันหมดอายุ

// ตรวจสอบว่าค่า monitorStatus มีการส่งมาหรือไม่
if (empty($monitorStatus)) {
    echo json_encode(['status' => 'error', 'message' => 'No monitor status provided']);
    exit;
}

// ตรวจสอบว่า patientIds เป็นอาร์เรย์และมีค่าไม่ว่าง
if (!is_array($patientIds) || empty($patientIds)) {
    echo json_encode(['status' => 'error', 'message' => 'No patient IDs provided or patient IDs are not an array']);
    exit;
}

// ตรวจสอบ ID ที่ส่งมา
error_log('Patient IDs for update: ' . implode(',', $patientIds));

// เตรียมคำสั่ง SQL เพื่ออัปเดตสถานะการติดตาม
$placeholders = implode(',', array_fill(0, count($patientIds), '?'));
$sql = "
    UPDATE monitor_information 
    SET monitor_status = ?, monitor_deadline = ?, monitor_round = monitor_round + 1
    WHERE patient_id IN ($placeholders)
";

try {
    $stmt = $conn->prepare($sql);
    if ($stmt === false) {
        throw new Exception("Failed to prepare SQL statement: " . $conn->error);
    }

    // ผูกค่าให้กับ placeholders
    $types = 'ss' . str_repeat('i', count($patientIds));
    $stmt->bind_param($types, $monitorStatus, $monitorDeadline, ...$patientIds);

    // ตรวจสอบก่อนการอัปเดตว่า ID มีอยู่ในฐานข้อมูลหรือไม่
    $sql_check = "SELECT * FROM monitor_information WHERE patient_id IN ($placeholders)";
    $stmt_check = $conn->prepare($sql_check);
    $stmt_check->bind_param(str_repeat('i', count($patientIds)), ...$patientIds);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    error_log('Matching records found: ' . $result_check->num_rows);

    if ($result_check->num_rows > 0) {
        // ทำการอัปเดต
        $stmt->execute();
        if ($stmt->error) {
            throw new Exception("SQL error: " . $stmt->error);
        }
        echo json_encode(['status' => 'success', 'updated_count' => $stmt->affected_rows]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'No matching records found for update.']);
    }

    $stmt_check->close(); // ปิด statement ตรวจสอบ
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}

// ปิดการเชื่อมต่อฐานข้อมูล
$conn->close();
?>
>>>>>>> 66d56798c43af83ae6c72c3a51642cd333ec7b1d
