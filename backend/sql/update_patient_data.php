<?php
// Assuming you have a connection to your database
include_once('db_connection.php');

// Get POST data
$data = json_decode(file_get_contents('php://input'), true);

// Prepare SQL statement to update patient data
if ($data) {
    $patient_id = $data['patient_id'];
    $disease_type = $data['disease_type'];
    $missing_appointments = $data['missing_appointments'];
    $general_condition = $data['general_condition'];
    $vital_signs = $data['vital_signs'];
    $blood_sugar_level = $data['blood_sugar_level'];
    $responsible_staff_follow_up = $data['responsible_staff_follow_up'];
    $treatment_date = $data['treatment_date'];
    $responsible_staff_treatment = $data['responsible_staff_treatment'];
    $treatment_notes = $data['treatment_notes'];

    // SQL Query
    $sql = "UPDATE patient_data SET disease_type = ?, missing_appointments = ?, general_condition = ?, 
            vital_signs = ?, blood_sugar_level = ?, responsible_staff_follow_up = ?, treatment_date = ?, 
            responsible_staff_treatment = ?, treatment_notes = ? WHERE patient_id = ?";

    if ($stmt = $conn->prepare($sql)) {
        $stmt->bind_param("sssssssssi", $disease_type, $missing_appointments, $general_condition, $vital_signs, 
                          $blood_sugar_level, $responsible_staff_follow_up, $treatment_date, 
                          $responsible_staff_treatment, $treatment_notes, $patient_id);

        if ($stmt->execute()) {
            echo json_encode(['status' => 'success']);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'ไม่สามารถอัปเดตข้อมูลได้']);
        }
        $stmt->close();
    } else {
        echo json_encode(['status' => 'error', 'message' => 'SQL เตรียมคำสั่งล้มเหลว']);
    }
    $conn->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'ข้อมูลไม่ถูกต้อง']);
}
?>
