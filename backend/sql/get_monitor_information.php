<?php
// Include the database connection using PDO
include 'db_connection.php';

try {
    // Prepare the SQL query to select the required fields from monitor_information
    $stmt = $conn->prepare("SELECT monitor_date, monitor_round, monitor_status, last_update, id_patient_information, id_user_info, id_monitor_form, id_treatment_information FROM monitor_information");
    
    // Execute the query
    $stmt->execute();

    // Fetch all the rows as an associative array
    $monitor_information = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if any data was returned
    if (count($monitor_information) > 0) {
        // Return the data as JSON
        echo json_encode($monitor_information);
    } else {
        // No data found message
        echo json_encode(array("message" => "No data found."));
    }
    
} catch (PDOException $e) {
    // Handle any errors
    echo json_encode(array("error" => $e->getMessage()));
}

// Close the connection
$conn = null;
?>
