<?php 
// Include the database connection using PDO
include 'db_connection.php';

try {
    // Prepare the SQL query to fetch additional fields along with aggregated counts for each monitor status by patient_id
    $stmt = $conn->prepare("
        SELECT 
            id,
            patient_id,
            monitor_date,
            monitor_round,
            monitor_status,
            MAX(monitor_date) AS last_update,
            id_patient_information,
            id_user_info,
            id_monitor_form,
            id_treatment_information,
            COUNT(CASE WHEN monitor_status = 'ติดตามแล้ว' THEN 1 END) AS followed_count,
            COUNT(CASE WHEN monitor_status = 'กำลังติดตาม' THEN 1 END) AS following_count,
            COUNT(CASE WHEN monitor_status = 'ยังไม่ได้ติดตาม' THEN 1 END) AS not_followed_count
        FROM monitor_information
        GROUP BY 
            id, 
            patient_id, 
            monitor_round,
            monitor_status,
            id_patient_information,
            id_user_info,
            id_monitor_form,
            id_treatment_information
    ");
    
    // Execute the SQL query
    $stmt->execute();

    // Fetch all data as an associative array
    $monitor_information = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if data exists
    if (count($monitor_information) > 0) {
        // Set content type to JSON
        header('Content-Type: application/json');
        // Output the data in JSON format
        echo json_encode($monitor_information);
    } else {
        // If no data found, return a message
        header('Content-Type: application/json');
        echo json_encode(array("message" => "No data found."));
    }
    
} catch (PDOException $e) {
    // Handle any errors
    header('Content-Type: application/json');
    echo json_encode(array("error" => $e->getMessage()));
}

// Close the connection
$conn = null;
?>
