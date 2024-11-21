<?php
include 'db_connection.php';

$data = json_decode(file_get_contents("php://input"), true);
$patient_id = $data['patient_id'];
$monitor_status = $data['monitor_status'];

if ($patient_id && $monitor_status) {
    try {
        $stmt = $conn->prepare("
            UPDATE monitor_information 
            SET monitor_status = :monitor_status 
            WHERE patient_id = :patient_id
        ");
        $stmt->bindParam(':monitor_status', $monitor_status);
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        echo json_encode(['status' => 'success', 'message' => 'Monitor status updated successfully.']);
    } catch (PDOException $e) {
        echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Invalid input.']);
}

$conn = null;
?>
