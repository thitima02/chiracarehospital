<?php
include 'db_connection.php'; // Database connection

header('Content-Type: application/json');

// Get JSON input data
$data = json_decode(file_get_contents("php://input"), true);

// Validate required fields
if (empty($data['patient_id']) || empty($data['monitor_status']) || empty($data['treatment_status'])) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Missing required fields: patient_id, monitor_status, or treatment_status.'
    ]);
    exit;
}

// Extract input data
$patient_id = $data['patient_id'];
$monitor_status = $data['monitor_status'];
$treatment_status = $data['treatment_status'];
$monitor_date = !empty($data['monitor_date']) && $data['monitor_date'] !== "ยังไม่มี" ? $data['monitor_date'] : null;
$monitor_deadline = !empty($data['monitor_deadline']) && $data['monitor_deadline'] !== "ยังไม่มี" ? $data['monitor_deadline'] : null;
$monitor_round = $data['monitor_round'] ?? null;
$appointment_date = !empty($data['appointment_date']) && $data['appointment_date'] !== "ยังไม่มี" ? $data['appointment_date'] : null;

try {
    $conn->beginTransaction(); // Start transaction

    // Update monitor information
    $monitor_stmt = $conn->prepare("
        UPDATE monitor_information 
        SET 
            monitor_status = :monitor_status, 
            monitor_date = :monitor_date, 
            monitor_deadline = :monitor_deadline, 
            monitor_round = :monitor_round 
        WHERE patient_id = :patient_id
    ");
    $monitor_stmt->execute([
        ':monitor_status' => $monitor_status,
        ':monitor_date' => $monitor_date,
        ':monitor_deadline' => $monitor_deadline,
        ':monitor_round' => $monitor_round,
        ':patient_id' => $patient_id
    ]);

    // Check if patient exists in treatment_information
    $check_stmt = $conn->prepare("
        SELECT id 
        FROM treatment_information 
        WHERE patient_id = :patient_id
    ");
    $check_stmt->execute([':patient_id' => $patient_id]);

    if ($check_stmt->rowCount() === 0) {
        throw new Exception('Patient not found in treatment_information.');
    }

    // Update treatment information
    $treatment_stmt = $conn->prepare("
        UPDATE treatment_information 
        SET 
            treatment_status = :treatment_status, 
            appointment_date = :appointment_date, 
            last_update = NOW() 
        WHERE patient_id = :patient_id
    ");
    $treatment_stmt->execute([
        ':treatment_status' => $treatment_status,
        ':appointment_date' => $appointment_date,
        ':patient_id' => $patient_id
    ]);

    $conn->commit(); // Commit transaction

    echo json_encode([
        'status' => 'success',
        'message' => 'Monitor and treatment statuses updated successfully.'
    ]);
} catch (Exception $e) {
    $conn->rollBack(); // Rollback changes on error
    echo json_encode([
        'status' => 'error',
        'message' => 'Error: ' . $e->getMessage()
    ]);
} finally {
    $conn = null; // Close connection
}
?>