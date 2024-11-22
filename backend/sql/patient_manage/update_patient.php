<?php
include('../db_connect.php');

$data = json_decode(file_get_contents("php://input"), true);
$patient_id = $data['patient_id'] ?? null;

if ($patient_id) {
    $name = $data['name'];
    $id_number = $data['id_number'];
    $age = $data['age'];
    $birthdate = $data['birthdate'];
    $phone = $data['phone'];
    $emergency_phone = $data['emergency_phone'];

    $query = "UPDATE patient_information SET name='$name', id_number='$id_number', age='$age', birthdate='$birthdate', phone='$phone', emergency_phone='$emergency_phone' WHERE patient_id='$patient_id'";
    
    if (mysqli_query($conn, $query)) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => 'Failed to update patient data.']);
    }
} else {
    echo json_encode(['error' => 'Invalid patient ID.']);
}

mysqli_close($conn);
?>
