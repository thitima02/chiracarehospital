<?php
header('Content-Type: application/json');

// Database connection
include 'db_connection.php'; // Ensure this file contains your PDO database connection logic

// Error handling for database connection
if (!$conn) {
    http_response_code(500); // Internal Server Error
    echo json_encode(array('status' => 'error', 'message' => 'Database connection failed.'));
    exit();
}

try {
    // SQL query to fetch data from the monitor_form table
    $sql = "
        SELECT 
            id,
            patient_id,
            user_fullname,  -- Added user_fullname field
            blood_sugar_level,
            general_symptoms,
            vital_signs,
            reason_for_missed_treatment,
            form_submission_date,
            newupdate,  -- Added newupdate field
            COUNT(reason_for_missed_treatment) AS reason_count
        FROM 
            monitor_form
        GROUP BY 
            reason_for_missed_treatment
        ORDER BY 
            reason_count DESC
    ";

    $stmt = $conn->prepare($sql);
    $stmt->execute();

    // Fetch data as an associative array
    $monitor_data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Check if there are results
    if (count($monitor_data) > 0) {
        // Return data in JSON format with success status
        echo json_encode(array('status' => 'success', 'data' => $monitor_data));
    } else {
        // No results found
        http_response_code(404); // Not Found
        echo json_encode(array('status' => 'error', 'message' => 'No data found.'));
    }
} catch (Exception $e) {
    // Return error in case of failure
    http_response_code(500); // Internal Server Error
    echo json_encode(array('status' => 'error', 'message' => $e->getMessage()));
}

// Close the database connection (set PDO object to null)
$conn = null;
?>
