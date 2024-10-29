<?php
// Include database connection
include 'db_connection.php';

try {
    // ตรวจสอบว่ามี patient_id ใน request หรือไม่
    if (isset($_GET['patient_id'])) {
        $patient_id = $_GET['patient_id'];

        // SQL query เพื่อดึงข้อมูลทางการแพทย์ตาม patient_id
        $stmt = $conn->prepare("SELECT patient_id, patient_group, patient_type, disease_type, date_create 
                                 FROM patient_medical_information WHERE patient_id = :patient_id");
        $stmt->bindParam(':patient_id', $patient_id);
        $stmt->execute();

        // Fetch data as associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Return data as JSON
        header('Content-Type: application/json');
        echo json_encode($data);
    } else {
        // ถ้าไม่มี patient_id ให้ดึงข้อมูลทั้งหมดตามปี
        $stmt = $conn->prepare("
            SELECT patient_id, patient_group, patient_type, disease_type, date_create 
            FROM patient_medical_information
        ");
        $stmt->execute();

        // Fetch data as associative array
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

        // Initialize array to store totals by year
        $yearly_totals = [];

        // Loop through the data and count each disease type based on patient_type and year
        foreach ($data as $row) {
            $year = date('Y', strtotime($row['date_create'])); // Extract year from date_create

            if (!isset($yearly_totals[$year])) {
                // Initialize the disease counts for the year if not set
                $yearly_totals[$year] = [
                    'hypertension' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'obesity' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'cholesterol' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'diabetes' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'cvd' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'kidney' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0, 'กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'patients' => [], // Store patient IDs for the year
                    'group_totals' => ['กลุ่มผู้ป่วย' => 0, 'กลุ่มเสี่ยง' => 0, 'กลุ่มสงสัยป่วย' => 0],
                    'group_totals_patient' => ['ประชาชน' => 0, 'ครอบครัว' => 0, 'กำลังพล' => 0]
                ];
            }

            $disease_types = explode(", ", $row['disease_type']); // Handle multiple diseases
            
            // Count for patient groups
            if ($row['patient_group'] === 'กลุ่มผู้ป่วย') {
                $yearly_totals[$year]['group_totals']['กลุ่มผู้ป่วย']++;
            } elseif ($row['patient_group'] === 'กลุ่มเสี่ยง') {
                $yearly_totals[$year]['group_totals']['กลุ่มเสี่ยง']++;
            } elseif ($row['patient_group'] === 'กลุ่มสงสัยป่วย') {
                $yearly_totals[$year]['group_totals']['กลุ่มสงสัยป่วย']++;
            }

            // Count total patients without separating by disease
            $yearly_totals[$year]['group_totals_patient'][$row['patient_type']]++;

            // Store patient ID in the appropriate year
            $yearly_totals[$year]['patients'][] = $row['patient_id'];

            foreach ($disease_types as $disease) {
                if (strpos($disease, 'ความดันโลหิตสูง') !== false) {
                    $yearly_totals[$year]['hypertension'][$row['patient_type']]++;
                }
                if (strpos($disease, 'โรคอ้วน') !== false) {
                    $yearly_totals[$year]['obesity'][$row['patient_type']]++;
                }
                if (strpos($disease, 'ไขมันในเลือดสูง') !== false) {
                    $yearly_totals[$year]['cholesterol'][$row['patient_type']]++;
                }
                if (strpos($disease, 'โรคเบาหวาน') !== false) {
                    $yearly_totals[$year]['diabetes'][$row['patient_type']]++;
                }
                if (strpos($disease, 'CVD') !== false || strpos($disease, 'หลอดเลือด') !== false) {
                    $yearly_totals[$year]['cvd'][$row['patient_type']]++;
                }
                if (strpos($disease, 'โรคไต') !== false) {
                    $yearly_totals[$year]['kidney'][$row['patient_type']]++;
                }
            }
        }

        // Return totals as JSON, including the year and patient IDs
        header('Content-Type: application/json');
        echo json_encode($yearly_totals);
    }
} catch (PDOException $e) {
    echo json_encode(['error' => $e->getMessage()]);
}
?>
