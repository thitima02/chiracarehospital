<?php 
// Include the database connection file
include 'db_connection.php';

try {
    // SQL query to fetch treatment information, sum treatment_round, get latest last_update, and patient_id
    $stmt = $conn->prepare("
        SELECT 
            patient_id,
            treatment_status,
            SUM(treatment_round) AS total_treatment_round,
            MAX(last_update) AS last_update, 
            id_patient_information, 
            id_user_info, 
            id_treatment_form, 
            id_monitor_information, 
            is_cure_done
        FROM treatment_information
        GROUP BY patient_id, treatment_status, id_patient_information, id_user_info, id_treatment_form, id_monitor_information, is_cure_done
    ");
    $stmt->execute();

    // Fetch all data
    $treatmentInformation = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Reindex the result array to a numerically indexed array
    $result = array_values($treatmentInformation);

    // Set header to application/json
    header('Content-Type: application/json');

    // Output the JSON encoded data
    echo json_encode($result);

} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(['error' => $e->getMessage()]);
}
?>
