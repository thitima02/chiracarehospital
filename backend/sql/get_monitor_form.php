<?php
header('Content-Type: application/json');

// Database connection
include 'db_connection.php'; // Ensure this file contains your database connection logic

// SQL query to fetch data from the monitor_form table
$sql = "SELECT * FROM monitor_form";
$result = $conn->query($sql);

// Check if there are results and fetch them
if ($result->num_rows > 0) {
    $monitor_data = array();

    while($row = $result->fetch_assoc()) {
        $monitor_data[] = $row;
    }

    // Return data in JSON format
    echo json_encode(array('status' => 'success', 'data' => $monitor_data));
} else {
    // No results found
    echo json_encode(array('status' => 'error', 'message' => 'No data found.'));
}

// Close the database connection
$conn->close();
?>
