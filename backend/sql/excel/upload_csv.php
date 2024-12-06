<?php

header('Content-Type: application/json');

// เชื่อมต่อกับฐานข้อมูล
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "chiracare_follow_up_db"; // ชื่อฐานข้อมูลที่ใช้

$conn = new mysqli($servername, $username, $password, $dbname);

// ตรวจสอบการเชื่อมต่อ
if ($conn->connect_error) {
    echo json_encode(["error" => "Connection failed: " . $conn->connect_error]);
    exit;
}

if (isset($_FILES['csvFile']) && $_FILES['csvFile']['error'] == 0) {
    $fileName = $_FILES['csvFile']['name'];
    $fileTmpName = $_FILES['csvFile']['tmp_name'];
    $fileType = $_FILES['csvFile']['type'];

    // ตรวจสอบว่าเป็นไฟล์ CSV หรือไม่
    if ($fileType == 'text/csv' || $fileType == 'application/vnd.ms-excel') {
        if (($handle = fopen($fileTmpName, 'r')) !== false) {
            // ข้ามหัวตาราง
            $headers = fgetcsv($handle, 1000, ',');

            // อ่านข้อมูลจากไฟล์ CSV
            while (($data = fgetcsv($handle, 1000, ',')) !== false) {
                // ข้อมูลที่ได้จากไฟล์ CSV
                $patient_id = mysqli_real_escape_string($conn, $data[0]); // เพิ่ม patient_id
                $rank = mysqli_real_escape_string($conn, $data[1]);
                $department = mysqli_real_escape_string($conn, $data[2]);
                $full_name = mysqli_real_escape_string($conn, $data[3]);
                $id_card = mysqli_real_escape_string($conn, $data[4]);
                $birth_date_original = $data[5]; // วันที่จาก CSV เช่น "29/4/2016"
                $date_obj = DateTime::createFromFormat('d/m/Y', $birth_date_original);
                $birth_date = $date_obj ? $date_obj->format('Y-m-d') : null;
                
                // ตรวจสอบว่า `$birth_date` มีค่าหรือไม่
                if (!$birth_date) {
                    echo json_encode(["error" => "รูปแบบวันที่ไม่ถูกต้องสำหรับ patient_id: $patient_id"]);
                    exit;
                }                
                $marital_status = mysqli_real_escape_string($conn, $data[6]);
                $phone_number = mysqli_real_escape_string($conn, $data[7]);
                $emergency_phone = mysqli_real_escape_string($conn, $data[8]);
                $postal_code = mysqli_real_escape_string($conn, $data[9]);
                $province = mysqli_real_escape_string($conn, $data[10]);
                $amphur = mysqli_real_escape_string($conn, $data[11]);
                $tambon = mysqli_real_escape_string($conn, $data[12]);
                $soi = mysqli_real_escape_string($conn, $data[13]);
                $moo = mysqli_real_escape_string($conn, $data[14]);
                $number = mysqli_real_escape_string($conn, $data[15]);
                $area = mysqli_real_escape_string($conn, $data[16]);
                $patient_group = mysqli_real_escape_string($conn, $data[17]);
                $patient_type = mysqli_real_escape_string($conn, $data[18]);
                $disease_type = mysqli_real_escape_string($conn, $data[19]);
                $current_status = mysqli_real_escape_string($conn, $data[20]);
                $note = mysqli_real_escape_string($conn, $data[21]);

                // SQL สำหรับแทรกข้อมูลลงในตาราง patient_information
                $sql_patient_information = "INSERT INTO patient_information (patient_id, rank, department, full_name, id_card, birth_date, marital_status, phone_number, emergency_phone, current_status)
                                             VALUES ('$patient_id', '$rank', '$department', '$full_name', '$id_card', '$birth_date', '$marital_status', '$phone_number', '$emergency_phone', '$current_status')";
                if (!mysqli_query($conn, $sql_patient_information)) {
                    echo json_encode(["error" => "Error inserting patient information: " . mysqli_error($conn)]);
                    exit;
                }

                // SQL สำหรับแทรกข้อมูลลงในตาราง patient_address
                $sql_patient_address = "INSERT INTO patient_address (patient_id, postal_code, province, amphur, tambon, soi, moo, number, area)
                                        VALUES ('$patient_id', '$postal_code', '$province', '$amphur', '$tambon', '$soi', '$moo', '$number', '$area')";
                if (!mysqli_query($conn, $sql_patient_address)) {
                    echo json_encode(["error" => "Error inserting patient address: " . mysqli_error($conn)]);
                    exit;
                }

                // SQL สำหรับแทรกข้อมูลลงในตาราง patient_medical_information
                $sql_patient_medical_information = "INSERT INTO patient_medical_information (patient_id, patient_group, patient_type, disease_type, note)
                                                    VALUES ('$patient_id', '$patient_group', '$patient_type', '$disease_type', '$note')";
                if (!mysqli_query($conn, $sql_patient_medical_information)) {
                    echo json_encode(["error" => "Error inserting medical information: " . mysqli_error($conn)]);
                    exit;
                }
                
                // หลังจากแทรกข้อมูลในตาราง patient_medical_information
$sql_monitor_information = "INSERT INTO monitor_information (patient_id, monitor_round) VALUES ('$patient_id', 0)";
if (!mysqli_query($conn, $sql_monitor_information)) {
    echo json_encode(["error" => "Error inserting monitor information: " . mysqli_error($conn)]);
    exit;
}

$sql_treatment_information = "INSERT INTO treatment_information (patient_id, treatment_round) VALUES ('$patient_id', 0)";
if (!mysqli_query($conn, $sql_treatment_information)) {
    echo json_encode(["error" => "Error inserting treatment information: " . mysqli_error($conn)]);
    exit;
}

            }

            fclose($handle);
            echo json_encode(["status" => "success", "message" => "ข้อมูลทั้งหมดได้ถูกอัปโหลดและบันทึกเรียบร้อยแล้ว"]);
        }
    } else {
        echo json_encode(["error" => "กรุณาอัปโหลดไฟล์ CSV เท่านั้น"]);
    }
} else {
    echo json_encode(["error" => "กรุณาเลือกไฟล์ CSV ที่ต้องการอัปโหลด"]);
}
?>