<?php
// ตัวอย่างการรับข้อมูล JSON จากฟอร์ม
$data = json_decode(file_get_contents('php://input'), true);

$patient_id = $data['patient_id'];
$monitor_status = $data['monitor_status'];
$disease_type = $data['disease_type'];
$vital_signs = $data['vital_signs'];
$blood_sugar = $data['blood_sugar'];
$treatment_refusal_reason = $data['treatment_refusal_reason'];
$general_symptoms = $data['general_symptoms'];
$treatment_issues = $data['treatment_issues'];

// คำสั่ง SQL ในการอัปเดตข้อมูลในฐานข้อมูล
$query = "UPDATE patient_follow_up SET 
            monitor_status = '$monitor_status', 
            disease_type = '$disease_type',
            vital_signs = '$vital_signs',
            blood_sugar = '$blood_sugar',
            treatment_refusal_reason = '$treatment_refusal_reason',
            general_symptoms = '$general_symptoms',
            treatment_issues = '$treatment_issues'
          WHERE patient_id = '$patient_id'";

// ดำเนินการคำสั่ง SQL
if (mysqli_query($connection, $query)) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => mysqli_error($connection)]);
}
?>
