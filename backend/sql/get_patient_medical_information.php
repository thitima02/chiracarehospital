<?php
// Include database connection
include 'db_connection.php';

try {
    // SQL query to fetch data
    $stmt = $conn->prepare("SELECT patient_group, patient_type, disease_type FROM patient_medical_information");
    $stmt->execute();

    // Fetch data as associative array
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Count disease types
    $totals = [
        'hypertension' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
        'obesity' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
        'cholesterol' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
        'diabetes' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
        'cvd' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
        'kidney' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0]
    ];

    // Loop through the data and count each disease type based on patient_type
    foreach ($data as $row) {
        $disease_types = explode(", ", $row['disease_type']); // Handle multiple diseases
        foreach ($disease_types as $disease) {
            if (strpos($disease, 'ความดันโลหิตสูง') !== false) {
                $totals['hypertension'][$row['patient_type']]++;
            }
            if (strpos($disease, 'โรคอ้วน') !== false) {
                $totals['obesity'][$row['patient_type']]++;
            }
            if (strpos($disease, 'ไขมันในเลือดสูง') !== false) {
                $totals['cholesterol'][$row['patient_type']]++;
            }
            if (strpos($disease, 'โรคเบาหวาน') !== false) {
                $totals['diabetes'][$row['patient_type']]++;
            }
            if (strpos($disease, 'CVD') !== false || strpos($disease, 'หลอดเลือด') !== false) {
                $totals['cvd'][$row['patient_type']]++;
            }
            if (strpos($disease, 'โรคไต') !== false) {
                $totals['kidney'][$row['patient_type']]++;
            }
        }
    }

    // Return totals as JSON
    header('Content-Type: application/json');
    echo json_encode($totals);

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
