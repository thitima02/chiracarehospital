<?php
// Include database connection
include 'db_connection.php';

try {
    // Check if patient_id is set
    if (isset($_GET['patient_id'])) {
        $patient_id = $_GET['patient_id'];
        $stmt = $conn->prepare("SELECT id, patient_id, patient_group, patient_type, disease_type, date_create 
                                 FROM patient_medical_information WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id);
    } else {
        // If no patient_id is set, get all records
        $stmt = $conn->prepare("SELECT id, patient_id, patient_group, patient_type, disease_type, date_create 
                                FROM patient_medical_information");
    }
    $stmt->execute();
    $patientData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Second query to calculate yearly totals
    $stmt = $conn->prepare("SELECT patient_group, patient_type, disease_type, YEAR(date_create) AS year, patient_id 
                            FROM patient_medical_information");
    $stmt->execute();
    $yearlyData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $yearly_totals = [];
    foreach ($yearlyData as $row) {
        $year = $row['year'];
        if (!isset($yearly_totals[$year])) {
            $yearly_totals[$year] = [
                'hypertension' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'obesity' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'cholesterol' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'diabetes' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'cvd' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'kidney' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0],
                'group_totals' => ['กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                'group_totals_patient' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0]
            ];
        }

        $disease_types = explode(", ", $row['disease_type']);
        
        // Counting patient groups
        if ($row['patient_group'] === 'กลุ่มผู้ป่วย') {
            $yearly_totals[$year]['group_totals']['กลุ่มผู้ป่วย']++;
        } elseif ($row['patient_group'] === 'กลุ่มเสี่ยง') {
            $yearly_totals[$year]['group_totals']['กลุ่มเสี่ยง']++;
        } elseif ($row['patient_group'] === 'กลุ่มสงสัยป่วย') {
            $yearly_totals[$year]['group_totals']['กลุ่มสงสัยป่วย']++;
        }

        // Counting patient types
        $yearly_totals[$year]['group_totals_patient'][$row['patient_type']]++;

        // Counting disease types
        foreach ($disease_types as $disease) {
            if (strpos($disease, 'ความดันโลหิตสูง') !== false) $yearly_totals[$year]['hypertension'][$row['patient_type']]++;
            if (strpos($disease, 'โรคอ้วน') !== false) $yearly_totals[$year]['obesity'][$row['patient_type']]++;
            if (strpos($disease, 'ไขมันในเลือดสูง') !== false) $yearly_totals[$year]['cholesterol'][$row['patient_type']]++;
            if (strpos($disease, 'โรคเบาหวาน') !== false) $yearly_totals[$year]['diabetes'][$row['patient_type']]++;
            if (strpos($disease, 'CVD') !== false || strpos($disease, 'หลอดเลือด') !== false) $yearly_totals[$year]['cvd'][$row['patient_type']]++;
            if (strpos($disease, 'โรคไต') !== false) $yearly_totals[$year]['kidney'][$row['patient_type']]++;
        }
    }

    // Output the patient medical information and yearly totals
    header('Content-Type: application/json');
    echo json_encode(['patientData' => $patientData, 'yearlyTotals' => $yearly_totals]);

} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
