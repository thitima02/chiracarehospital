<?php
// Connect to your database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_patient_data"; // Use your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch patient type statistics
$sql = "SELECT patient_type, COUNT(*) AS total FROM patients GROUP BY patient_type";
$result = $conn->query($sql);

$data = array();

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $data[] = $row;
    }
}

// Send the data as JSON
echo json_encode($data);

$conn->close();
?>
