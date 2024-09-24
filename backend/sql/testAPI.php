<?php
header('Content-Type: application/json'); // กำหนดให้ response เป็น JSON

$response = [
    'status' => 'success',
    'message' => 'API กำลังทำงาน'
];

// ส่ง response กลับเป็น JSON
echo json_encode($response);
