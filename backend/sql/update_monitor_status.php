<?php
// Include the database connection using PDO
include 'db_connection.php';

// Get raw POST data
$data = json_decode(file_get_contents("php://input"));

// Check if data exists
if ($data) {
    $patient_id = $data->patient_id;
    $monitor_status = $data->monitor_status;

    if ($patient_id && $monitor_status) {
        try {
            // Prepare the update query
            $stmt = $conn->prepare("UPDATE monitor_information SET monitor_status = :monitor_status WHERE patient_id = :patient_id");

            // Bind the parameters
            $stmt->bindParam(':monitor_status', $monitor_status);
            $stmt->bindParam(':patient_id', $patient_id);

            // Execute the query
            $stmt->execute();

            // Return success response
            echo json_encode(['status' => 'success']);
        } catch (PDOException $e) {
            // Return error response
            echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
        }
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Invalid input']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'No data received']);
}

// Close the connection
$conn = null;
?>
