<?php
// Include the database connection file
include 'db_connection.php';

try {
    // SQL query to fetch treatment information with treatment_status grouping
    $stmt = $conn->prepare("
        SELECT 
            id, 
            patient_id, 
            appointment_date, 
            treatment_round, 
            treatment_status, 
            last_update, 
            id_patient_information, 
            id_user_info, 
            id_treatment_form, 
            id_monitor_information, 
            is_cure_done,
            COUNT(*) as status_count
        FROM 
            treatment_information
        GROUP BY 
            treatment_status
    ");
    $stmt->execute();

    // Fetch all data
    $treatmentInformation = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Set header to application/json
    header('Content-Type: application/json');

    // Output the JSON encoded data
    echo json_encode($treatmentInformation);

} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>
