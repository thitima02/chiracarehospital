<?php
// Database connection details
$servername = "localhost";
$username = "root"; 
$password = ""; 
$dbname = "chiracare_follow_up_db"; 

// Create connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// SQL query to fetch untreated patients
$sql = "SELECT 
    p.full_name AS patient_name,
    p.patient_image,
    p.patient_id,
    t.appointment_date,
    pm.patient_type,
    pm.disease_type,
    t.treatment_round,
    t.treatment_status,
    m.monitor_status AS follow_up_status
FROM 
    treatment_information t
JOIN 
    patient_information p ON t.patient_id = p.patient_id
LEFT JOIN 
    patient_medical_information pm ON t.patient_id = pm.patient_id
LEFT JOIN 
    monitor_information m ON t.patient_id = m.patient_id
WHERE t.treatment_status = 'ไม่มาตามนัด'"; // Treated patients status

$result = $conn->query($sql);

// Prepare response data
$patients = array();
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $patients[] = $row;
    }
    echo json_encode(['patients' => $patients]); 
} else {
    echo json_encode(['patients' => []]); 
}

$conn->close();
?>
